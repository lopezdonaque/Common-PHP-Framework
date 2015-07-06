<?php

namespace Common\Middleware\Api;


/**
 * Middleware to validate API request parameters
 *
 */
class ApiValidator implements \Common\Middleware\Listener
{

  /**
   * Validates required arguments before Api execution.
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   */
  public function call( &$request, &$response )
  {
    if( $response->fullfilled || $response->api_exception )
    {
      return;
    }

    $api_name = $request->api_class_name;

    // Check if api class exists
    if( !class_exists( $api_name, true ) )
    {
      $response->api_exception = new \Common\Api\Exception( "API [{$request->api_entity}] not found", \Common\Api\ExceptionCodes::CALL_ENTITY_NOT_FOUND );
      return;
    }

    // Check if method exists
    if( !method_exists( $api_name, $request->api_method ) )
    {
      $response->api_exception = new \Common\Api\Exception( "Method [{$request->api_method}] of API [{$request->api_entity}] not found", \Common\Api\ExceptionCodes::CALL_METHOD_NOT_FOUND );
      return;
    }

    // Check number of required parameters
    $method_reflection = new \ReflectionMethod( $api_name, $request->api_method );
    if( count( $request->api_arguments ) < $method_reflection->getNumberOfRequiredParameters() )
    {
      $response->api_exception = new \Common\Api\Exception( "Wrong number of required arguments for method [{$request->api_method}] of API [{$request->api_entity}].", \Common\Api\ExceptionCodes::CALL_MISSING_ARGUMENTS );
      return;
    }
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
