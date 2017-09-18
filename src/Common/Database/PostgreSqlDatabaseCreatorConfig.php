<?php

namespace Common\Database;


/**
 * Defines configuration to create a PostgreSQL database
 *
 */
class PostgreSqlDatabaseCreatorConfig
{

  /**
   * Host
   *
   * @var string
   */
  public $host = '127.0.0.1';


  /**
   * Port
   *
   * @var int
   */
  public $port;


  /**
   * User
   *
   * @var string
   */
  public $user;


  /**
   * Password
   *
   * @var string
   */
  public $password;


  /**
   * Database name
   *
   * @var string
   */
  public $dbname;


  /**
   * Template
   *
   * @var string
   */
  public $template = 'template0';


  /**
   * Owner
   *
   * @var string
   */
  public $owner;


  /**
   * Tablespace
   *
   * @var string
   */
  public $tablespace;


  /**
   * Database scripts (SQL files or SQL code which will be executed)
   *
   * @var array
   */
  public $sql_scripts = [];


  /**
   * Procedural languages to install on database.
   * For example: plpgsql, pltcl, plperl or plphyton.
   * This is the type of language to write procedures, etc. inside the new database.
   *
   * @var array
   */
  public $procedural_languages = [];


  /**
   * Fixtures (sql or json)
   *
   * @var array
   */
  public $fixtures = [];


  /**
   * Directory where the database executables are installed
   *
   * @var string
   */
  public $db_installation_path;


  /**
   * Defines if the database will be removed if already exists
   *
   * @var bool
   */
  public $dropdb = true;



  /**
   * Constructor
   *
   * @param array $config
   */
  public function __construct( $config = [] )
  {
    foreach( $config as $key => $value )
    {
      $this->$key = $value;
    }
  }

}
