<?php

namespace Common\Orm\Doctrine;


/**
 * DateTime type to allow use "datetime" as primeray key with Doctrine 2
 *
 * See: http://stackoverflow.com/questions/15080573/doctrine-2-orm-datetime-field-in-identifier
 *
 */
class DateTimeExtendedType extends \Doctrine\DBAL\Types\DateTimeType
{

  /**
   * Name of the type
   *
   * @var string
   */
  const NAME = 'common-datetimeextended';



  /**
   * Converts to PHP value
   *
   * @param string $value
   * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
   * @return DateTimeExtended
   */
  public function convertToPHPValue( $value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform )
  {
    $datetime = parent::convertToPHPValue( $value, $platform );

    if( !$datetime )
    {
      return $datetime;
    }

    return new DateTimeExtended( '@' . $datetime->format( 'U' ) );
  }



  /**
   * @return string
   */
  public function getName()
  {
    return self::NAME;
  }

}
