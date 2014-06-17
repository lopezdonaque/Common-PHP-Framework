<?php

namespace Common\Middleware\JsonRpc;


/**
 * Encodes the given JSON-RPC Response object into the HTTP Response body
 *
 */
class Encoder implements \Common\Middleware\Listener
{

  /**
   * Middleware "call" callback
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   */
  public function call( &$request, &$response )
  {
    if( !$response->httpResponse )
    {
      $response->httpResponse = new \Common\Middleware\Http\Response();
    }

    $response->httpResponse->code = 200;
    $response->httpResponse->set_header( 'Content-type', 'application/json' );
    $response->httpResponse->body = json_encode( $response->jsonRpcResponse  );
  }



  /**
   * Middleware "abort" callback.
   * Will be called if someone aborts the pipeline down the road to allow rolling back or logging.
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception ){}

}
