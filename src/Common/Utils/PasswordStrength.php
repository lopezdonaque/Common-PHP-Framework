<?php

namespace Common\Utils;


/**
 * Password strength checker
 *
 */
class PasswordStrength
{

  /**
   * Check if a password strength is valid by given options
   *
   * @param string $password
   * @param PasswordStrengthOptions $options
   * @return bool
   */
  public static function is_valid_strength( $password, \Common\Utils\PasswordStrengthOptions $options )
  {

    // Check min length
    if( strlen( $password ) < $options->minlength )
    {
      return false;
    }

    // Check max length
    if( strlen( $password ) > $options->maxlength )
    {
      return false;
    }

    // Check letters
    if( $options->letters && !preg_match( "#[aA-zZ]+#", $password ) )
    {
      return false;
    }

    // Check uppercase
    if( $options->uppercase && !preg_match( "#[A-Z]+#", $password ) )
    {
      return false;
    }

    // Check lowercase
    if( $options->lowercase && !preg_match( "#[a-z]+#", $password ) )
    {
      return false;
    }

    // Check numbers
    if( $options->numbers && !preg_match( "#[0-9]+#", $password ) )
    {
      return false;
    }

    // Check symbols
    if( $options->symbols && !preg_match( "#\\W+#", $password ) )
    {
      return false;
    }

    return true;
  }

}
