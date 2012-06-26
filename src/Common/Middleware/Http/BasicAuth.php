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
  private $m_authenticator;

  /**
   * Is this mandatory
   *
   * @var bool
   */
  private $m_mandatory = false;


  /**
   * Authentication Realm
   *
   * @var string
   */
  private $m_realm;


  /**
   * @param boolean $mandatory
   * @param \Common\Middleware\Authenticator $authenticator
   * @param string $realm
   */
  public function __construct( $mandatory, $authenticator, $realm = "Realm" )
  {
    $this->m_mandatory = $mandatory;
    $this->m_realm = $realm;
    $this->m_authenticator = $authenticator;
  }


  /**
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   */
  public function call( &$request, &$response )
  {
    if( $response->fullfilled )
    {
      return;
    }

    $accepted = ! $this->m_mandatory;

    if ( $request->httpRequest->authentication_user )
    {
      $accepted = $this->m_authenticator->authenticate( $request );
    }

    if( !$accepted )
    {
      $response->httpResponse = new Response();
      $response->httpResponse->code = 401;
      $response->httpResponse->set_header( 'WWW-Authenticate', 'Basic realm="' . $this->m_realm . '"' );
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
   * @param Request $request
   * @param Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception )
  {
  }


}

