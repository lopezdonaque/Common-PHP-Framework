<?php

namespace Common\Middleware\Api;


/**
 * Converts the api name to the php class name using given formatter function.
 *
 */
class ApiToClassNameFinder implements \Common\Middleware\Listener
{

  /**
   * Formatter
   *
   * @var callable
   */
  private $_formatter;



  /**
   * Create the JSON/RPC extractor
   *
   * @param callable $formatter
   */
  public function __construct( $formatter )
  {
    $this->_formatter = $formatter;
  }



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

    $request->api_class_name = call_user_func_array( $this->_formatter, array( $request->api_entity ) );
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
