<?php

namespace Common\Logger\Formatter;


/**
 * Formats records as HTML table
 *
 */
class HtmlTableFormatter extends \Monolog\Formatter\NormalizerFormatter
{

  /**
   * Format
   *
   * @param array $record
   * @return array|string
   */
  public function format( array $record )
  {
    $clean_record = \Common\Utils\Arrays::object_to_array( \Doctrine\Common\Util\Debug::export( $record, 5 ) );
    return \Common\Utils\Arrays::get_html( $clean_record );
  }



  /**
   * Format batch
   *
   * @param array $records
   * @return string
   */
  public function formatBatch( array $records )
  {
    return implode( '<br><br>', array_map( array( $this, 'format' ), $records ) );
  }

}