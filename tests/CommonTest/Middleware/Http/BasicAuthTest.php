<?php

require_once "FakeAuthenticator.php";


/**
 * Test class for BasicAuth.
 */
class BasicAuthTest extends PHPUnit_Framework_TestCase
{

  /**
   * Basic auth instance
   *
   * @var \Common\Middleware\Http\BasicAuth
   */
  private $_basic_auth;



  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp()
  {
    $this->_basic_auth = new \Common\Middleware\Http\BasicAuth( true, new \CommonTest\Middleware\Http\FakeAuthenticator() );
  }



  /**
   * Test for method "call"
   */
  public function test_call()
  {
    $request = new \Common\Middleware\Request();
    $request->httpRequest = new \Common\Middleware\Http\Request();

    $response = new \Common\Middleware\Response();
    $request->httpRequest->authentication_user = 'user';
    $request->httpRequest->authentication_pass = 'right';

    $this->_basic_auth->call( $request, $response );
    $this->assertTrue( $request->httpRequest->authenticated );
    $this->assertFalse( $response->fullfilled );

    $request->httpRequest->authentication_pass = 'wrong';
    $this->_basic_auth->call( $request, $response );
    $this->assertFalse( $request->httpRequest->authenticated );
    $this->assertTrue( $response->fullfilled );
    $this->assertEquals( 401, $response->httpResponse->code );
  }

}
