<?php

namespace Common\Middleware\Api;


/**
 * An extension of \Common\Middleware\Http\Response containing a API call response
 *
 */
class Response extends \Common\Middleware\Response
{

  /**
   * Has the api been executed?
   *
   * @var bool
   */
  public $api_executed = false;


  /**
   * The number of seconds required to execute the Api method
   *
   * @var int
   */
  public $api_execution_time;


  /**
   * Did the API throw an exception?
   *
   * @var \Exception
   */
  public $api_exception;


  /**
   * Real exception throwed
   *
   * @var \Exception
   */
  public $api_real_exception;


  /**
   * Object returned by the api
   *
   * @var mixed
   */
  public $api_return_value;


  /**
   * Instance of the api class
   *
   * @var mixed
   */
  public $api_instance;

}
