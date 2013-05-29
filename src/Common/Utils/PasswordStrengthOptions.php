<?php

namespace Common\Utils;


/**
 * Password strength options
 *
 */
class PasswordStrengthOptions
{

  /**
   * Min lenght
   *
   * @var int
   */
  public $minlength = 6;


  /**
   * Max lenght
   *
   * @var int
   */
  public $maxlength = 20;


  /**
   * Must contains letters
   *
   * @var bool
   */
  public $letters = true;


  /**
   * Must contains uppercase characters
   *
   * @var bool
   */
  public $uppercase = false;


  /**
   * Must contains lowercase characters
   *
   * @var bool
   */
  public $lowercase = false;


  /**
   * Must contains numbers
   *
   * @var bool
   */
  public $numbers = false;


  /**
   * Must contains symbols
   *
   * @var bool
   */
  public $symbols = false;

}
