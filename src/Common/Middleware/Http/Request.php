<?php

namespace Common\Middleware\Http;

/**
 * Container for the HTTP request. Can be subclassed to use for specific
 * request pipelines.
 *
 * @package common
 */
class Request
{

  /**
   * Parsed HTTP method
   *
   * @var string
   */
  public $method;


  /**
   * Parsed HTTP headers. Headers are indexed by LOWERCASE field-name.
   * If multiple values for a header exist, those are returned as an array in the order received.
   *
   * @var string[string]|array
   */
  public $headers = [];


  /**
   * GET and POST parameters, combined.
   * Case of the field name is preserved.
   *
   * @var string[string]
   */
  public $parameters = [];


  /**
   * Posted/Putted files (a clone of php $_FILES)
   *
   * @var array
   */
  public $files = null;


  /**
   * HTTP request body, in case of a HTTP POST/PUT
   *
   * @var string
   */
  public $body;


  /**
   * HTTP Basic-Authentication user
   *
   * @var string
   */
  public $authentication_user;


  /**
   * HTTP Basic-Authentication password.
   *
   * @var string
   */
  public $authentication_pass;


  /**
   * Has the request been authenticated?
   *
   * @var boolean
   */
  public $authenticated = false;


  /**
   * Are we using a SSL/TLS/encrypted channel?
   *
   * @var boolean
   */
  public $secure = false;



  /**
   * Get a HTTP request header. A case-insensitive match is performed.
   * The header-name is expected without ending semicolon.
   *
   * @param string $header_name
   * @param bool $with_arguments if false, do not return arguments after a ';' character
   * @return string the header or null if not found
   */
  public function get_header( $header_name, $with_arguments = true )
  {
    $header_name = strtolower( $header_name );
    $header_value = null;

    if( isset( $this->headers[ $header_name ] ) )
    {
      $header_value = $this->headers[ $header_name ];

      if( !$with_arguments )
      {
        list( $header_value, ) = explode( ';', $header_value, 2 );
      }
    }

    return $header_value;
  }

}
