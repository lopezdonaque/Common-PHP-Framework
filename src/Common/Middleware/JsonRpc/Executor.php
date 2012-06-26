<?php

namespace Common\Middleware\JsonRpc;


/**
 * A JSON-RPC executor. If passed an object, that will the one used to call its "method" as told by the JsonRpc\Request
 *
 * @author Androme Iberica 2011
 * @package common
 */
class Executor implements \Common\Middleware\Listener
{

  /**
   * Handler object for the request.
   *
   * @var object
   */
  private $m_handler;



  /**
   * Constructor
   *
   * @param string $handler
   */
  public function __construct( $handler = null )
  {
    $this->m_handler = $handler;
  }



  /**
   * Middleware "call" callback
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @return void
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
    if( strpos( $request->jsonRpcRequest->method, '.' ) !== false )
    {
      list ( $object, $method ) = explode( '.', $request->jsonRpcRequest->method, 2 );
      if( $this->m_handler )
      {
        $object = $this->m_handler;
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
      $object = $this->m_handler;
    }

    if( !$object || !is_callable( array( $object, $method ) ) )
    {
      $response->jsonRpcResponse->result = null;
      $response->jsonRpcResponse->error = new \Common\JsonRpc\Error();
      $response->jsonRpcResponse->error->code = \Common\JsonRpc\Error::METHOD_NOT_FOUND;
      $response->jsonRpcResponse->error->message = "Method not found";
      return;
    }

    $args = \Common\Utils\Arrays::convert_to_arguments( $request->jsonRpcRequest->params );

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
   * middleware "abort" callback. will be called if someone aborts the
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

