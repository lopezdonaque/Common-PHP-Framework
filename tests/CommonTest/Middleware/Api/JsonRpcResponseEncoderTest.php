<?php

namespace CommonTest\Middleware\Api;


/**
 * Test class for "JsonRpcResponseEncoder"
 */
class JsonRpcResponseEncoderTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Encoder instance
   *
   * @var \Common\Middleware\Api\JsonRpcResponseEncoder
   */
  private $encoder;



  /**
   * Prepares the environment before running a test
   */
  protected function setUp()
  {
    parent::setUp();
    $this->encoder = new \Common\Middleware\Api\JsonRpcResponseEncoder();
  }



  /**
   * Test api exception response
   */
  public function test_api_exception()
  {
    $request = new \Common\Middleware\Api\Request();
    $request->api_transaction_id = uniqid( '', true );
    $request->httpRequest = new \Common\Middleware\Http\Request();
    $request->httpRequest->method = 'POST';
    $request->httpRequest->headers[ 'content-type' ] = 'text/html';
    $request->httpRequest->headers[ 'content-length' ] = 8;
    $request->httpRequest->body = '12345678';

    $response = new \Common\Middleware\Api\Response();
    $response->api_executed = true;
    $response->api_exception = new \Exception( '' );

    $this->encoder->call( $request, $response );

    $this->assertTrue( $response->httpResponse->headers[ 'Content-Type' ] == 'application/json; charset=UTF-8;' );
    $this->assertTrue( \Common\Utils\Json::is_json( $response->httpResponse->body ) );
  }

}
