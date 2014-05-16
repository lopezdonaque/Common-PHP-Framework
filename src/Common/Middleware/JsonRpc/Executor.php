<?php

namespace Common\Middleware\JsonRpc;


/**
 * A JSON-RPC executor. If passed an object, that will the one used to call its "method" as told by the JsonRpc\Request
 *
 */
class Executor implements \Common\Middleware\Listener
{

  /**
   * Handler object for the request.
   *
   * @var object
   */
  private $_handler;



  /**
   * Constructor
   *
   * @param string $handler
   */
  public function __construct( $handler = null )
  {
    $this->_handler = $handler;
  }



  /**
   * Middleware "call" callback
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   */
  public function call( &$request, &$response )
  {
    if( $response->fullfilled ) // Something went wrong before us
    {
      return;
    }

    $response->jsonRpcResponse = new \Common\JsonRpc\Response();
    $response->jsonRpcResponse->id = $request->jsonRpcRequest->id;

    $object = null;
    $method = $request->jsonRpcRequest->method;

    // Extract object and method to call from the method when it contains "." (ie. object_x.method_y)
    if( strpos( $request->jsonRpcRequest->method, '.' ) !== false )
    {
      list( $object, $method ) = explode( '.', $request->jsonRpcRequest->method, 2 );

      if( $this->_handler )
      {
        $object = $this->_handler;
      }
      else
      {
        if( class_exists( $object ) )
        {
          $object = new $object();
        }
      }
    }
    else
    {
      $object = $this->_handler;
    }

    if( !$object || !is_callable( array( $object, $method ) ) )
    {
      $response->jsonRpcResponse->result = null;
      $response->jsonRpcResponse->error = new \Common\JsonRpc\Error();
      $response->jsonRpcResponse->error->code = \Common\JsonRpc\Error::METHOD_NOT_FOUND;
      $response->jsonRpcResponse->error->message = "Method not found";
      return;
    }

    // Convert associative arrays to stdClass
    $args = json_decode( json_encode( $request->jsonRpcRequest->params ) );

    try
    {
      $response->jsonRpcResponse->result = call_user_func_array( array( $object, $method ), $args );
    }
    catch( \Exception $e )
    {
      $response->jsonRpcResponse->result = null;
      $response->jsonRpcResponse->error = new \Common\JsonRpc\Error();
      $response->jsonRpcResponse->error->code = $e->getCode();
      $response->jsonRpcResponse->error->message = $e->getMessage();
    }
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

