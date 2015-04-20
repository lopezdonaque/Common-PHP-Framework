<?php

namespace Common\Database;


/**
 * This class is used to create databases.
 * It uses system commands like: createdb, createlang, psql, etc.
 *
 */
class PostgreSqlDatabaseCreator
{

  /**
   * Config
   *
   * @var PostgreSqlDatabaseCreatorConfig
   */
  public $config;



  /**
   * Constructor
   *
   * @param array|PostgreSqlDatabaseCreatorConfig $config
   */
  public function __construct( $config = array() )
  {
    $this->config = is_array( $config ) ? new PostgreSqlDatabaseCreatorConfig( $config ) : $config;
  }



  /**
   * Executes the creation of the database
   *
   * @return bool
   */
  public function exec()
  {
    if( $this->config->dropdb && $this->exists_database() )
    {
      $this->_execute_db_command( 'dropdb', $this->config->dbname );
    }

    // Execute create database
    $this->_createdb();

    // Install procedural languages
    $this->_create_langs();

    // Execute SQL scripts
    $this->_execute_sql_scripts();

    // Execute fixtures
    $this->_execute_fixtures();

    return true;
  }



  /**
   * Executes the createdb command to create database
   */
  private function _createdb()
  {
    $arguments = '';

    if( $this->config->host )
    {
      $arguments .= '-h ' . $this->config->host;
    }

    if( $this->config->port )
    {
      $arguments .= ' -p ' . $this->config->port;
    }

    if( $this->config->user )
    {
      $arguments .= ' -U ' . $this->config->user;
    }

    if( $this->config->owner )
    {
      $arguments .= ' -O ' . $this->config->owner;
    }

    if( $this->config->tablespace )
    {
      $arguments .= ' -D ' . $this->config->tablespace;
    }

    $arguments .= " -E utf8 -T " . $this->config->template . " " . $this->config->dbname . " 2>&1";

    $this->_execute_db_command( 'createdb', $arguments );
  }



  /**
   * Install procedural languages on database
   */
  private function _create_langs()
  {
    foreach( $this->config->procedural_languages as $lang )
    {
      $this->_execute_createlang_command( $lang );
    }
  }



  /**
   * Executes the createlang command to install procedural language on database
   *
   * @param string $lang
   */
  private function _execute_createlang_command( $lang )
  {
    $arguments = '';

    if( $this->config->host )
    {
      $arguments .= '-h ' . $this->config->host;
    }

    if( $this->config->port )
    {
      $arguments .= " -p " . $this->config->port;
    }

    if( $this->config->user )
    {
      $arguments .= " -U " . $this->config->user;
    }

    $arguments .= " $lang " . $this->config->dbname . " 2>&1";

    $this->_execute_db_command( 'createlang', $arguments );
  }



  /**
   * Executes the SQL scripts
   */
  private function _execute_sql_scripts()
  {
    foreach( $this->config->sql_scripts as $script )
    {
      $this->_execute_sql_script( $script );
    }
  }



  /**
   * Executes the fixtures
   */
  private function _execute_fixtures()
  {
    foreach( $this->config->fixtures as $file )
    {
      $method = ( pathinfo( $file, PATHINFO_EXTENSION ) == 'sql' ) ? '_execute_sql_script' : '_add_data';
      $this->$method( $file );
    }
  }



  /**
   * Executes a SQL script
   *
   * @param string $script
   * @throws \Common\Database\PostgreSqlDatabaseCreatorException
   */
  private function _execute_sql_script( $script )
  {
    // Check if it's SQL file or code
    $is_sql_file = ( substr( $script, -4, 4 ) == '.sql' );

    // Check if it's a filename and exists
    if( $is_sql_file && !file_exists( $script ) )
    {
      throw new PostgreSqlDatabaseCreatorException( "SQL script not found [$script]" );
    }

    $arguments = '';

    if( $this->config->host )
    {
      $arguments .= '-h ' . $this->config->host;
    }

    if( $this->config->port )
    {
      $arguments .= " -p " . $this->config->port;
    }

    if( $this->config->user )
    {
      $arguments .= " -U " . $this->config->user;
    }

    $arguments .= " -d " . $this->config->dbname;

    if( $is_sql_file )
    {
      $arguments .= " -f " . $script;
    }
    else
    {
      $arguments .= " -c " . $script;
    }

    $arguments .= " 2>&1";

    // Set the script path as current path to allow execute other scripts from the master db_script ("\i other.sql")
    if( $is_sql_file )
    {
      $oldcwd = getcwd();
      chdir( dirname( $script ) );
      $this->_execute_db_command( 'psql', $arguments );
      chdir( $oldcwd );
    }
    else
    {
      $this->_execute_db_command( 'psql', $arguments );
    }
  }



