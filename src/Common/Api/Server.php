<?php

namespace Common\Api;


/**
 * Handles Api requests
 *
 * @deprecated Use Middleware components
 */
class Server
{

  /**
   * Return formats constants
   */
  const RETURN_PHP_SERIAL = 'php_serial';
  const RETURN_JSON = 'json';
  const RETURN_JSONP = 'jsonp';
  const RETURN_IFRAME = 'iframe';


  /**
   * Transaction id
   *
   * @var string
   */
  private $_transaction_id;


  /**
   * User data
   *
   * @var user_data
   */
  private $_user_data;


  /**
   * Authentication token
   *
   * @var string
   */
  private $_token;


  /**
   * Entity
   *
   * @var string
   */
  private $_entity;


  /**
   * Method
   *
   * @var string
   */
  private $_method;


  /**
   * Arguments to pass to the entity method
   *
   * @var array
   */
  private $_arguments;


  /**
   * Return format
   *
   * @var string
   */
  private $_return_format;


  /**
   * Callback
   * ONLY used when return_format is jsonp
   *
   * @var string
   */
  private $_callback;


  /**
   * Request object
   *
   * @var \Zend_Controller_Request_Http
   */
  private $_request;


  /**
   * Function to convert entity to php classname
   *
   * @var callback
   */
  private $_classname_formatter;



  /**
   * Constructor
   *
   * @param callback $classname_formatter
   */
  public function __construct( $classname_formatter = null )
  {
    $this->_classname_formatter = $classname_formatter;
  }



  /**
   * Handles request
   *
   * @return void
   */
  public function handle()
  {
    $this->_request = new \Zend_Controller_Request_Http();

    // Set headers for cross-domain requests
    header( 'Access-Control-Allow-Origin: *' );
    header( 'Access-Control-Max-Age: 1728000' );
    header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
    header( 'Access-Control-Allow-Headers: ' . $this->_request->getHeader( 'Access-Control-Request-Headers' ) );

    // Check if it's a preflight request
    if( $this->_request->isOptions() == 'OPTIONS' )
    {
      return;
    }

    $this->_request_parameters();
    $this->_decode_parameters();
    $this->_check_parameters();
    $this->_call();
  }


  /**
   * Check request parameters
   *
   */
  private function _request_parameters()
  {
    $this->_transaction_id = $this->_request->get( 'transaction_id' );
    $this->_token = $this->_request->get( 'token' );
    $this->_user_data = $this->_request->get( 'user_data' );
    $this->_entity = $this->_request->get( 'entity' );
    $this->_method = $this->_request->get( 'method' );
    $this->_arguments = $this->_request->get( 'arguments' );
    $this->_return_format = $this->_request->get( 'return_format' ) ?: $this->_request->get( 'format' );
    $this->_callback = $this->_request->get( 'callback' );
  }



  /**
   * Decode request parameters
   *
   */
  private function _decode_parameters()
  {
    /*if( !$this->_token && !( $this->_user_data = json_decode( $this->_user_data, true ) ) )
    {
      $this->response( new Exception( 'Cannot decode user data' ) );
    }*/

    if( !is_array( $this->_arguments = json_decode( $this->_arguments, true ) ) )
    {
      $this->response( new Exception( 'Cannot decode arguments', Exception::INVALID_ARGUMENT ) );
    }
  }



  /**
   * Checks required parameters
   *
   */
  private function _check_parameters()
  {
    if( !$this->_transaction_id )
    {
      $this->response( new Exception( 'Missing transaction_id in parameters', Exception::CALL_MISSING_TRANSACTION_ID ) );
    }

    /*if( !$this->_token && !$this->_user_data )
    {
      $this->response( new Exception( 'Missing authentication (user_data or token) in parameters' ) );
    }*/

    if( !$this->_entity )
    {
      $this->response( new Exception( 'Missing entity in parameters', Exception::CALL_MISSING_ENTITY ) );
    }

    if( !$this->_method )
    {
      $this->response( new Exception( 'Missing method in parameters', Exception::CALL_MISSING_METHOD ) );
    }

    if( !is_array( $this->_arguments ) )
    {
      $this->response( new Exception( 'Missing arguments in parameters', Exception::CALL_MISSING_ARGUMENTS ) );
    }

    if( !$this->_return_format )
    {
      $this->_return_format = self::RETURN_JSON;
      $this->response( new Exception( 'Missing return_format in parameters', Exception::CALL_MISSING_FORMAT ) );
    }

    if( $this->_return_format == self::RETURN_JSONP && !$this->_callback )
    {
      $this->_return_format = self::RETURN_JSON;
      $this->response( new Exception( 'Missing callback in parameters', Exception::CALL_MISSING_JSONP_CALLBACK ) );
    }
  }



  /**
   * Call api
   *
   */
  private function _call()
  {
    if( $this->_classname_formatter )
    {
      $api_name = call_user_func_array( $this->_classname_formatter, array( $this->_entity ) );
    }
    else
    {
      $api_name = $this->_entity;
    }

    // Check if api class exists
    if( !class_exists( $api_name, true ) )
    {
      $this->response( new Exception( "Api {$this->_entity} not found", Exception::CALL_ENTITY_NOT_FOUND ) );
    }

    //
    $args = \Common\Utils\Arrays::convert_to_arguments( $this->_arguments );

    try
    {
      $api = new $api_name( $this->_token );

      // Check if method exists
      if( !method_exists( $api, $this->_method ) )
      {
        $this->response( new Exception( "Method $this->_method of API {$this->_entity} not found", Exception::CALL_METHOD_NOT_FOUND ) );
      }

      $result = call_user_func_array( array( $api, $this->_method ), $args );
      $this->response( $result );
    }
    catch( \Exception $e )
    {
      $this->response( $e );
    }
  }



  /**
   * Response method
   *
   * @param mixed $object
   */
  public function response( $object )
  {
    // Check if its an exception
    if( is_object( $object ) && ( get_class( $object ) == 'Exception' || is_subclass_of( $object, 'Exception' ) ) )
    {
      $tmp = new \stdClass();
      $tmp->error = true;

      $type = get_class( $object );

      // Check if its an api controlled exception
      if( $type == 'Common\\Api\\Exception' )
      {
        $tmp->code = $object->getCode();
        $tmp->message = $object->getMessage();
      }
      else
      {
        $tmp->code = 0;
        $tmp->message = 'Internal error';

        // Real error for debug
        $tmp->real_error = new \stdClass();
        $tmp->real_error->code = $object->getCode();
        $tmp->real_error->message = $object->getMessage();
      }

      $object = $tmp;
    }

    if( $this->_return_format == self::RETURN_PHP_SERIAL )
    {
      die( serialize( $object ) );
    }

    if( $this->_return_format == self::RETURN_JSON )
    {
      header( 'Content-type: application/json; charset=UTF-8;' );
      die( json_encode( $object ) );
    }

    if( $this->_return_format == self::RETURN_JSONP )
    {
      header( 'Content-Type: text/javascript' );
      die( $this->_request->get( 'callback' ) . '(' . json_encode( $object ) . ');' );
    }

    if( $this->_return_format == self::RETURN_IFRAME )
    {
      header( 'Content-type: text/html; charset=UTF-8;' );

      // JSON_HEX_APOS => All ' are converted to \u0027
      // JSON_HEX_QUOT => All " are converted to \u0022
      $json = json_encode( $object, JSON_HEX_APOS | JSON_HEX_QUOT );
      $json = str_replace( '\\', '\\\\', $json );
      die( "<html><script type=\"text/javascript\"> window.name = '" . $json . "'; </script></html>" );
    }
  }

}
