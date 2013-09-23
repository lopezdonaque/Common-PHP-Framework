<?php

namespace Common\Orm\Doctrine;


/**
 * DateTime class to allow use "datetime" as primeray key with Doctrine 2
 *
 */
class DateTimeExtended extends \DateTime
{
  public function __toString()
  {
    return $this->format( 'U' );
  }
}
