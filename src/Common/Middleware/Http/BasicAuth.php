<?php

namespace Common\Middleware\Http;


/**
 * Check credentials from a HTTP basic_auth request.
 * An authenticator interface obeying object (@see \Common\Middleware\Authenticator) should be passed.
 * It will check the credentials and tell if those authorize or not the request.
 *
 */
class BasicAuth implements \Common\Middleware\Listener
{

  /**
   * Authenticator object
   *
   * @var \Common\Middleware\Authenticator
   */
  private $_authenticator;


  /**
   * Is this mandatory
   *
   * @var bool
   */
  private $_mandatory = false;


  /**
   * Authentication Realm
   *
   * @var string
   */
  private $_realm;



  /**
   * Constructor
   *
   * @param bool $mandatory
   * @param \Common\Middleware\Authenticator $authenticator
   * @param string $realm
   */
  public function __construct( $mandatory, $authenticator, $realm = "Realm" )
  {
    $this->_mandatory = $mandatory;
    $this->_realm = $realm;
    $this->_authenticator = $authenticator;
  }



  /**
   * Call
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

    $accepted = !$this->_mandatory;

    if( $request->httpRequest->authentication_user )
    {
      $accepted = $this->_authenticator->authenticate( $request );
    }

    if( !$accepted )
    {
      $response->httpResponse = new Response();
      $response->httpResponse->code = 401;
      $response->httpResponse->set_header( 'WWW-Authenticate', 'Basic realm="' . $this->_realm . '"' );
      $response->httpResponse->reason = 'Unauthorized';
      $response->httpResponse->body = 'Unauthorized';
      $request->httpRequest->authenticated = false;
      $response->fullfilled = true;
      return;
    }

    $request->httpRequest->authenticated = true;
  }



  /**
   * Abort
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception ){}

}

