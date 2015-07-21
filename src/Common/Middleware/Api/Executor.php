<?php

namespace Common\Middleware\Api;


/**
 * Middleware to execute API requests
 *
 */
class Executor implements \Common\Middleware\Listener
{

  /**
   * Execute the parsed API request (if available).
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   */
  public function call( &$request, &$response )
  {
    if( $response->fullfilled || $response->api_exception || $response->api_executed )
    {
      return;
    }

    $api_name = $request->api_class_name;
    $starttime = time();

    try
    {
      $api_instance = $response->api_instance = new $api_name();
      $response->api_return_value = call_user_func_array( array( $api_instance, $request->api_method ), $request->api_arguments );
    }
    catch( \Common\Api\Exception $e ) // Capture controlled Api exceptions
    {
      $response->api_real_exception = $e;
      $response->api_exception = $e;
    }
    catch( \Exception $e ) // Any other exception must return an internal error (with a unique id to identify it)
    {
      $response->api_real_exception = $e;
      $response->api_exception = new \Common\Api\Exception( 'Internal error - ' . uniqid(), \Common\Api\ExceptionCodes::GENERAL_INTERNAL_ERROR );
    }

    $response->api_executed = true;
    $response->api_execution_time = time() - $starttime;
  }



  /**
   * Middleware "abort" callback.
   * Will be called if someone aborts the pipeline down the road to allow rolling back or logging.
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception ){}

}
