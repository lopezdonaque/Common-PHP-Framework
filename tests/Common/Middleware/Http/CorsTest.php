<?php


/**
 * middleware cors test case.
 */
class CorsTest extends PHPUnit_Framework_TestCase
{

  /**
   * Test a preflight request
   *
   */
  public function test_preflight()
  {
    $request = new \Common\Middleware\Request();
    $request->httpRequest = new Common\Middleware\Http\Request();
    $request->httpRequest->method = 'OPTIONS';
    $response = new \Common\Middleware\Response();
    $response->httpResponse = new \Common\Middleware\Http\Response();
    $mid_cors = new \Common\Middleware\Http\Cors( '*' );
    $mid_cors->call( $request, $response );
    $this->assertArrayNotHasKey( 'Access-Control-Allow-Origin', $response->httpResponse->headers );
    $request->httpRequest->headers['origin'] =  'http://test.com';
    $request->httpRequest->headers['access-control-request-method'] =  'POST';
    $mid_cors->call( $request, $response );
    $this->assertArrayHasKey( 'Access-Control-Allow-Origin', $response->httpResponse->headers );
    $this->assertArrayHasKey( 'Access-Control-Max-Age', $response->httpResponse->headers );
    $this->assertArrayHasKey( 'Access-Control-Allow-Methods', $response->httpResponse->headers );

  }

  /**
   * Test normal request with CORS headers
   */
  public function test_normal_request()
  {
    $request = new \Common\Middleware\Request();
    $request->httpRequest = new Common\Middleware\Http\Request();
    $request->httpRequest->method = 'GET';
    $request->httpRequest->headers['origin'] =  'http://test.com';
    $response = new \Common\Middleware\Response();
    $response->httpResponse = new \Common\Middleware\Http\Response();
    $mid_cors = new \Common\Middleware\Http\Cors( '*' );
    $mid_cors->call( $request, $response );
    $this->assertArrayHasKey( 'Access-Control-Allow-Origin', $response->httpResponse->headers );
    $this->assertArrayNotHasKey( 'Access-Control-Max-Age', $response->httpResponse->headers );
    $this->assertArrayNotHasKey( 'Access-Control-Allow-Methods', $response->httpResponse->headers );
  }
}
