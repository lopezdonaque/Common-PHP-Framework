<?php

namespace CommonTest\Database;


/**
 * Test for class "PostgreSqlDatabaseCreator"
 */
class PostgreSqlDatabaseCreatorTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Database name
   *
   * @var string
   */
  private $_dbname;



  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();
    $this->_dbname = uniqid( 'test_' );
  }



  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    parent::tearDown();
    shell_exec( 'dropdb -U root ' . $this->_dbname );
  }



  /**
   * Test for method "exec"
   */
  public function test_exec()
  {
    $host = '127.0.0.1';

    $options = array
    (
      'host' => $host,
      'user' => 'root',
      'template' => 'template0',
      'dbname' => $this->_dbname,
      'procedural_languages' => array( 'plpgsql' ),
      'sql_scripts' => array
      (
        $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . DIRECTORY_SEPARATOR . 'database.sql'
      )
    );

    $dc = new \Common\Database\PostgreSqlDatabaseCreator( $options );
    $dc->exec();

    // Check if database has been created
    $num_ddbb = trim( shell_exec( '/usr/bin/psql -U root -h ' . $host . ' -l | grep ' . $this->_dbname .' | wc -l' ) );
    $this->assertEquals( '1', $num_ddbb );
  }



  /**
   * Test using an invalid SQL file
   */
  public function test_invalid_sql_script()
  {
    $host = '127.0.0.1';

    $options = array
    (
      'host' => $host,
      'user' => 'root',
      'template' => 'template0',
      'dbname' => $this->_dbname,
      'sql_scripts' => array
      (
        $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . DIRECTORY_SEPARATOR . 'database_fail.sql'
      )
    );

    $this->setExpectedException( '\Common\Database\PostgreSqlDatabaseCreatorException', null );
    $dc = new \Common\Database\PostgreSqlDatabaseCreator( $options );
    $dc->exec();
  }

}
