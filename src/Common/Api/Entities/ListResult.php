<?php

namespace Common\Api\Entities;


/**
 * Result object after call a list method
 *
 */
class ListResult
{

  /**
   * Page
   *
   * @var integer
   */
  public $page;


  /**
   * Rows per page
   *
   * @var integer
   */
  public $rows_per_page;


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
   * Total rows
   *
   * @var integer
   */
  public $total_rows;


  /**
   * Items
   *
   * @var array
   */
  public $items;

}
