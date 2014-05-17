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
  private $_config;



  /**
   * Constructor
   *
   * @param array|PostgreSqlDatabaseCreatorConfig $config
   */
  public function __construct( $config = array() )
  {
    $this->_config = is_array( $config ) ? new PostgreSqlDatabaseCreatorConfig( $config ) : $config;
  }



  /**
   * Executes the creation of the database
   *
   * @return bool
   */
  public function exec()
  {
    if( $this->_config->dropdb && $this->exists_database() )
    {
      $this->_execute_db_command( 'dropdb', array( $this->_config->dbname ) );
    }

    // Execute create database
    if( !$this->_createdb() )
    {
      return false;
    }

    // Install procedural languages
    if( !$this->_create_langs() )
    {
      return false;
    }

    // Execute SQL scripts
    if( !$this->_execute_sql_scripts() )
    {
      return false;
    }

    // Execute fixtures
    if( !$this->_execute_fixtures() )
    {
      return false;
    }

    return true;
  }



  /**
   * Executes the createdb command to create database
   *
   * @return bool
   */
  private function _createdb()
  {
    $arguments = '';

    if( $this->_config->host )
    {
      $arguments .= '-h ' . $this->_config->host;
    }

    if( $this->_config->port )
    {
      $arguments .= ' -p ' . $this->_config->port;
    }

    if( $this->_config->user )
    {
      $arguments .= ' -U ' . $this->_config->user;
    }

    if( $this->_config->owner )
    {
      $arguments .= ' -O ' . $this->_config->owner;
    }

    if( $this->_config->tablespace )
    {
      $arguments .= ' -D ' . $this->_config->tablespace;
    }

    $arguments .= " -E utf8 -T " . $this->_config->template . " " . $this->_config->dbname . " 2>&1";

    if( !$this->_execute_db_command( 'createdb', $arguments ) )
    {
      return false;
    }

    return true;
  }



  /**
   * Install procedural languages on database
   *
   * @return bool
   */
  private function _create_langs()
  {
    foreach( $this->_config->procedural_languages as $lang )
    {
      if( !$this->_execute_createlang_command( $lang ) )
      {
        return false;
      }
    }

    return true;
  }



  /**
   * Executes the createlang command to install procedural language on database
   *
   * @param string $lang
   * @return bool
   */
  private function _execute_createlang_command( $lang )
  {
    $arguments = '';

    if( $this->_config->host )
    {
      $arguments .= '-h ' . $this->_config->host;
    }

    if( $this->_config->port )
    {
      $arguments .= " -p " . $this->_config->port;
    }

    if( $this->_config->user )
    {
      $arguments .= " -U " . $this->_config->user;
    }

    $arguments .= " $lang " . $this->_config->dbname . " 2>&1";

    if( !$this->_execute_db_command( 'createlang', $arguments ) )
    {
      return false;
    }

    return true;
  }



  /**
   * Executes the SQL scripts
   *
   * @return bool
   */
  private function _execute_sql_scripts()
  {
    foreach( $this->_config->sql_scripts as $script )
    {
      if( !$this->_execute_sql_script( $script ) )
      {
        return false;
      }
    }

    return true;
  }



  /**
   * Executes the fixtures
   *
   * @return bool
   */
  private function _execute_fixtures()
  {
    foreach( $this->_config->fixtures as $file )
    {
      $method = ( pathinfo( $file, PATHINFO_EXTENSION ) == 'sql' ) ? '_execute_sql_script' : '_add_data';

      if( !$this->$method( $file ) )
      {
        return false;
      }
    }

    return true;
  }



  /**
   * Executes a SQL script
   *
   * @param string $filename
   * @return bool
   * @throws \Common\Database\PostgreSqlDatabaseCreatorException
   */
  private function _execute_sql_script( $filename )
  {
    // Check if the script exists
    if( !file_exists( $filename ) )
    {
      throw new PostgreSqlDatabaseCreatorException( 'SQL script not found in: ' . $filename );
    }

    $arguments = '';

    if( $this->_config->host )
    {
      $arguments .= '-h ' . $this->_config->host;
    }

    if( $this->_config->port )
    {
      $arguments .= " -p " . $this->_config->port;
    }

    if( $this->_config->user )
    {
      $arguments .= " -U " . $this->_config->user;
    }

    $arguments .= " -d " . $this->_config->dbname . " -f " . $filename . " 2>&1";

    // Set the script path as current path to allow execute other scripts from the master db_script ("\i other.sql")
    $oldcwd = getcwd();
    chdir( dirname( $filename ) );

    if( !$this->_execute_db_command( 'psql', $arguments, $oldcwd ) )
    {
      return false;
    }

    return true;
  }



  /**
   * Returns if database exists
   *
   * @return bool
   */
  public function exists_database()
  {
    $num_ddbb = shell_exec( '/usr/bin/psql -h ' . $this->_config->host . ' -l | grep -w ' . $this->_config->dbname . ' | wc -l' );
    $num_ddbb = (int) trim( $num_ddbb );
    return ( $num_ddbb > 0 );
  }



  /**
   * Adds fixtures data
   *
   * @param string $file
   * @return bool
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
      $pdo = new \PDO( "pgsql:dbname={$this->_config->dbname};host={$this->_config->host}", $this->_config->user, $this->_config->password );
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
    return true;
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
   * @param string $oldcwd Optional directory to go back to after executing (to relocate current dir)
   * @return bool
   * @throws \Common\Database\PostgreSqlDatabaseCreatorException
   */
  private function _execute_db_command( $command, $arguments, $oldcwd = null )
  {
    if( $this->_config->db_installation_path )
    {
      // Prepare the command with the complete path
      $comm = '"' . $this->_config->db_installation_path . DIRECTORY_SEPARATOR . $command . '" ' . $arguments;
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

    if( $oldcwd != null )
    {
      chdir( $oldcwd );
    }

    return true;
  }

}
