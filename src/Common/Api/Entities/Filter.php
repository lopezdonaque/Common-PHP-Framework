<?php

namespace Common\Api\Entities;


/**
 * Manages filter struct data
 *
 */
class Filter
{

  /**#@+
   * Filter operators constants
   *
   * @var string
   */
  const FL_EQUALS = 'equals';
  const FL_BIGGER = 'bigger';
  const FL_BIGGER_EQUALS = 'bigger_equals';
  const FL_LOWER = 'lower';
  const FL_LOWER_EQUALS = 'lower_equals';
  const FL_CONTAINS = 'contains';
  const FL_CONTAINS_SENSITIVE = 'contains_sensitive';
  const FL_BEGINS = 'begins';
  const FL_ENDS = 'ends';
  const FL_INTEGER_EQUAL = 'integer_equal';
  const FL_IN = 'in';
  const FL_NOT_IN = 'not_in';
  /**#@-*/


  /**
   * Column name
   *
   * @var string
   */
  public $column;


  /**
   * Operator
   *
   * @enum Common\Api\Entities\Filter::FL_.*
   * @var string
   */
  public $operator;


  /**
   * Value
   *
   * @var string
   */
  public $value;

}
