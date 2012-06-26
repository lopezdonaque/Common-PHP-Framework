<?php

namespace Common\Api;


/**
 * Api exception
 *
 */
class Exception extends \Exception
{

  /**
   * Constructor
   *
   * @param string $message
   * @param int $code
   */
  public function __construct( $message, $code = \Common\Api\ExceptionCodes::GENERAL_INTERNAL_ERROR )
  {
    parent::__construct( $message, $code );
  }

}
