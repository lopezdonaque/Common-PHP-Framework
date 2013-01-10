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
   * Wether to stop processing if the request is not JSON/RPC
   *
   * @var boolean
   */
  private $_abort_if_not_jsonrpc;



  /**
   * Create the JSON/RPC extractor
   *
   * @param boolean $abort_if_not_jsonrpc Wether to stop processing if the request is not JSON/RPC
   */
  public function __construct( $abort_if_not_jsonrpc = false )
  {
    $this->_abort_if_not_jsonrpc = $abort_if_not_jsonrpc;
  }



  /**
   * Abort
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception ){}



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
   * @return boolean True if all parameters found, false if not.
   */
  private function _check_parameters( &$request, &$response )
  {
    if( !isset( $request->httpRequest->parameters ) )
    {
      $response->api_exception = new \Common\Api\Exception( 'Cannot decode parameters', \Common\Api\ExceptionCodes::CALL_MISSING_ARGUMENTS );
      return false;
    }

    // Mandatory JSON encoded parameters
    foreach( array( 'entity', 'method', 'arguments' ) as $param )
    {
      if( !isset( $request->httpRequest->parameters[ $param ] ) )
      {
        $response->api_exception = new \Common\Api\Exception( "Missing $param in parameters", \Common\Api\ExceptionCodes::CALL_MISSING_ARGUMENTS );
        $response->fullfilled = true;
        return false;
      }

      if( $param == 'arguments' )
      {
        $decoded_param = json_decode( $request->httpRequest->parameters[ $param ], true );

        if( $decoded_param === false )
        {
          $response->api_exception = new \Common\Api\Exception( "Cannot json-decode $param in parameters", \Common\Api\ExceptionCodes::CALL_MISSING_ARGUMENTS );
          $response->fullfilled = true;
          return false;
        }

        $decoded_param = \Common\Utils\Arrays::convert_to_arguments( $decoded_param );
      }
      else
      {
        $decoded_param = $request->httpRequest->parameters[ $param ];
      }

      $propname = 'api_' . $param;
      $request->$propname = $decoded_param;
    }

    // Optional parameters
    foreach( array( 'token', /*'user_data',*/ 'callback', 'return_format', 'transaction_id', 'transport' ) as $param )
    {
      if( isset( $request->httpRequest->parameters[ $param ] ) )
      {
        /*if( $param == 'user_data' )
        {
          $decoded_param = json_decode( $request->httpRequest->parameters[ $param ], false );

          if( $decoded_param === false )
          {
            $response->api_exception = new \Exception( "Cannot json-decode $param in parameters" );
            $response->fullfilled = true;
            return false;
          }
        }
        else
        {*/
          $decoded_param = $request->httpRequest->parameters[ $param ];
        //}

        $propname = 'api_'. $param;
        $request->$propname = $decoded_param;
      }
    }
  }

}