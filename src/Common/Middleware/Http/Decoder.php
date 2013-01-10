<?php

namespace Common\Middleware\Http;

/**
 * Extract all HTTP request headers into the request for other modules to use.
 * In case of abort, generate a meaningful response.
 *
 * @package common
 */
class Decoder implements \Common\Middleware\Listener
{

  /**
   * Debug flag
   *
   * @var bool
   */
  private $_debug;



  /**
   * Constructor
   *
   * @param boolean $debug if true, in case of abort generate a error page with exception information
   */
  public function __construct( $debug = false )
  {
    $this->_debug = $debug;
  }



  /**
   * Get all HTTP Request Headers (if possible)
   *
   * @return string[string]
   */
  private function get_all_http_headers()
  {
    $headers = false;

    if( function_exists( 'getallheaders' ) )
    {
      $headers = getallheaders();
    }

    // Fallback
    if( $headers === false )
    {
      $headers = array();
      foreach( $_SERVER as $name => $value )
      {
        if( substr( $name, 0, 5 ) == 'HTTP_' )
        {
          $headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) ) ] = $value;
        }
      }
    }

    $headers = \Common\Utils\Arrays::array_map_assoc( $headers, function ( $k, $v )
    {
      return array( strtolower( $k ), $v );
    });

    return $headers;
  }


  /**
   * Abort transaction handling, will print out debug info if asked
   *
   * @param $request
   * @param $response
   * @param $exception
   */
  public function abort( &$request, &$response, &$exception )
  {
    header( "HTTP/1.0 500 Internal server error" );

    if( $this->_debug )
    {
      header( 'Content-Type: text/plain' );
      echo "REQUEST\r\n";
      print_r( $request );
      echo "RESPONSE\r\n";
      print_r( $response );
      echo "EXCEPTION\r\n";
      print_r( $exception );
    }
  }



  /**
   * Add all available HTTP headers to the request HTTP_RAW_HEADERS context
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @return void
   */
  public function call( &$request, &$response )
  {
    $request->httpRequest = new Request();
    $response->httpResponse = new Response();
    $request->httpRequest->headers = $this->get_all_http_headers();
    $request->httpRequest->method = $_SERVER[ 'REQUEST_METHOD' ];

    if( !empty( $_GET ) )
    {
      $request->httpRequest->parameters = array_merge( $request->httpRequest->parameters, $_GET );
    }

    if( $request->httpRequest->method == 'POST' )
    {
      if( !empty( $_POST ) )
      {
        $request->httpRequest->parameters = array_merge( $request->httpRequest->parameters, $_POST );
      }
      elseif( $request->httpRequest->get_header( 'Content-length' ) > 0 )
      {
        if( $request->httpRequest->get_header( 'Content-type' ) == '' )
        {
          // WORKAROUND:
          // IE8/IE9 XDomainRequest will send a content-type of text/plain but a x-www-form-urlencoded data payload
          // we have to parse manually.
          $postdata = file_get_contents( "php://input" );
          if( $postdata )
          {
            $request->httpRequest->body = $postdata;

            if( \Common\Utils\Json::is_json( $postdata ) )
            {
              $params = @json_decode( $postdata, true );
              foreach( $params as $key => $value )
              {
                $request->httpRequest->parameters[ $key ] = $value;
              }
            }
            else
            {
              parse_str( $postdata, $request->httpRequest->parameters );
            }
          }
        }
        elseif( $request->httpRequest->get_header( 'Content-type', false ) == 'application/json' )
        {
          $postdata = file_get_contents( "php://input" );
          $request->httpRequest->body = $postdata;
        }
      }
    }

    if( !empty( $_FILES ) )
    {
      $request->httpRequest->files = $_FILES;
    }

    if( isset( $_SERVER[ 'PHP_AUTH_USER' ] ) )
    {
      $request->httpRequest->authentication_user = $_SERVER[ 'PHP_AUTH_USER' ];
      $request->httpRequest->authentication_pass = isset( $_SERVER[ 'PHP_AUTH_PW' ] ) ? $_SERVER[ 'PHP_AUTH_PW' ] : null;
    }

    if( isset ( $_SERVER[ 'HTTPS' ] ) )
    {
      $request->httpRequest->secure = true;
    }
  }

}
