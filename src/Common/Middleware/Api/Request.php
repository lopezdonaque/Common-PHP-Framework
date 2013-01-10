<?php

namespace Common\Middleware\Api;


/**
 * A middleware request with special fields for API calls
 *
 */
class Request extends \Common\Middleware\Request
{

  /**
   * Return format constants
   */
  const RETURN_PHP_SERIAL = 'php_serial';
  const RETURN_JSON = 'json';


  /**
   * Transports constants
   */
  const TRANSPORT_JSONP = 'jsonp';
  const TRANSPORT_IFRAME = 'iframe';


  /**
   * Could we decode a full request?
   *
   * @var boolean
   */
  public $is_request_present = false;


  /**
   * API entity, sans "api_" prefix
   *
   * @var string
   */
  public $api_entity;


  /**
   * API method
   *
   * @var string
   */
  public $api_method;


  /**
   * Arguments for the call
   *
   * @var mixed[]
   */
  public $api_arguments;


  /**
   * API access token
   *
   * @var string
   */
  public $api_token;


  /**
   * Calling user credentials
   *
   * @var \user_data
   */
  public $api_user_data;


  /**
   * Transaction id
   *
   * @var string
   */
  public $api_transaction_id;


  /**
   * Expected response return format. One of RETURN values
   *
   * @var string
   */
  public $api_return_format;


  /**
   * Request transport. One of TRANSPORT values
   *
   * @var string
   * @see \Common\Middleware\Api\Request::TRANSPORT_* constants
   */
  public $api_transport;


  /**
   * Callback name for JSONP requests.
   * Used if the transport is "jsonp".
   *
   * @var string
   */
  public $api_callback;

}
