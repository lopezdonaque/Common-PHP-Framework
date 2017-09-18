<?php

namespace Common\Middleware\Api;


/**
 * Middleware to extract a potential API-JSON/RPC request from the HTTP request
 * Extracts and validates required parameters like entity, method, arguments, etc.
 *
 */
class JsonRpcParser implements \Common\Middleware\Listener
{

  /**
   * Check if the request is a JSON/RPC request
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   */
  public function call( &$request, &$response )
  {
    if( $response->fullfilled )
    {
      return;
    }

    $this->_check_parameters( $request, $response );
  }



  /**
   * Checks required parameters. Will set $response exception.
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   */
  private function _check_parameters( &$request, &$response )
  {
    if( !isset( $request->httpRequest->parameters ) )
    {
      $response->api_exception = new \Common\Api\Exception( 'Cannot decode parameters', \Common\Api\ExceptionCodes::CALL_MISSING_ARGUMENTS );
      return;
    }

    // Mandatory JSON encoded parameters
    foreach( [ 'entity', 'method', 'arguments' ] as $param )
    {
      if( !isset( $request->httpRequest->parameters[ $param ] ) )
      {
        $response->api_exception = new \Common\Api\Exception( "Missing $param in parameters", \Common\Api\ExceptionCodes::CALL_MISSING_ARGUMENTS );
        $response->fullfilled = true;
        return;
      }

      if( $param == 'arguments' )
      {
        $decoded_param = json_decode( $request->httpRequest->parameters[ $param ], true );

        if( $decoded_param === false )
        {
          $response->api_exception = new \Common\Api\Exception( "Cannot json-decode $param in parameters", \Common\Api\ExceptionCodes::CALL_MISSING_ARGUMENTS );
          $response->fullfilled = true;
          return;
        }

        if( !is_array( $decoded_param ) )
        {
          $response->api_exception = new \Common\Api\Exception( "The arguments parameter must be a json encoded array", \Common\Api\ExceptionCodes::CALL_MISSING_ARGUMENTS );
          $response->fullfilled = true;
          return;
        }

        // Convert associative arrays to stdClass
        $decoded_param = json_decode( json_encode( $decoded_param ) );
      }
      else
      {
        $decoded_param = $request->httpRequest->parameters[ $param ];
      }

      $propname = 'api_' . $param;
      $request->$propname = $decoded_param;
    }

    // Optional parameters
    foreach( [ 'token', 'callback', 'return_format', 'transaction_id', 'transport' ] as $param )
    {
      if( isset( $request->httpRequest->parameters[ $param ] ) )
      {
        $decoded_param = $request->httpRequest->parameters[ $param ];
        $propname = 'api_'. $param;
        $request->$propname = $decoded_param;
      }
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
