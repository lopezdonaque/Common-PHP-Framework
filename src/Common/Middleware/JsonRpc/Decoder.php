<?php

namespace Common\Middleware\JsonRpc;


/**
 * Middleware to extract a potential JSON/RPC request from a HTTP request
 *
 */
class Decoder implements \Common\Middleware\Listener
{

  /**
   * Wether to stop processing if the request is not JSON/RPC
   *
   * @var boolean
   */
  private $_fullfill_if_not_jsonrpc;



  /**
   * Create the JSON/RPC extractor
   *
   * @param boolean $abort_if_not_jsonrpc wether to fullfill the request if the request is not JSON/RPC
   */
  public function __construct( $abort_if_not_jsonrpc = false )
  {
    $this->_fullfill_if_not_jsonrpc = $abort_if_not_jsonrpc;
  }



  /**
   * Abort
   *
   * @param $request
   * @param $response
   * @param $exception
   */
  public function abort( &$request, &$response, &$exception ){}



  /**
   * Check if the request is a JSON/RPC request
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   */
  public function call( &$request, &$response )
  {
    if( $response->fullfilled )
    {
      return;
    }

    $parsed  = $this->parse_jsonrpc_request( $request->httpRequest );

    if( $parsed instanceof \Common\JsonRpc\Response )
    {
      if ( $this->_fullfill_if_not_jsonrpc )
      {
        $response->fullfilled = true;
        $response->jsonRpcResponse = $parsed;
      }
    }
    else
    {
      $request->jsonRpcRequest = $parsed;
    }
  }



  /**
   * Check if the request is a JSON-RPC one.
   * It should be a HTTP POST with content-type application/json and the HTTP body should be json-decodeable.
   *
   * In case of success will return a JSON-RPC Request object
   * In case of failure will return a errored JSON-RPC response
   *
   * @param \Common\Middleware\Http\Request $request
   * @return \Common\JsonRpc\Request|\Common\JsonRpc\Response
   */
  protected function parse_jsonrpc_request( &$request )
  {
    if( $request->get_header( 'Content-type', false ) == 'application/json' && $request->get_header( 'Content-length' ) > 0 )
    {
      $decoded = @json_decode( $request->body );

      if( $decoded != null )
      {
        if( isset( $decoded->method ) )
        {
          $response = $this->extract_request( $request );
        }
        else
        {
          $response = new \Common\JsonRpc\Response();
          $response->error = new \Common\JsonRpc\Error();
          $response->jsonrpc = '2.0';
          $response->error->code = \Common\JsonRpc\Error::METHOD_NOT_FOUND;
          $response->error->message = "Method not found in request";
        }
      }
      else
      {
        $response = new \Common\JsonRpc\Response();
        $response->error = new \Common\JsonRpc\Error();
        $response->jsonrpc = '2.0';
        $response->error->code = \Common\JsonRpc\Error::PARSE_ERROR;
        $response->error->message = "Error parsing the JSON request";
      }
    }
    else
    {
      $response = new \Common\JsonRpc\Response();
      $response->error = new \Common\JsonRpc\Error();
      $response->jsonrpc = '2.0';
      $response->error->code = \Common\JsonRpc\Error::INVALID_REQUEST;
      $response->error->message = "Request not found or content-type not application/json";
    }

    return $response;
  }



  /**
   * Extract the JSON-RPC request
   *
   * @param \Common\Middleware\Http\Request $request
   * @return \Common\JsonRpc\Request
   */
  protected function extract_request( &$request )
  {
    $jsonrequest = new \Common\JsonRpc\Request();
    $decoded = @json_decode( $request->body );
    $jsonrequest->jsonrpc = ( isset( $decoded->jsonrpc ) ) ? $decoded->jsonrpc : '1.0';
    $jsonrequest->id = ( isset( $decoded->id ) ) ? $decoded->id : null;
    $jsonrequest->method = $decoded->method;
    $jsonrequest->params = ( isset( $decoded->params ) ) ? $decoded->params : array();

    foreach( $decoded as $id => $value )
    {
      if( $id == 'jsonrpc' || $id == 'id' || $id == 'method' || $id == 'params' )
      {
        continue;
      }

      $jsonrequest->extensions[ $id ] = $value;
    }

    return $jsonrequest;
  }

}
