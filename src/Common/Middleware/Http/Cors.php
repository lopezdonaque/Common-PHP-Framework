<?php

namespace Common\Middleware\Http;


/**
 * Listener for CORS preflighted requests and CORS GET requests.
 * Will intercept CORS requests and add the needed headers.
 *
 * @package common
 */
class Cors implements \Common\Middleware\Listener
{

  /**
   * Accepted origins (e.g. "*",
   *
   * @var string|array
   */
  private $_accepted_origins;


  /**
   * List of accepted HTTP methods
   *
   * @var string[]
   */
  private $_accepted_methods;


  /**
   * Maximum time (seconds) the CORS response can be cached
   *
   * @var int
   */
  private $_access_control_max_age;


  /**
   * Wether the resource supports user credentials
   *
   * @var boolean
   */
  private $_supports_credentials;



  /**
   * Constructor
   *
   * @param string|array $origins [optional] list of accepted origins. By default '*'
   * @param string[] $methods [optional] list of accepted methods. By default GET or POST
   * @param int $maxage [optional] maximum seconds the preflight request can be cached (20 days default)
   * @param boolean $supports_credentials flag that indicates whether the resource supports user credentials in the request. It is true when the resource does and false otherwise
   */
  public function __construct( $origins = '*', $methods = array( 'GET', 'POST', 'OPTIONS' ), $maxage = 1728000, $supports_credentials = true )
  {
    $this->_accepted_origins = $origins;
    $this->_accepted_methods = $methods;
    $this->_access_control_max_age = $maxage;
    $this->_supports_credentials = $supports_credentials;
  }



  /**
   * If the request is an OPTIONS preflight request, output the needed headers and die.
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @return void
   */
  public function call( &$request, &$response )
  {
    if( $response->fullfilled )
    {
      return;
    }

    $origin = $request->httpRequest->get_header( 'Origin' );

    if( $origin )
    {
      $acmethod = $request->httpRequest->get_header( 'access-control-request-method' );
      $acheaders = $request->httpRequest->get_header( 'access-control-request-headers' );

      if( $request->httpRequest->method == 'OPTIONS' )
      {
        $this->respond_to_preflight( $origin, $acmethod, $acheaders, $response->httpResponse );
        $response->fullfilled = true;
      }
      else
      {
        $this->respond_to_simple_request( $origin, $acmethod, $acheaders, $response->httpResponse );
      }
    }
  }



  /**
   * Respond to a CORS preflight request
   *
   * @see {http://www.w3.org/TR/cors/#resource-preflight-requests}
   * @param string $origin
   * @param string $method
   * @param string $headers
   * @param \Common\Middleware\Http\Response $response
   */
  private function respond_to_preflight( $origin, $method, $headers, &$response )
  {
    if( $this->is_accepted_origin( $origin ) && $method != '' )
    {
      if( in_array( $method, $this->_accepted_methods ) )
      {
        $response->set_header( 'Access-Control-Allow-Origin', $origin );

        if( $this->_supports_credentials )
        {
          $response->set_header( 'Access-Control-Allow-Credentials', 'true' );
        }

        $response->set_header( 'Access-Control-Max-Age', $this->_access_control_max_age );
        $response->set_header( 'Access-Control-Allow-Methods', implode( ', ', $this->_accepted_methods ) );

        if( $headers )
        {
          $response->set_header( 'Access-Control-Allow-Headers', $headers );
        }

        $response->body = '';
      }
    }
  }



  /**
   * Respond to a simple CORS request
   *
   * @see {http://www.w3.org/TR/cors/#resource-requests}
   * @param string $origin incoming request "Origin" header value
   * @param string $method incoming "Access-Control-Request-Method" value
   * @param string $headers incoming " Access-Control-Request-Headers" value
   * @param \Common\Middleware\Http\Response $response
   */
  private function respond_to_simple_request( $origin, $method, $headers, &$response )
  {
    if( $this->is_accepted_origin( $origin ) )
    {
      $response->set_header( 'Access-Control-Allow-Origin', $origin );

      if( $this->_supports_credentials )
      {
        $response->set_header( 'Access-Control-Allow-Credentials', 'true' );
      }

      if( $headers )
      {
        $response->set_header( 'Access-Control-Allow-Headers', $headers );
      }
    }
  }



  /**
   * Split the value of the Origin header on the U+0020 SPACE character and check if any of the resulting tokens is a
   * case-sensitive match for any of the values in list of origins.
   *
   * @param string $origin
   * @return bool
   */
  private function is_accepted_origin( $origin )
  {
    if( $this->_accepted_origins === '*' )
    {
      return true;
    }

    if( !is_array( $this->_accepted_origins ) || count( $this->_accepted_origins ) == 0 )
    {
      return false;
    }

    $origins = mb_split( '\s', $origin );

    foreach( $origins as $orig )
    {
      if( in_array( $orig, $this->_accepted_origins ) )
      {
        return true;
      }
    }

    return false;
  }



  /**
   * Middleware "abort" callback.
   * Will be called if someone aborts the pipeline down the road to allow rolling back or logging.
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception ){}

}
