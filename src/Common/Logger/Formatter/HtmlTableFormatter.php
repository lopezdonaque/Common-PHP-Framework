<?php

namespace Common\Logger\Formatter;


/**
 * Formats records as HTML table
 *
 */
class HtmlTableFormatter extends \Monolog\Formatter\NormalizerFormatter
{


  /**
   * Options
   *
   * @var array
   */
  private $_options = [];



  /**
   * Constructor
   *
   * @param array $options
   */
  public function __construct( $options = [] )
  {
    $this->_options = $options;
    parent::__construct();
  }



  /**
   * Format
   *
   * @param array $record
   * @return array|string
   */
  public function format( array $record )
  {
    $clean_record = \Common\Utils\Arrays::object_to_array( \Doctrine\Common\Util\Debug::export( $record, @$this->_options[ 'maxDepth' ] ?: 8 ) );
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
    return implode( '<br><br>', array_map( [ $this, 'format' ], $records ) );
  }

}
