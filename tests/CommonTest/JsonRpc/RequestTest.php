<?php

namespace CommonTest\JsonRpc;


/**
 * Test class for Request.
 *
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Request instance
   *
   * @var \Common\JsonRpc\Request
   */
  private $_request;



  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    $this->_request = new \Common\JsonRpc\Request();
  }



  /**
   * Test for method "is_notification"
   */
  public function test_is_notification()
  {
    $this->_request->id = null;
    $this->assertTrue( $this->_request->is_notification() );

    $this->_request->id = 1;
    $this->assertFalse( $this->_request->is_notification() );
  }

}
