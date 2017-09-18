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
    $options =
    [
      'user' => 'root',
      'dbname' => $this->_dbname,
      'procedural_languages' => [ 'plpgsql' ],
      'sql_scripts' =>
      [
        $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . DIRECTORY_SEPARATOR . 'database.sql'
      ],
      'fixtures' =>
      [
        $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . DIRECTORY_SEPARATOR . 'fixtures.sql',
        $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . DIRECTORY_SEPARATOR . 'fixtures.json'
      ]
    ];

    $dc = new \Common\Database\PostgreSqlDatabaseCreator( $options );
    $dc->exec();

    // Check if database has been created
    $this->assertTrue( $dc->exists_database() );
  }



  /**
   * Test using an invalid SQL file
   */
  public function test_invalid_sql_script()
  {
    $options =
    [
      'user' => 'root',
      'dbname' => $this->_dbname,
      'sql_scripts' =>
      [
        $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . DIRECTORY_SEPARATOR . 'database_fail.sql'
      ]
    ];

    $this->setExpectedException( '\Common\Database\PostgreSqlDatabaseCreatorException', null );
    $dc = new \Common\Database\PostgreSqlDatabaseCreator( $options );
    $dc->exec();
  }

}
