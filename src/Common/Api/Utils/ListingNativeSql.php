<?php

namespace Common\Api\Utils;


/**
 * Class to retrieve results list
 *
 */
class ListingNativeSql extends Listing
{

  /**
   * Constructor
   *
   * @param string $basic_query
   * @param \Common\Api\Entities\ListOptions $list_options
   */
  public function __construct( $basic_query, $list_options )
  {
    parent::__construct( $basic_query, $list_options );
  }



  /**
   * Returns list query
   *
   * @return string
   */
  public function get_list_query()
  {
    // Prepare the query to get rows
    $list_query = "SELECT * FROM ( $this->_basic_query ) AS A WHERE 1 = 1 ";

    // Filters
    if( is_array( $this->_filters ) && count( $this->_filters ) > 0 )
    {
      $filters = $this->_get_filters_expression( $this->_filters );
      $list_query .= " AND ( $filters ) ";
    }

    // Search filters
    if( is_array( $this->_search_filters ) && count( $this->_search_filters ) > 0 )
    {
      $search_filters = $this->_get_search_filters_expression( $this->_search_filters );
      $list_query .= " AND ( $search_filters ) ";
    }

    // Sort
    if( $this->_sort_column && $this->_sort_type )
    {
      $list_query .= " ORDER BY $this->_sort_column $this->_sort_type";
    }

    // Limit
    if( $this->_page && $this->_rows_per_page )
    {
      $offset = ( $this->_page - 1 ) *  $this->_rows_per_page;
      $list_query .= " OFFSET $offset ";
      $list_query .= " LIMIT $this->_rows_per_page ";
    }

    return $list_query;
  }



  /**
   * Returns count query
   *
   * @return string
   */
  public function get_count_query()
  {
    $count_query = "SELECT count(*) FROM ( $this->_basic_query ) AS A WHERE 1 = 1 ";

    // Filters
    if( is_array( $this->_filters ) && count( $this->_filters ) > 0 )
    {
      $filters = $this->_get_filters_expression( $this->_filters );
      $count_query .= " AND ( $filters ) ";
    }

    // Search filters
    if( is_array( $this->_search_filters ) && count( $this->_search_filters ) > 0 )
    {
      $search_filters = $this->_get_search_filters_expression( $this->_search_filters );
      $count_query .= " AND ( $search_filters ) ";
    }

    return $count_query;
  }



  /**
   * Returns filters as SQL expression
   *
   * @param array $filters
   * @return string
   */
  private function _get_filters_expression( $filters )
  {
    $conditions = array();

    foreach( $filters as $filter )
    {
      $conditions[] = $this->_get_filter_expression( $filter );
    }

    $res = implode( ' AND ' , $conditions );
    return $res;
  }



  /**
   * Returns search filters as SQL expression
   *
   * @param array $filters
   * @return string
   */
  private function _get_search_filters_expression( $filters )
  {
    $conditions = array();

    foreach( $filters[ 0 ]->filters as $filter )
    {
      $conditions[] = $this->_get_filter_expression( $filter );
    }

    $res = implode( ' OR ' , $conditions );
    return $res;
  }



  /**
   * Returns filter as SQL expression
   *
   * @param \Common\Api\Entities\Filter $filter
   * @return string
   * @throws \Exception
   */
  private function _get_filter_expression( $filter )
  {
    $filter_name = $filter->column;
    $filter_value = $filter->value;

    switch( $filter->operator )
    {
      case \Common\Api\Entities\Filter::FL_EQUALS:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " UPPER( $filter_name::VARCHAR ) SIMILAR TO UPPER( '$filter_value'::VARCHAR ) ";

      case \Common\Api\Entities\Filter::FL_NOT_EQUALS:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " UPPER( $filter_name::VARCHAR ) NOT SIMILAR TO UPPER( '$filter_value'::VARCHAR ) ";

      case \Common\Api\Entities\Filter::FL_BEGINS:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " $filter_name::VARCHAR ILIKE '$filter_value%'::VARCHAR ";

      case \Common\Api\Entities\Filter::FL_ENDS:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " $filter_name::VARCHAR ILIKE '%$filter_value'::VARCHAR ";

      case \Common\Api\Entities\Filter::FL_CONTAINS:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " $filter_name::VARCHAR ILIKE '%$filter_value%'::VARCHAR ";

      case \Common\Api\Entities\Filter::FL_BIGGER:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " $filter_name::VARCHAR > '$filter_value'::VARCHAR ";

      case \Common\Api\Entities\Filter::FL_BIGGER_EQUALS:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " $filter_name::VARCHAR >= '$filter_value'::VARCHAR ";

      case \Common\Api\Entities\Filter::FL_LOWER:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " $filter_name::VARCHAR < '$filter_value'::VARCHAR ";

      case \Common\Api\Entities\Filter::FL_LOWER_EQUALS:
        $filter_value = self::convert_to_db_format( $filter_value );
        return " $filter_name::VARCHAR <= '$filter_value'::VARCHAR ";

      case \Common\Api\Entities\Filter::FL_NOT_IN:
        $filter_value = is_array( $filter_value ) ? implode( ',', $filter_value ) : $filter_value;
        $filter_value = self::convert_to_db_format( $filter_value ); // Expects e.g."1,3,6"
        return " $filter_name NOT IN ( $filter_value ) ";

      case \Common\Api\Entities\Filter::FL_IN:
        $filter_value = is_array( $filter_value ) ? implode( ',', $filter_value ) : $filter_value;
        $filter_value = self::convert_to_db_format( $filter_value ); // Expects e.g."1,3,6"
        return " $filter_name IN ( $filter_value ) ";

      default:
        throw new \Exception( 'Wrong operator' );
    }
  }



  /**
   * Escape given string to be sane for database insertion
   *
   * @param  string $str string to escape
   * @return string escaped string
   * @access public
   * @static
   */
  public static function convert_to_db_format( $str )
  {
    $search  = array( "\\", "'" );
    $replace = array( "\\\\", "\'" );
    return str_replace( $search, $replace, $str );
  }

}

