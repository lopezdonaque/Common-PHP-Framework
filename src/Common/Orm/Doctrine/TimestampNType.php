<?php

namespace Common\Orm\Doctrine;


/**
 * Timestamp N type (supports timestamp with N microseconds)
 *
 * PHP: float as timestamp with microseconds
 * Database: timestamp(N)
 *
 */
class TimestampNType extends \Doctrine\DBAL\Types\DateTimeType
{

  /**
   * Name of the type
   *
   * @var string
   */
  const NAME = 'common-timestamp-n';



  /**
   * Converts to PHP value
   *
   * @param string $value
   * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
   * @return float
   * @throws \Doctrine\DBAL\Types\ConversionException
   */
  public function convertToPHPValue( $value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform )
  {
    if( $value === null )
    {
      return $value;
    }

    if( !( $datetime = date_create( $value ) ) ) // Use "date_create" to allow both formats "Y-m-d H:i:s" and "Y-m-d H:i:s.u"
    {
      throw \Doctrine\DBAL\Types\ConversionException::conversionFailedFormat( $value, $this->getName(), 'Y-m-d H:i:s.u' );
    }

    return (float) $datetime->format( 'U.u' );
  }



  /**
   * Converts to database value
   *
   * @param float $value
   * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
   * @return string
   * @throws \Exception
   */
  public function convertToDatabaseValue( $value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform )
  {
    if( $value )
    {
      if( ( $value = \DateTime::createFromFormat( is_float( $value ) ? 'U.u' : 'U', $value ) ) === false )
      {
        throw new \Exception( "Wrong timestamp value [$value]" );
      }
    }

    return ( $value !== null ) ? $value->format( 'Y-m-d H:i:s.u' ) : null;
  }



  /**
   * @return string
   */
  public function getName()
  {
    return self::NAME;
  }

}
