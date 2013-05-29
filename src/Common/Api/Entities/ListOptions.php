<?php

namespace Common\Api\Entities;


/**
 * Object options to send when a list method is called
 *
 */
class ListOptions
{

  /**
   * Page
   *
   * @var integer
   */
  public $page = 1;


  /**
   * Rows per page
   *
   * @var integer
   */
  public $rows_per_page = 10;


  /**
   * Sort column
   *
   * @var string
   */
  public $sort_column;


  /**
   * Sort type
   *
   * @var string
   */
  public $sort_type;


  /**
   * Filters
   *
   * @var filter[]
   */
  public $filters = array();


  /**
   * Search filters
   *
   * @var search_filter[]
   */
  public $search_filters = array();

}
