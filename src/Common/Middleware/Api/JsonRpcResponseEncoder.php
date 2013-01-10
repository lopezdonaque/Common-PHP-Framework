<?php

namespace Common\Middleware\Api;


/**
 * Response encoding for Phemium-API-JSON/RPC requests
 *
 */
class JsonRpcResponseEncoder implements \Common\Middleware\Listener
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
      throw new \Exception( "API was not executed before calling response encoder" );
    }

    $response->httpResponse->code = 200;
    $output = new \stdClass();

    if( $response->api_exception )
    {
      $output->transaction_id = $request->api_transaction_id;
      $output->error = true;
      $output->message = $response->api_exception->getMessage();
      $output->code = $response->api_exception->getCode();
      $output->type = ''; // ONLY for backward compatibility with JavaConnector
    }
    else
    {
      $output = $response->api_return_value;
    }

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
        $response->httpResponse->body = $request->api_callback . '(' . $response->body . ');';
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
   * Abort
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception ){}

}

