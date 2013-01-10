<?php

namespace CommonTest\Middleware\JsonRpc;


/**
 * Test class for "Encoder"
 */
class EncoderTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Encoder instance
   *
   * @var \Common\Middleware\JsonRpc\Encoder
   */
  private $_encoder;



  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    $this->_encoder = new \Common\Middleware\JsonRpc\Encoder();
  }



  /**
   * Check we don't parse what's not a JSON/RPC request
   */
  public function test_encoding()
  {
    $expectedresponse = new \stdClass();
    $expectedresponse->foo = 1;
    $expectedresponse->bar = 2;
    $expectedresponse->quux = array( 1, 2, 3, 4, 5 );

    $request = new \Common\Middleware\Request();
    $request->httpRequest = new \Common\Middleware\Http\Request();
    $request->jsonRpcRequest = new \Common\JsonRpc\Request();

    $response = new \Common\Middleware\Response();
    $response->httpResponse = new \Common\Middleware\Http\Response();
    $response->jsonRpcResponse = new \Common\JsonRpc\Response();
    $response->jsonRpcResponse->error = null;
    $response->jsonRpcResponse->jsonrpc = '2.0';
    $response->jsonRpcResponse->result = $expectedresponse;

    $this->_encoder->call( $request, $response );
    $this->assertNotNull( $response->httpResponse->body );
    $this->assertEquals( $response->httpResponse->get_header( 'Content-type', false ), 'application/json', "Wrong returned content-type" );

    $obtained = json_decode( $response->httpResponse->body );
    $this->assertFalse( $obtained === false, "Undecodable JSON body" );
    $this->assertEquals( $expectedresponse, $obtained->result, "Fail decoding response" );
  }



  /**
   * Test for wrong request
   */
  public function test_wrong_request()
  {
    $body = json_encode( array( 'foo'=> 'bar' ) );

    $request = new \Common\Middleware\Request();
    $request->httpRequest = new \Common\Middleware\Http\Request();
    $request->httpRequest->headers[ 'Content-type' ] = 'application/json';
    $request->httpRequest->headers[ 'Content-length' ] = strlen( $body );
    $request->httpRequest->body = $body;

    $response = new \Common\Middleware\Response();

    $this->_encoder->call( $request, $response );
    $this->assertNull( $request->jsonRpcRequest );
  }

}
