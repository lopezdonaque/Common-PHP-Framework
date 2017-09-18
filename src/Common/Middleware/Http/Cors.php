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
   * Options
   *
   * @var \Common\Middleware\Http\CorsOptions
   */
  private $_options;



  /**
   * Constructor
   *
   * @param array|\Common\Middleware\Http\CorsOptions $options
   */
  public function __construct( $options = [] )
  {
    $this->_options = is_array( $options ) ? new CorsOptions( $options ) : $options;
  }



  /**
   * If the request is an OPTIONS preflight request, output the needed headers and die.
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
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
        $this->_respond_to_preflight( $origin, $acmethod, $acheaders, $response->httpResponse );
        $response->fullfilled = true;
      }
      else
      {
        $this->_respond_to_simple_request( $origin, $acmethod, $acheaders, $response->httpResponse );
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
  private function _respond_to_preflight( $origin, $method, $headers, &$response )
  {
    if( $this->_is_accepted_origin( $origin ) && $method != '' )
    {
      if( in_array( $method, $this->_options->methods ) )
      {
        $response->set_header( 'Access-Control-Allow-Origin', $origin );

        if( $this->_options->supports_credentials )
        {
          $response->set_header( 'Access-Control-Allow-Credentials', 'true' );
        }

        $response->set_header( 'Access-Control-Max-Age', $this->_options->maxage );
        $response->set_header( 'Access-Control-Allow-Methods', implode( ', ', $this->_options->methods ) );

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
  private function _respond_to_simple_request( $origin, $method, $headers, &$response )
  {
    if( $this->_is_accepted_origin( $origin ) )
    {
      $response->set_header( 'Access-Control-Allow-Origin', $origin );

      if( $this->_options->supports_credentials )
      {
        $response->set_header( 'Access-Control-Allow-Credentials', 'true' );
      }

      if( $headers )
      {
        $response->set_header( 'Access-Control-Allow-Headers', $headers );
      }

      if( $this->_options->expose_headers )
      {
        $response->set_header( 'Access-Control-Expose-Headers', $this->_options->expose_headers );
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
  private function _is_accepted_origin( $origin )
  {
    if( $this->_options->origins === '*' )
    {
      return true;
    }

    if( !is_array( $this->_options->origins ) || count( $this->_options->origins ) == 0 )
    {
      return false;
    }

    $origins = mb_split( '\s', $origin );

    foreach( $origins as $orig )
    {
      if( in_array( $orig, $this->_options->origins ) )
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
