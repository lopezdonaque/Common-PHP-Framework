<?php

namespace Common\Middleware\Api;


/**
 * Base class to encode request response
 *
 */
abstract class BaseResponseEncoder implements \Common\Middleware\Listener
{

  /**
   * Call
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   * @throws \Exception
   */
  public function call( &$request, &$response )
  {
    if( $request->httpRequest->method == 'OPTIONS' )
    {
      return;
    }

    if( !$response->api_executed && !$response->api_exception )
    {
      throw new \Exception( 'API was not executed before calling response encoder' );
    }

    $response->httpResponse->code = 200;
    $output = $this->_get_output( $request, $response );

    switch( $request->api_return_format )
    {
      case \Common\Middleware\Api\Request::RETURN_PHP_SERIAL:
        $response->httpResponse->headers[ 'Content-Type' ] = 'application/php-serialized-data; charset=UTF-8;';
        $response->httpResponse->body = serialize( $output );
        break;

      default: // Default is JSON
        $response->httpResponse->headers[ 'Content-Type' ] = 'application/json; charset=UTF-8;';
        $response->httpResponse->body = json_encode( $output );
        break;
    }

    switch( $request->api_transport )
    {
      case \Common\Middleware\Api\Request::TRANSPORT_JSONP:
        $response->httpResponse->headers[ 'Content-Type' ] = 'text/javascript; charset=UTF-8;';
        $response->httpResponse->body = $request->api_callback . '(' . $response->httpResponse->body . ');';
        break;

      case \Common\Middleware\Api\Request::TRANSPORT_IFRAME:
        $response->httpResponse->headers[ 'Content-Type' ] = 'text/html; charset=UTF-8;';
        // JSON_HEX_APOS => All ' are converted to \u0027
        // JSON_HEX_QUOT => All " are converted to \u0022
        $json = json_encode( $output, JSON_HEX_APOS | JSON_HEX_QUOT );
        $json = str_replace( '\\', '\\\\', $json );
        $response->httpResponse->body = "<html><script type=\"text/javascript\"> window.name = '" . $json . "'; </script></html>";
        break;
    }
  }



  /**
   * Returns the output
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   * @return mixed
   */
  protected function _get_output( &$request, &$response ){}



  /**
   * Abort
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception ){}

}

