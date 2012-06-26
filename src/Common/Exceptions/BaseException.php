<?php

namespace Common\Exceptions;


/**
 * Base class to create exceptions with extra data
 *
 */
class BaseException extends \Exception
{

  /**
   * Stores extra data
   *
   * @var mixed
   */
  private $_data;



  /**
   * Constructor
   *
   * @param string $message
   * @param int $code
   * @param mixed $data
   */
  public function __construct( $message, $code = null, $data = null )
  {
    parent::__construct( $message, $code );
    $this->_data = $data;
  }



  /**
   * Returns data
   *
   * @return mixed
   */
  public function getData()
  {
    return $this->_data;
  }

}
