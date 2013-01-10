<?php

namespace CommonTest\Middleware\JsonRpc;


/**
 * Test class for "Decoder"
 */
class DecoderTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Decoder instance
   *
   * @var \Common\Middleware\JsonRpc\Decoder
   */
  private $_decoder;



  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();
    $this->_decoder = new \Common\Middleware\JsonRpc\Decoder( true );
  }



  /**
   * Check we don't parse what's not a JSON/RPC request
   */
  public function test_wrong_contenttype()
  {
    $request = new \Common\Middleware\Request();
    $request->httpRequest = new \Common\Middleware\Http\Request();
    $request->httpRequest->headers[ 'content-type' ] = 'text/html';
    $request->httpRequest->headers[ 'content-length' ] = 8;
    $request->httpRequest->body = 'xxxxxxxx';

    $response = new \Common\Middleware\Response();

    $this->_decoder->call( $request, $response );
    $this->assertNull( $request->jsonRpcRequest );
    $this->assertNotNull( $response->jsonRpcResponse );
    $this->assertNotNull( $response->jsonRpcResponse->error );
    $this->assertEquals( \Common\JsonRpc\Error::INVALID_REQUEST, $response->jsonRpcResponse->error->code );
  }



  /**
   * Test method not found
   */
  public function test_method_not_found()
  {
    $body = json_encode( array( 'foo'=> 'bar' ) );

    $request = new \Common\Middleware\Request();
    $request->httpRequest = new \Common\Middleware\Http\Request();
    $request->httpRequest->headers[ 'content-type' ] = 'application/json';
    $request->httpRequest->headers[ 'content-length' ] = strlen( $body );
    $request->httpRequest->body = $body;

    $response = new \Common\Middleware\Response();

    $this->_decoder->call( $request, $response );
    $this->assertNull( $request->jsonRpcRequest );
    $this->assertNotNull( $response->jsonRpcResponse );
    $this->assertNotNull( $response->jsonRpcResponse->error );
    $this->assertEquals( \Common\JsonRpc\Error::METHOD_NOT_FOUND, $response->jsonRpcResponse->error->code );
  }



  /**
   * Test for jsonrpc 1.0 request
   */
  public function test_jsonrpc_10_request()
  {
    $body = json_encode( array
    (
      'id'     => 1,
      'method' => 'foo',
      'params' => array( 'bar' ) )
    );

    $request = new \Common\Middleware\Request();
    $request->httpRequest = new \Common\Middleware\Http\Request();
    $request->httpRequest->headers[ 'content-type' ] = 'application/json';
    $request->httpRequest->headers[ 'content-length' ] = strlen( $body );
    $request->httpRequest->body = $body;

    $response = new \Common\Middleware\Response();

    $this->_decoder->call( $request, $response );
    $this->assertNull( $response->jsonRpcResponse );
    $this->assertEquals( '1.0', $request->jsonRpcRequest->jsonrpc );
    $this->assertEquals( 1, $request->jsonRpcRequest->id );
    $this->assertEquals( 'foo', $request->jsonRpcRequest->method );
    $this->assertEquals( array( 'bar' ), $request->jsonRpcRequest->params );
  }



  /**
   * Test for jsonrpc 2.0 request
   */
  public function test_jsonrpc_20_request()
  {
    $body = json_encode( array
    (
      'jsonrpc'=> '2.0',
      'id'     => 1,
      'method' => 'foo',
      'params' => array( 'bar' ) )
    );

    $request = new \Common\Middleware\Request();
    $request->httpRequest = new \Common\Middleware\Http\Request();
    $request->httpRequest->headers[ 'content-type' ] = 'application/json';
    $request->httpRequest->headers[ 'content-length' ] = strlen( $body );
    $request->httpRequest->body = $body;

    $response = new \Common\Middleware\Response();

    $this->_decoder->call( $request, $response );
    $this->assertNotNull( $request->jsonRpcRequest );
    $this->assertEquals( '2.0', $request->jsonRpcRequest->jsonrpc );
    $this->assertEquals( 1, $request->jsonRpcRequest->id );
    $this->assertEquals( 'foo', $request->jsonRpcRequest->method );
    $this->assertEquals( array( 'bar' ), $request->jsonRpcRequest->params );
  }



  /**
   * Test for jsonrpc 2.0 notification
   */
  public function test_jsonrpc_20_notification()
  {
    $body = json_encode( array
    (
      'jsonrpc'=> '2.0',
      'method' => 'foo',
      'params' => array( 'bar' ) )
    );

    $request = new \Common\Middleware\Request();
    $request->httpRequest = new \Common\Middleware\Http\Request();
    $request->httpRequest->headers[ 'content-type' ] = 'application/json';
    $request->httpRequest->headers[ 'content-length' ] = strlen( $body );
    $request->httpRequest->body = $body;

    $response = new \Common\Middleware\Response();

    $this->_decoder->call( $request, $response );
    $this->assertNotNull( $request->jsonRpcRequest );
    $this->assertEquals( '2.0', $request->jsonRpcRequest->jsonrpc );
    $this->assertEquals( null, $request->jsonRpcRequest->id );
    $this->assertEquals( 'foo', $request->jsonRpcRequest->method );
    $this->assertEquals( array( 'bar' ), $request->jsonRpcRequest->params );
    $this->assertTrue( $request->jsonRpcRequest->is_notification() );
  }

}
