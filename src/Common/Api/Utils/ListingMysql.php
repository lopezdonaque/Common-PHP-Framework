<?php

namespace Common\Api\Utils;


/**
 * Class to retrieve results list
 *
 */
class ListingMySql extends Listing
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
    $list_query = clone $this->_basic_query;

    //add first condition to avoid to check if exists filters
    $list_query->where( '1 = 1' );

    //filters
    if( $this->_filters )
    {
      $filters = $this->_get_filters_expression( $this->_filters );
      $list_query->andWhere( $filters );
    }

    //search filters
    if( $this->_search_filters )
    {
      $search_filters = $this->_get_search_filters_expression( $this->_search_filters );
      $list_query->andWhere( $search_filters );
    }

    //sort
    if( $this->_sort_column && $this->_sort_type )
    {
      $list_query->orderBy( 'a.' . $this->_sort_column, $this->_sort_type );
    }

    //limit
    $offset = ( $this->_page - 1 ) *  $this->_rows_per_page;
    $list_query->setFirstResult( $offset );
    $list_query->setMaxResults( $this->_rows_per_page );

    return $list_query;
  }



  /**
   * Returns count query
   *
   * @return QueryBuilder
   */
  public function get_count_query()
  {
    $count_query = clone $this->_basic_query;

    $count_query->select( 'COUNT(a)' );

    //add first condition to avoid to check if exists filters
    $count_query->where( '1 = 1' );

    //filters
    if( $this->_filters )
    {
      $filters = $this->_get_filters_expression( $this->_filters );
      $count_query->andWhere( $filters );
    }

    //search filters
    if( $this->_search_filters )
    {
      $search_filters = $this->_get_search_filters_expression( $this->_search_filters );
      $count_query->andWhere( $search_filters );
    }

    //limit
    $offset = ( $this->_page - 1 ) *  $this->_rows_per_page;
    $count_query->setFirstResult( $offset );
    $count_query->setMaxResults( $this->_rows_per_page );

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

    foreach( $filters as $filter )
    {
      $conditions[] = $this->_get_filter_expression( $filter )->__toString();
    }

    $res = implode( ' OR ' , $conditions );

    return $res;
  }



  /**
   * Returns filter as DQL expression
   *
   * @param filter $filter
   * @return Expr
   */
  private function _get_filter_expression( $filter )
  {
    $expr = new \Doctrine\ORM\Query\Expr();
    $column = 'a.' . $filter->field;

    switch( $filter->operator )
    {
      case \Common\Api\Entities\Filter::FL_EQUALS:
        $f = $expr->eq( $column, $expr->literal( $filter->value ) );
        return $f;

      case \Common\Api\Entities\Filter::FL_CONTAINS:
        $f = $expr->like( $column, $expr->literal( '%' . $filter->value . '%' ) );
        return $f;
    }
  }

}

