<?php

namespace Common\Middleware\JsonRpc;

/**
 * Encodes the given JSON-RPC Response object into the HTTP Response body
 *
 * @author Androme Iberica 2011
 * @package common
 */
class Encoder implements \Common\Middleware\Listener
{

  /**
   * Middleware "call" callback
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @return void
   */
  public function call( &$request, &$response )
  {
    if ( ! $response->httpResponse )
    {
      $response->httpResponse = new \Common\Middleware\Http\Response();
    }

    $response->httpResponse->code = 200;
    $response->httpResponse->set_header( 'Content-type', 'application/json' );
    $response->httpResponse->body = json_encode( $response->jsonRpcResponse  );
  }



  /**
   * Middleware "abort" callback. will be called if someone aborts the
   * pipeline down the road to allow rolling back or logging.
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @param \Exception $exception
   * @return void
   */
  public function abort( &$request, &$response, &$exception )
  {
  }

}
