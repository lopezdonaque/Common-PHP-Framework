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
   * Host
   *
   * @var string
   */
  private $_host;


  /**
   * Port
   *
   * @var int
   */
  private $_port;


  /**
   * User
   *
   * @var string
   */
  private $_user;


  /**
   * Database name
   *
   * @var string
   */
  private $_dbname;


  /**
   * Template
   *
   * @var string
   */
  private $_template;


  /**
   * Owner
   *
   * @var string
   */
  private $_owner;


  /**
   * Tablespace
   *
   * @var string
   */
  private $_tablespace;


  /**
   * Database scripts (sql files which will be executed)
   *
   * @var array
   */
  private $_sql_scripts = array();


  /**
   * Procedural languages to install on database.
   * For example: plpgsql, pltcl, plperl or plphyton.
   * This is the type of language to write procedures, etc. inside the new database.
   *
   * @var array
   */
  private $_procedural_languages = array();


  /**
   * Directory where the database is installed
   *
   * @var string
   */
  private $_db_installation_path;



  /**
   * Constructor
   *
   * @param array $options
   * @return \Common\Database\PostgreSqlDatabaseCreator
   */
  public function __construct( $options = array() )
  {
    $this->_apply_options( $options );
    return $this;
  }



  /**
   * Apply options
   *
   * @param array $options
   */
  private function _apply_options( $options )
  {
    if( isset( $options[ 'host' ] ) )
    {
      $this->_host = $options[ 'host' ];
    }

    if( isset( $options[ 'port' ] ) )
    {
      $this->_port = $options[ 'port' ];
    }

    if( isset( $options[ 'user' ] ) )
    {
      $this->_user = $options[ 'user' ];
    }

    if( isset( $options[ 'dbname' ] ) )
    {
      $this->_dbname = $options[ 'dbname' ];
    }

    if( isset( $options[ 'template' ] ) )
    {
      $this->_template = $options[ 'template' ];
    }

    if( isset( $options[ 'owner' ] ) )
    {
      $this->_owner = $options[ 'owner' ];
    }

    if( isset( $options[ 'tablespace' ] ) )
    {
      $this->_owner = $options[ 'tablespace' ];
    }

    if( isset( $options[ 'sql_scripts' ] ) )
    {
      $this->_sql_scripts = $options[ 'sql_scripts' ];
    }

    if( isset( $options[ 'procedural_languages' ] ) )
    {
      $this->_procedural_languages = $options[ 'procedural_languages' ];
    }

    if( isset( $options[ 'db_installation_path' ] ) )
    {
      $this->_db_installation_path = $options[ 'db_installation_path' ];
    }
  }



  /**
   * Executes the creation of the database
   *
   * @return bool
   */
  public function exec()
  {
    // Execute create database
    if( !$this->_execute_createdb_command() )
    {
      return false;
    }

    // Install procedural languages
    if( !$this->_execute_langs() )
    {
      return false;
    }

    // Exectue sql scripts
    if( !$this->_execute_sql_scripts() )
    {
      return false;
    }

    return true;
  }



  /**
   * Executes the createdb command to create customer database
   *
   * @return bool
   */
  private function _execute_createdb_command()
  {
    $arguments = '';

    if( $this->_host )
    {
      $arguments .= '-h ' . $this->_host;
    }

    if( $this->_port )
    {
      $arguments .= ' -p ' . $this->_port;
    }

    if( $this->_user )
    {
      $arguments .= ' -U ' . $this->_user;
    }

    if( $this->_owner )
    {
      $arguments .= ' -O ' . $this->_owner;
    }

    if( $this->_tablespace )
    {
      $arguments .= ' -D ' . $this->_tablespace;
    }

    $arguments .= " -E utf8 -T " . $this->_template . " " . $this->_dbname . " 2>&1";

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
  private function _execute_langs()
  {
    if( count( $this->_procedural_languages ) > 0 )
    {
      foreach( $this->_procedural_languages as $lang )
      {
        if( !$this->_execute_createlang_command( $lang ) )
        {
          return false;
        }
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

    if( $this->_host )
    {
      $arguments .= '-h ' . $this->_host;
    }

    if( $this->_port )
    {
      $arguments .= " -p " . $this->_port;
    }

    if( $this->_user )
    {
      $arguments .= " -U " . $this->_user;
    }

    $arguments .= " $lang " . $this->_dbname . " 2>&1";

    if( !$this->_execute_db_command( 'createlang', $arguments ) )
    {
      return false;
    }

    return true;
  }



  /**
   * Executes the sql scripts
   *
   * @return bool
   */
  private function _execute_sql_scripts()
  {
    if( count( $this->_sql_scripts ) > 0 )
    {
      foreach( $this->_sql_scripts as $script )
      {
        if( !$this->_execute_sql_script( $script ) )
        {
          return false;
        }
      }
    }

    return true;
  }



  /**
   * Executes a sql script
   *
   * @param string $script_name
   * @return bool
   * @throws \Common\Database\PostgreSqlDatabaseCreatorException
   */
  private function _execute_sql_script( $script_name )
  {
    // Check if the script exists
    if( !file_exists( $script_name ) )
    {
      throw new PostgreSqlDatabaseCreatorException( 'SQL script not found in: ' . $script_name );
    }

    $arguments = '';

    if( $this->_host )
    {
      $arguments .= '-h ' . $this->_host;
    }

    if( $this->_port != '' )
    {
      $arguments .= " -p " . $this->_port;
    }

    if( $this->_user != '' )
    {
      $arguments .= " -U " . $this->_user;
    }

    $arguments .= " -d " . $this->_dbname . " -f " . $script_name . " 2>&1";

    // Set the script path as current path to allow execute other scripts from the master db_script ("\i other.sql")
    $oldcwd = getcwd();
    chdir( dirname( $script_name ) );

    if( !$this->_execute_db_command( 'psql', $arguments, $oldcwd ) )
    {
      return false;
    }

    return true;
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
    if( $this->_db_installation_path != '' )
    {
      // Prepare the command with the complete path
      $comm = '"' . $this->_db_installation_path . DIRECTORY_SEPARATOR . $command . '" ' . $arguments;
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
