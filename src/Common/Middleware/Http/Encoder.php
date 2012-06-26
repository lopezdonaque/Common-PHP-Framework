<?php

namespace Common\Middleware\Http;

/**
 * Given a middleware request, output the given headers and body.
 * Usually the last piece on a HTTP-based pipeline
 *
 * @package   common
 */
class Encoder implements \Common\Middleware\Listener
{


  /**
   * Middleware "call" callback
   *
   * @param \Common\Middleware\Request  $request
   * @param \Common\Middleware\Response $response
   */
  public function call( &$request, &$response )
  {
    if( $response->httpResponse->code != 200 )
    {
      header( "HTTP/1.1 " . $response->httpResponse->code . ' ' . $response->httpResponse->reason );
    }
    foreach( $response->httpResponse->headers as $k => $v )
    {
      header( "$k: $v" );
    }

    $length = strlen( $response->httpResponse->body );
    header( "Content-length: $length" );
    print $response->httpResponse->body;
  }



  /**
   * Middleware "abort" callback. will be called if someone aborts the
   * pipeline down the road to allow rolling back or logging.
   *
   * @see Listener::abort()
   *
   * @param \Common\Middleware\Request  $request
   * @param \Common\Middleware\Response $response
   * @param \Exception                  $exception
   */
  public function abort( &$request, &$response, &$exception ){}

}

