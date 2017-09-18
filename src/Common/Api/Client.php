<?php

namespace Common\Api;


/**
 * Api client
 *
 * Usage:
 *
 * - Configure URL to use in all the requests
 *     \Common\Api\Client::$URL = 'http://domain.com/api';
 *
 * - Configure token to use in all the requests
 *     \Common\Api\Client::$TOKEN = 'foo';
 *
 * - Usage:
 *     $client = new \Common\Api\Client( 'login', 'login_user', array( 'user', 'password' ) );
 *     $result = $client->request();
 *
 * - Usage with specific URL:
 *     $client = new \Common\Api\Client( 'login', 'login_user', array( 'user', 'password' ) );
 *     $client->url = 'http://api.domain.com';
 *     $result = $client->request();
 *
 */
class Client
{

  /**
   * PHP serial return format (Api public entities are required)
   *
   * @var string
   */
  const RETURN_PHP_SERIAL = 'php_serial';


  /**
   * JSON return format
   *
   * @var string
   */
  const RETURN_JSON = 'json';


  /**
   * Api endpoint URL to use in all the Client instances
   *
   * @var string
   */
  public static $URL;


  /**
   * Token for authentication
   *
   * @var string
   */
  public static $TOKEN;


  /**
   * Transaction id
   *
   * @var string
   */
  public $transaction_id;


  /**
   * URL
   *
   * @var string
   */
  public $url;


  /**
   * Entity
   *
   * @var string
   */
  public $entity;


  /**
   * Method
   *
   * @var string
   */
  public $method;


  /**
   * Arguments
   *
   * @var array
   */
  public $arguments;


  /**
   * Files
   *
   * @var array
   */
  public $files;


  /**
   * Return format
   *
   * @var string
   */
  public $return_format = self::RETURN_JSON;


  /**
   * Token
   *
   * @var string
   */
  public $token;


  /**
   * Customer data (login and password)
   *
   * @var string[string]
   */
  public $customer_data;



  /**
   * Constructor
   *
   * @param string $entity
   * @param string $method
   * @param array $arguments
   * @param array $files
   */
  public function __construct( $entity, $method, $arguments = [], $files = [] )
  {
    $this->transaction_id = uniqid( $entity . $method . ':' );
    $this->entity = $entity;
    $this->method = $method;
    $this->arguments = $arguments;
    $this->files = $files;
  }



  /**
   * Do request
   *
   * @return object
   */
  public function request()
  {
    $curl_resource = $this->get_curl_resource();
    $response_text = curl_exec( $curl_resource ); // Response text could be json, phpserial or invalid response (if php prints fatal, notice, etc.)
    curl_close( $curl_resource );

    $result =  $this->process_response( $response_text );
    return $result;
  }



  /**
   * Returns the curl resource to retrieve data.
   * This could be used for "curl_multi".
   *
   * @return resource
   */
  public function get_curl_resource()
  {
    // Initialize curl object and make the call
    $curl_resource = curl_init();

    curl_setopt( $curl_resource, CURLOPT_URL, self::get_builded_url( $this->url ?: self::$URL ) );
    curl_setopt( $curl_resource, CURLOPT_HEADER, false );
    curl_setopt( $curl_resource, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl_resource, CURLOPT_POST, true );
    curl_setopt( $curl_resource, CURLOPT_POSTFIELDS, $this->_get_arguments_for_curl() );
    curl_setopt( $curl_resource, CURLOPT_SSL_VERIFYPEER, false ); // Disables SSL Certificate validation

    if( isset( $_SERVER[ 'REMOTE_ADDR' ] ) )
    {
      curl_setopt( $curl_resource, CURLOPT_HTTPHEADER, [ 'X-Forwarded-For:' . $_SERVER[ 'REMOTE_ADDR' ] ] );
    }

    if( $this->customer_data )
    {
      curl_setopt( $curl_resource, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
      curl_setopt( $curl_resource, CURLOPT_USERPWD, $this->customer_data[ 'login' ] . ':' . $this->customer_data[ 'password' ] );
    }

    return $curl_resource;
  }



  /**
   * Returns the arguments for the curl resource options
   *
   * @return string[string]
   */
  private function _get_arguments_for_curl()
  {
    $arguments = [];
    $arguments[ 'transaction_id' ] = $this->transaction_id;
    $arguments[ 'entity' ] = $this->entity;
    $arguments[ 'method' ] = $this->method;
    $arguments[ 'arguments' ] = json_encode( $this->arguments );
    $arguments[ 'return_format' ] = $this->return_format;

    // Set authentication
    if( $this->token || self::$TOKEN )
    {
      $arguments[ 'token' ] = $this->token ?: self::$TOKEN;
    }

    // Set files
    if( is_array( $this->files ) )
    {
      foreach( $this->files as $key => $file )
      {
        $arguments[ $key ] = '@' . $file;
      }
    }

    return $arguments;
  }



  /**
   * Process the response text from the request
   *
   * @param string $response_text
   * @return object
   * @throws \Exception
   */
  public function process_response( $response_text )
  {
    // Check if the connection fails (for example, when the URL not exists or is invalid)
    if( $response_text === false )
    {
      throw new \Exception( "Unable to connect to Api. [URL={$this->url}]" );
    }

    if( $this->return_format == self::RETURN_PHP_SERIAL )
    {
      $data_object = @unserialize( trim( $response_text ) );

      if( $data_object === false && trim( $response_text ) != 'b:0;' )
      {
        throw new \Exception( "Cannot parse phpserial retrieved data from api call. Transaction id: $this->transaction_id. Response text: $response_text. Arguments: " . json_encode( $this->_get_arguments_for_curl() ), 0 );
      }
    }
    else
    {
      if( $response_text == 'null' )
      {
        return null;
      }

      $data_array = json_decode( $response_text, true );

      if( $data_array === null )
      {
        throw new \Exception( "Cannot parse json retrieved data from api call. Transaction id: $this->transaction_id. Response text: $response_text", 0 );
      }

      $data_object = \Common\Utils\Arrays::array_to_object( $data_array, false );
    }

    if( isset( $data_object->error ) )
    {
      throw new \Exception( $data_object->message, $data_object->code );
    }

    return $data_object;
  }



  /**
   * Returns builded URL
   *
   * @param string $url
   * @return string
   */
  public static function get_builded_url( $url )
  {
    if( \Common\Utils\Cookies::get( 'start_xdebug_forward' ) && \Common\Utils\Cookies::get( '_XDEBUG_SESSION' ) )
    {
      $debug_params = [ 'XDEBUG_SESSION_START' => \Common\Utils\Cookies::get( '_XDEBUG_SESSION' ) ];
      return $url . '?' . http_build_query( $debug_params );
    }

    if( \Common\Utils\Cookies::get( 'start_debug_forward' ) )
    {
      $debug_params = array_flip( [ 'debug_fastfile', 'debug_host', 'use_remote', 'debug_port', 'debug_stop', 'original_url', 'debug_start_session', 'debug_session_id', 'send_debug_header', 'send_sess_end', 'debug_jit' ] );
      array_walk( $debug_params, function( &$item, $key ){ $item = \Common\Utils\Cookies::get( $key ); } );
      $debug_params[ 'start_debug' ] = 1;
      return $url . '?' . http_build_query( $debug_params );
    }

    return $url;
  }

}
