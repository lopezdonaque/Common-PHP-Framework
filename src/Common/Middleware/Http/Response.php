<?php

namespace Common\Middleware\Http;

/**
 * An HTTP response container to pass through the middleware stack and to be printed out by the HTTP\Encoder
 *
 */
class Response
{

  /**
   * Response code, as defined by HTTP RFCs
   *
   * @var int
   */
  public $code;


  /**
   * Response reason, appended to the HTTP code
   *
   * @var string
   */
  public $reason;


  /**
   * Response headers
   *
   * @var string[]
   */
  public $headers = array();


  /**
   * Response body
   *
   * @var string
   */
  public $body;



  /**
   * Set a HTTP response header.
   *
   * @param string $name
   * @param string $value
   */
  public function set_header( $name, $value )
  {
    $this->headers[ $name ] = $value;
  }



  /**
   * Get a HTTP response header. A case-insensitive match is performed.
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

    foreach( $this->headers as $header => $value )
    {
      if( $header_name == strtolower($header) )
      {
        $header_value = $value;
      }
    }

    if( $header_value && ! $with_arguments )
    {
      list( $header_value, ) = explode( ';', $header_value, 2 );
    }

    return $header_value;
  }

}

