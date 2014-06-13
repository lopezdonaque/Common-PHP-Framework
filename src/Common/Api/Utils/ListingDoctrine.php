<?php

namespace Common\Api\Utils;


/**
 * Class to retrieve results list
 *
 */
class ListingDoctrine extends Listing
{

  /**
   * Constructor
   *
   * @param \Doctrine\ORM\QueryBuilder $basic_query
   * @param \Common\Api\Entities\ListOptions|object $list_options
   */
  public function __construct( $basic_query, $list_options )
  {
    parent::__construct( $basic_query, $list_options );
  }



  /**
   * Returns list query
   *
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function get_list_query()
  {
    $list_query = clone $this->_basic_query;

    // Add first condition to avoid to check if exists filters
    if( is_null( $list_query->getDQLPart( 'where' ) ) )
    {
      $list_query->where( '1 = 1' );
    }

    // Filters
    if( is_array( $this->_filters ) && count( $this->_filters ) > 0 )
    {
      $filters = $this->_get_filters_expression( $this->_filters );
      $list_query->andWhere( $filters );
    }

    // Search filters
    if( is_array( $this->_search_filters ) && count( $this->_search_filters ) > 0 )
    {
      $search_filters = $this->_get_search_filters_expression( $this->_search_filters );
      $list_query->andWhere( $search_filters );
    }

    // Sort
    if( $this->_sort_column && $this->_sort_type )
    {
      $list_query->orderBy( 'a.' . $this->_sort_column, $this->_sort_type );
    }

    // Limit
    if( $this->_page && $this->_rows_per_page )
    {
      $offset = ( $this->_page - 1 ) *  $this->_rows_per_page;
      $list_query->setFirstResult( $offset );
      $list_query->setMaxResults( $this->_rows_per_page );
    }

    return $list_query;
  }



  /**
   * Returns count query
   *
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function get_count_query()
  {
    $count_query = clone $this->_basic_query;

    $count_query->select( 'COUNT(a)' );

    // Add first condition to avoid to check if exists filters
    if( is_null( $count_query->getDQLPart( 'where' ) ) )
    {
      $count_query->where( '1 = 1' );
    }

    // Filters
    if( is_array( $this->_filters ) && count( $this->_filters ) > 0 )
    {
      $filters = $this->_get_filters_expression( $this->_filters );
      $count_query->andWhere( $filters );
    }

    // Search filters
    if( is_array( $this->_search_filters ) && count( $this->_search_filters ) > 0 )
    {
      $search_filters = $this->_get_search_filters_expression( $this->_search_filters );
      $count_query->andWhere( $search_filters );
    }

    return $count_query;
  }



  /**
   * Returns filters as DQL expression
   *
   * @param array $filters
   * @return string
   */
  private function _get_filters_expression( $filters )
  {
    $conditions = array();

    foreach( $filters as $filter )
    {
      $conditions[] = $this->_get_filter_expression( $filter )->__toString();
    }

    $res = implode( ' AND ' , $conditions );

    return $res;
  }



  /**
   * Returns search filters as DQL expression
   *
   * @param array $filters
   * @return string
   */
  private function _get_search_filters_expression( $filters )
  {
    $conditions = array();

    foreach( $filters[ 0 ]->filters as $filter )
    {
      $conditions[] = $this->_get_filter_expression( $filter )->__toString();
    }

    $res = implode( ' OR ' , $conditions );

    return $res;
  }



  /**
   * Returns filter as DQL expression
   *
   * @param \Common\Api\Entities\Filter $filter
   * @return \Doctrine\ORM\Query\Expr
   * @throws \Exception
   */
  private function _get_filter_expression( $filter )
  {
    $expr = new \Doctrine\ORM\Query\Expr();
    $column = 'a.' . $filter->column;

    switch( $filter->operator )
    {
      case \Common\Api\Entities\Filter::FL_EQUALS:
      case \Common\Api\Entities\Filter::FL_INTEGER_EQUAL:
        $f = $expr->eq( $column, $expr->literal( $filter->value ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_NOT_EQUALS:
        $f = $expr->neq( $column, $expr->literal( $filter->value ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_CONTAINS:
        $f = $expr->like( "LOWER( $column )", $expr->literal( '%' . strtolower( $filter->value ) . '%' ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_CONTAINS_SENSITIVE:
        $f = $expr->like( $column, $expr->literal( '%' . $filter->value . '%' ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_BIGGER:
        $f = $expr->gt( $column, $expr->literal( $filter->value ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_BIGGER_EQUALS:
        $f = $expr->gte( $column, $expr->literal( $filter->value ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_LOWER:
        $f = $expr->lt( $column, $expr->literal( $filter->value ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_LOWER_EQUALS:
        $f = $expr->lte( $column, $expr->literal( $filter->value ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_IN:
        $f = $expr->in( $column, $filter->value );
        return $f;

      case \Common\Api\Entities\Filter::FL_NOT_IN:
        $f = $expr->notIn( $column, $filter->value );
        return $f;

      case \Common\Api\Entities\Filter::FL_EQUALS_TIMESTAMP:
        $f = $expr->eq( $column, $expr->literal( date( 'Y-m-d H:i:s', $filter->value ) ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_BIGGER_TIMESTAMP:
        $f = $expr->gt( $column, $expr->literal( date( 'Y-m-d H:i:s', $filter->value ) ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_BIGGER_EQUALS_TIMESTAMP:
        $f = $expr->gte( $column, $expr->literal( date( 'Y-m-d H:i:s', $filter->value ) ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_LOWER_TIMESTAMP:
        $f = $expr->lt( $column, $expr->literal( date( 'Y-m-d H:i:s', $filter->value ) ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_LOWER_EQUALS_TIMESTAMP:
        $f = $expr->lte( $column, $expr->literal( date( 'Y-m-d H:i:s', $filter->value ) ) );
        return $f;

      default:
        throw new \Exception( 'Wrong operator' );
    }
  }

}
