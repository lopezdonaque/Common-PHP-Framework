<?php

namespace CommonTest\Utils;


/**
 * Password strength checker test
 *
 */
class PasswordStrength extends \PHPUnit_Framework_TestCase
{

  /**
   * Test for method "is_valid_strength"
   */
  public function test_is_valid_strength()
  {
    $options = new \Common\Utils\PasswordStrengthOptions();
    $options->letters = false;

    $options->minlength = 3;
    $this->assertTrue( \Common\Utils\PasswordStrength::is_valid_strength( '123', $options ) );
    $this->assertFalse( \Common\Utils\PasswordStrength::is_valid_strength( '12', $options ) );

    $options->maxlength = 6;

    $this->assertTrue( \Common\Utils\PasswordStrength::is_valid_strength( '123456', $options ) );
    $this->assertFalse( \Common\Utils\PasswordStrength::is_valid_strength( '1234567', $options ) );

    $options->uppercase = true;
    $this->assertTrue( \Common\Utils\PasswordStrength::is_valid_strength( 'A12345', $options ) );
    $this->assertFalse( \Common\Utils\PasswordStrength::is_valid_strength( '123456', $options ) );

    $options->lowercase = true;
    $this->assertTrue( \Common\Utils\PasswordStrength::is_valid_strength( 'aA2345', $options ) );
    $this->assertFalse( \Common\Utils\PasswordStrength::is_valid_strength( '123456', $options ) );

    $options->numbers = true;
    $this->assertTrue( \Common\Utils\PasswordStrength::is_valid_strength( 'aA2345', $options ) );
    $this->assertFalse( \Common\Utils\PasswordStrength::is_valid_strength( 'aAxxxx', $options ) );

    $options->symbols = true;
    $this->assertTrue( \Common\Utils\PasswordStrength::is_valid_strength( 'aA2#45', $options ) );
    $this->assertFalse( \Common\Utils\PasswordStrength::is_valid_strength( 'aA2345', $options ) );
  }

}
