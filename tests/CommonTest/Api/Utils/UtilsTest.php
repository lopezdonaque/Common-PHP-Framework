<?php

/**
 * Test for class "Utils"
 *
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    if( !is_array( $_FILES ) )
    {
      $_FILES = array();
    }

    $_FILES[ 'formfield' ] = array
    (
      'error' => 0,
      'tmp_name' => __FILE__,
      'name' => basename( __FILE__ )
    );
  }



  /**
   * Test for method "get_attachment"
   */
  public function test_get_attachment()
  {
    $base = basename( __FILE__ );

    $local = \Common\Api\Utils\Utils::get_attachment( 'formfield' );
    $this->assertNotNull( $local, "Getting as form field failed" );
    $this->assertEquals( $local, __FILE__, "Gotten value is not __FILE__" );

    $local = \Common\Api\Utils\Utils::get_attachment( $base );
    $this->assertNotNull( $local, "Getting as _FILE filename failed" );
    $this->assertEquals( $local, __FILE__, "Gotten value is not __FILE__" );

    $local = \Common\Api\Utils\Utils::get_attachment( __FILE__ );
    $this->assertNotNull( $local, "Getting as local filename failed" );
    $this->assertEquals( $local, __FILE__, "Gotten local filename value is not __FILE__" );

    $local = \Common\Api\Utils\Utils::get_attachment( 'file:///' . __FILE__ );
    $this->assertNotNull( $local, "Getting as local filename failed" );
    $this->assertEquals( $local, 'file:///' . __FILE__, "Gotten local filename value is not __FILE__" );

    $local = \Common\Api\Utils\Utils::get_attachment( 'http://www.google.com/logo.png' );
    $this->assertNotNull( $local, "Getting as URL failed" );
    $this->assertEquals( $local, 'http://www.google.com/logo.png', "Gotten URL filename value is not __FILE__" );

    $local = \Common\Api\Utils\Utils::get_attachment( 'ftp://www.google.com/logo.png' );
    $this->assertNotNull( $local, "Getting as URL failed" );
    $this->assertEquals( $local, 'ftp://www.google.com/logo.png', "Gotten URL filename value is not __FILE__" );
  }

}