  /**
   * Returns if database exists
   *
   * @return bool
   */
  public function exists_database()
  {
    $num_ddbb = shell_exec( '/usr/bin/psql -h ' . $this->config->host . ' -l | grep -w ' . $this->config->dbname . ' | wc -l' );
    $num_ddbb = (int) trim( $num_ddbb );
    return ( $num_ddbb > 0 );
  }



  /**
   * Adds fixtures data
   *
   * @param string $file
   * @throws \Common\Database\PostgreSqlDatabaseCreatorException
   */
  private function _add_data( $file )
  {
    if( !file_exists( $file ) )
    {
      throw new PostgreSqlDatabaseCreatorException( 'Fixtures file not found: ' . $file );
    }

    $json = json_decode( file_get_contents( $file ) );

    try
    {
      $pdo = new \PDO( "pgsql:dbname={$this->config->dbname};host={$this->config->host}", $this->config->user, $this->config->password );
      $pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
    }
    catch( \Exception $e )
    {
      throw new PostgreSqlDatabaseCreatorException( 'Unable to connect to database' ); // If connection fails, the process must be aborted
    }

    // Insert data
    if( isset( $json->inserts ) )
    {
      foreach( $json->inserts as $insert )
      {
        $this->_execute_insert_query_from_array( $pdo, $insert );
      }
    }

    // Update data
    if( isset( $json->updates ) )
    {
      foreach( $json->updates as $update )
      {
        $this->_execute_update_query_from_array( $pdo, $update );
      }
    }

    $pdo = null;
  }



  /**
   * Inserts data
   *
   * @param \PDO $pdo
   * @param \stdClass $insert
   * @throws \Common\Database\PostgreSqlDatabaseCreatorException
   */
  private function _execute_insert_query_from_array( $pdo, $insert )
  {
    $table = $insert->table;
    $columns = join( ', ', array_keys( (array) $insert->columns ) ); // Columns: a, b, c
    $values = join( ', ', explode( ' ', trim( str_repeat( " ?", count( (array) $insert->columns ) ) ) ) ); // Values: ?, ?, ?

    $sql = "INSERT INTO {$table} ( {$columns} ) VALUES ( {$values} );";
    $pdo_statement = $pdo->prepare( $sql );

    $parameters = array_values( (array) $insert->columns );

    try
    {
      $pdo_statement->execute( $parameters );
    }
    catch( \Exception $e )
    {
      throw new PostgreSqlDatabaseCreatorException( 'Insert failed' );
    }
  }



  /**
   * Updates data
   *
   * @param \PDO $pdo
   * @param \stdClass $update
   * @throws \Common\Database\PostgreSqlDatabaseCreatorException
   */
  private function _execute_update_query_from_array( $pdo, $update )
  {
    $table = $update->table;
    $columns_sql = array();

    foreach( $update->columns as $column => $value )
    {
      $columns_sql[] = $column . ' = ?';
    }

    $sql = "UPDATE {$table} SET " . join( ', ', $columns_sql ) . ";";
    $pdo_statement = $pdo->prepare( $sql );

    $parameters = array_values( (array) $update->columns );

    try
    {
      $pdo_statement->execute( $parameters );
    }
    catch( \Exception $e )
    {
      throw new PostgreSqlDatabaseCreatorException( 'Update failed' );
    }
  }



  /**
   * Executes a db command and check if has errors
   *
   * @param string $command
   * @param string $arguments
   * @throws \Common\Database\PostgreSqlDatabaseCreatorException
   */
  private function _execute_db_command( $command, $arguments )
  {
    if( $this->config->db_installation_path )
    {
      // Prepare the command with the complete path
      $comm = '"' . $this->config->db_installation_path . DIRECTORY_SEPARATOR . $command . '" ' . $arguments;
    }
    else
    {
      // Prepare the command without the complete path
      $comm = $command . ' ' . $arguments;
    }

    // Execute the command
    exec( $comm, $output, $result );

    $result_message = implode( ' ', $output );

    if( $result != 0 )
    {
      throw new PostgreSqlDatabaseCreatorException( $result_message );
    }

    if( strpos( $result_message, 'ERROR' ) !== false )
    {
      throw new PostgreSqlDatabaseCreatorException( $result_message );
    }
  }

}
