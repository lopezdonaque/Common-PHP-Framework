<?php

class FakeAuthenticator implements \Common\Middleware\Authenticator
{

  /**
   * Check the passed credentials in the request to authenticate
   *
   * @param \Common\Middleware\Request $request
   * @return boolean true if authenticated, false if not
   */
  public function authenticate( $request )
  {
    if( $request->httpRequest->authentication_user == 'user' )
    {
      if( $request->httpRequest->authentication_pass == 'right' )
      {
        return true;
      }
    }
    return false;
  }
}


