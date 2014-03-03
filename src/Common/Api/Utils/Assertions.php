<?php

namespace Common\Api\Utils;


/**
 * Assertion methods
 *
 */
class Assertions
{

  /**
   * Check an assertion, throw an error if it fails
   *
   * @param boolean $assertion
   * @param string $message
   * @param integer $code
   * @throws \Common\Api\Exception
   */
  public static function assert( $assertion, $message, $code = \Common\Api\ExceptionCodes::GENERAL_INTERNAL_ERROR )
  {
    if( !$assertion )
    {
      throw new \Common\Api\Exception( $message, $code );
    }
  }



  /**
   * Check if a mail address is valid
   *
   * @param string $email
   * @param string $message in case of invalid
   * @throws \Common\Api\Exception
   */
  public static function assert_is_valid_email( $email, $message = 'Invalid e-mail' )
  {
    self::assert( filter_var( $email, FILTER_VALIDATE_EMAIL ), $message, \Common\Api\ExceptionCodes::GENERAL_INVALID_ARGUMENT );
  }

}
