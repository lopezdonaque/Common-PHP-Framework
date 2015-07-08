<?php

namespace Common\Orm\Doctrine;


/**
 * Timestamp type
 *
 * PHP: int as timestamp
 * Database: timestamp
 *
 */
class TimestampType extends \Doctrine\DBAL\Types\DateTimeType
{

  /**
   * Name of the type
   *
   * @var string
   */
  const NAME = 'common-timestamp';



  /**
   * Converts to PHP value
   *
   * @param string $value
   * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
   * @return int
   */
  public function convertToPHPValue( $value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform )
  {
    $datetime = parent::convertToPHPValue( $value, $platform );

    if( !$datetime )
    {
      return $datetime;
    }

    return (int) $datetime->format( 'U' );
  }



  /**
   * Converts to database value
   *
   * @param int $value
   * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
   * @return string
   */
  public function convertToDatabaseValue( $value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform )
  {
    if( $value )
    {
      $value = \DateTime::createFromFormat( 'U', $value );
    }

    return parent::convertToDatabaseValue( $value, $platform );
  }



  /**
   * @return string
   */
  public function getName()
  {
    return self::NAME;
  }

}
