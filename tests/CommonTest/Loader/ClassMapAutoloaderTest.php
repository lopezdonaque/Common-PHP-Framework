<?php

namespace CommonTest\Loader;


/**
 * Test for class "ClassMapAutoloader"
 */
class ClassMapAutoloaderTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Classmap file
   *
   * @var string
   */
  private $_classmap_file;



  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();
    $this->_classmap_file = $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . '/../commonphp_autoload_classmap.php';
  }



  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    parent::tearDown();
    unlink( $this->_classmap_file );
  }



  /**
   * Test for method "build_classmap"
   */
  public function test_build_classmap()
  {
    $paths = array
    (
      $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . '/classmap/dir1',
      $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . '/classmap/dir2',
      $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . '/classmap/dir_namespace'
    );

    $loader = new \Common\Loader\ClassMapAutoloader( $this->_classmap_file, $paths );
    $loader->build_classmap();

    // TODO: The absolute path is different on Jenkins
    //$expected = file_get_contents( $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . '/classmap/commonphp_autoload_classmap_result.php' );
    //$this->assertEquals( $expected, file_get_contents( $this->_classmap_file ) );

    $result = include( $this->_classmap_file );
    $expected = include( $GLOBALS[ '__COMMON_RESOURCES_PATH' ] . '/classmap/commonphp_autoload_classmap_result.php' );
    $this->assertEquals( count( $result ), count( $expected ) );
  }

}
