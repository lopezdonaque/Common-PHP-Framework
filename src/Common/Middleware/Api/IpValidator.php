<?php

namespace Common\Middleware\Api;


/**
 * Middleware to validate remote IP
 *
 */
class IpValidator implements \Common\Middleware\Listener
{

  /**
   * Config
   *
   * @var array
   */
  private $_config;



  /**
   * Constructor
   *
   * @param array $config
   */
  public function __construct( array $config )
  {
    $this->_config = $config;
  }



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

    $is_private_ip = !filter_var( $_SERVER[ 'REMOTE_ADDR' ], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE );
    $is_reserved_ip = !filter_var( $_SERVER[ 'REMOTE_ADDR' ], FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE );
    $allowed_remote_ips = $this->_config[ 'allowed' ];

    if( !( $is_private_ip || $is_reserved_ip || in_array( $_SERVER[ 'REMOTE_ADDR' ], $allowed_remote_ips ) ) )
    {
      $response->api_exception = new \Common\Api\Exception( 'Unauthorized', \Common\Api\ExceptionCodes::AUTHENTICATION_ACCESS_NOT_ALLOWED );
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
