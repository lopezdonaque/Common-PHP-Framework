<?php

namespace Common\Api\Utils;


/**
 * Abstract class to create list functionality from SQL
 *
 */
abstract class Listing
{

  /**
   * Basic query
   *
   * @var mixed
   */
  protected $_basic_query;


  /**
   * Filters
   *
   * @var array
   */
  protected $_filters;


  /**
   * Search filters
   *
   * @var array
   */
  protected $_search_filters;


  /**
   * Page
   *
   * @var integer
   */
  protected $_page;


  /**
   * Rows per page
   *
   * @var integer
   */
  protected $_rows_per_page;


  /**
   * Sort column
   *
   * @var string
   */
  protected $_sort_column;


  /**
   * Sort type
   *
   * @var string
   */
  protected $_sort_type;



  /**
   * Constructor
   *
   * @param mixed $basic_query
   * @param \Common\Api\Entities\ListOptions $list_options
   */
  public function __construct( $basic_query, $list_options )
  {
    $this->_basic_query = $basic_query;

    if( $list_options )
    {
      $this->_setup_from_list_options( $list_options );
    }
  }



  /**
   * Setup parameters from list_options
   *
   * @param \Common\Api\Entities\ListOptions $list_options
   */
  protected function _setup_from_list_options( $list_options )
  {
    $this->_page = $list_options->page;
    $this->_rows_per_page = $list_options->rows_per_page;
    $this->_sort_column = $list_options->sort_column;
    $this->_sort_type = $list_options->sort_type;
    $this->_filters = $list_options->filters;
    $this->_search_filters = $list_options->search_filters;
  }



  /**
   * Implemented by child class
   */
  public function get_count_query(){}



  /**
   * Implemented by child class
   */
  public function get_list_query(){}

}
