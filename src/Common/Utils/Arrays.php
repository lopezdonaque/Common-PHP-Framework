<?php

namespace Common\Utils;


/**
 * Class to manage array methods
 *
 */
class Arrays
{

  /**
   * Closest option to search in an array going up
   *
   * @var string
   */
  const CLOSEST_UP = 'up';


  /**
   * Closest option to search in an array going down
   *
   * @var string
   */
  const CLOSEST_DOWN = 'down';



  /**
   * UTF Decode the elements of an array
   *
   * @param array $array
   */
  public static function utf8_decode_array( &$array )
  {
    foreach( $array as &$value )
    {
      if( is_array( $value ) )
      {
        self::utf8_decode_array( $value );
      }
      else
      {
        $value = utf8_decode( $value );
      }
    }
  }



  /**
   * UTF Encode the elements of an array
   *
   * @param array $array
   */
  public static function utf8_encode_array( &$array )
  {
    foreach( $array as &$value )
    {
      if( is_array( $value ) )
      {
        self::utf8_encode_array( $value );
      }
      else
      {
        $value = utf8_encode( $value );
      }
    }
  }



  /**
   * Returns if an array is associative or indexed
   *
   * @param array $array
   * @return boolean
   */
  public static function is_associative( $array )
  {
    return is_array( $array ) && array_diff_key( $array, array_keys( array_keys( $array ) ) );
  }



  /**
   * Sort a multi array by a column key
   *
   * @param array $array
   * @param string $key
   * @param string $comparison_function - Function to compare
   */
  public static function array_key_multi_sort( &$array, $key, $comparison_function = 'strnatcasecmp' )
  {
    uasort( $array, create_function( '$a, $b', "return $comparison_function(\$a['$key'], \$b['$key']);" ) );
  }



  /**
   * Sort array by a element
   *
   * @param array $arr
   * @param string $element
   * @param string $comparison_function - Function to compare
   */
  public static function array_element_multi_sort( &$arr, $element, $comparison_function = 'strnatcasecmp' )
  {
    uasort( $arr, create_function( '$a, $b', "return $comparison_function(\$a->$element, \$b->$element);" ) );
  }



  /**
   * Sorts an array of object by given numeric property
   *
   * @param array $array
   * @param string $property
   */
  public static function sort_array_of_objects_by_numeric_property( &$array, $property )
  {
    usort( $array, function( $a, $b ) use( $property )
    {
      if( $a->$property == $b->$property )
        return 0;

      return ( $a->$property < $b->$property ) ? -1 : 1;
    });
  }



  /**
   * Displays an array as a html table
   *
   * @param array $array
   */
  public static function print_r_html( $array )
  {
    print self::get_html( $array );
  }



  /**
   * Returns an array as a html table
   *
   * @param array $array
   * @param bool $linkify
   * @return array|string
   */
  public static function get_html( $array, $linkify = false )
  {
    $text = '';

    if( is_array( $array ) )
    {
      $text .= "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
      $text .= '<tr><td colspan=50 style="background-color: #333333;"></td></tr>';

      if( empty( $array ) )
      {
        $text .= '<tr>';
        $text .= '<td style="width: 40px; background-color: #F0F0F0; vertical-align: top;">&nbsp;</td>';
        $text .= '<td>EMPTY</td>';
        $text .= '</tr>';
      }
      else
      {
        foreach( $array as $k => $v )
        {
          $text .= '<tr>';
          $text .= '<td style="width: 40px; background-color: #F0F0F0; vertical-align: top; font-weight: bold; cursor: pointer;" onclick="this.nextSibling.style.display = ( this.nextSibling.style.display == \'none\' ) ? \'\' : \'none\';">' . htmlentities( $k, null, ini_get( 'default_charset' ) ) . '</td>';
          $text .= '<td>' . self::get_html( $v, $linkify ) . '</td>';
          $text .= '</tr>';
        }
      }

      $text .= "</table>";
      return $text;
    }

    if( is_string( $array ) )
    {
      $value = htmlentities( $array, null, ini_get( 'default_charset' ) );

      if( $linkify && substr( $array, 0, 4 ) == 'http' )
      {
        return '<a href="' . $array . '">' . $value . '</a>';
      }

      return $value;
    }

    return $array;
  }



  /**
   * Checks if the key of the array exists to return the value or returns the notset_value parameter
   *
   * @param array $array
   * @param string $key
   * @param mixed $notset_return_value
   * @return mixed
   */
  public static function get_value( $array, $key, $notset_return_value )
  {
    if( isset( $array[ $key ] ) )
    {
      return $array[ $key ];
    }

    return $notset_return_value;
  }



  /**
   * Checks if the key of the array exists to return the value or returns the notset_value parameter
   *
   * @param array $array
   * @param string $property
   * @param string $value
   * @param mixed $notset_return_value
   * @return mixed
   */
  public static function get_item_by_property_value( $array, $property, $value, $notset_return_value = null )
  {
    foreach( $array as $item )
    {
      if( $item->$property == $value )
      {
        return $item;
      }
    }

    return $notset_return_value;
  }



  /**
   * Join an array using a template and replacing text with a sprintf
   *
   * @param string[string] $array
   * @param string $template
   * @param string $separator
   * @return string
   */
  public static function join_sprintf( $array, $template, $separator = '' )
  {
    $result = [];

    foreach( $array as $key => $value )
    {
      $result[] = sprintf( $template, $key, $value );
    }

    return implode( $separator, $result );
  }



  /**
   * Retrieve closest value in an array
   *
   * @param array $array
   * @param integer $value
   * @param string $closest_option
   * @return integer|boolean
   */
  public static function get_closest_value( $array, $value, $closest_option = self::CLOSEST_UP )
  {
    $closest_value = null;
    $size = count( $array );

    if( $size == 1 )
    {
      $closest_value = $array[ 0 ];
    }
    elseif( $size > 0 )
    {
      for( $i = 1; $i <= $size; $i++ )
      {
        if( $value > $array[ $i - 1 ] && $value < $array[ $i ] )
        {
          $closest_value = ( $closest_option == self::CLOSEST_UP ) ? $array[ $i ]: $array[ $i - 1 ];
        }
      }
    }
    else
    {
      return false;
    }

    return $closest_value;
  }



  /**
   * Converts array to object
   *
   * @param array $array
   * @param boolean $convert_indexed
   * @return \stdClass
   */
  public static function array_to_object( $array, $convert_indexed = true )
  {
    if( !is_array( $array ) )
    {
      return $array;
    }

    if( count( $array ) > 0 )
    {
      if( $convert_indexed || self::is_associative( $array ) )
      {
        $object = new \stdClass();

        foreach( $array as $name => $value )
        {
          $name = strtolower( trim( $name ) );

          if( !empty( $name ) )
          {
            $object->$name = self::array_to_object( $value, $convert_indexed );
          }
        }

        return $object;
      }
      else
      {
        foreach( $array as &$value )
        {
          $value = self::array_to_object( $value, $convert_indexed );
        }

        return $array;
      }
    }
    else
    {
      return new \stdClass();
    }
  }



  /**
  * Convert an object into an array
  *
  * @param object|array $object
  * @return array
  */
  public static function object_to_array( $object )
  {
    if( is_object( $object ) )
    {
      if( isset( $object->value ) && count( (array)$object ) == 1  )
      {
        $object = (array)$object->value;
      }
      elseif( $object )
      {
        $object = (array)$object;
      }
      elseif( empty( $object ) )
      {
        return '';
      }
      else
      {
        return $object;
      }
    }
    elseif( !is_array( $object ) )
    {
      return $object;
    }

    foreach( $object as &$element )
    {
      $element = self::object_to_array( $element );
    }

    return $object;
  }



  /**
   * Convert a indexed array into an associative
   *
   * @param array $array
   * @param string $element
   * @return array
   */
  public static function indexed_to_associative( $array, $element )
  {
    $indexed_array = $array;
    $associative_array = [];

    for( $i = 0; $i < count( $indexed_array ); $i++ )
    {
      if( is_object( $indexed_array[ $i ] ) )
      {
        $associative_element = $indexed_array[ $i ]->$element;
        unset( $indexed_array[ $i ]->$element );
      }
      else
      {
        $associative_element = $indexed_array[ $i ][ $element ];
        unset( $indexed_array[ $i ][ $element ] );
      }

      $associative_array[ $associative_element ] = $indexed_array[ $i ];
    }

    return $associative_array;
  }



  /**
   * Filter an associative array with a provided callback function
   *
   * @param array $array
   * @param callback $callback of the form function f($key,$value)
   * @return array
   */
  public static function array_filter_assoc( $array, $callback )
  {
    $res = [];

    foreach( $array as $k => $v )
    {
      if( $callback( $k, $v ) == true )
      {
        $res[ $k ] = $v;
      }
    }

    return $res;
  }



  /**
   * Find the first element of the array that callback returns true
   *
   * @param array $array
   * @param callback $callback
   * @param bool $return_key
   * @return mixed|int|string or null if not found
   */
  public static function find_first( $array, $callback, $return_key = true )
  {
    if( !is_callable( $callback ) )
    {
      return null;
    }

    foreach( $array as $k => $item )
    {
      if( $callback( $item ) === true )
      {
        return $return_key ? $k : $item;
      }
    }

    return null;
  }



  /**
   * Method to convert an array into arguments to call a method
   *
   * @param array $array_args
   * @return array
   */
  public static function convert_to_arguments( $array_args )
  {
    $object_args = [];

    foreach( $array_args as $arg )
    {
      if( is_array( $arg ) )
      {
        $object_args[] = self::array_to_object( $arg, false );
      }
      else
      {
        $object_args[] = $arg;
      }
    }

    return $object_args;
  }



  /**
   * Returns object from array of object by given attribute and value
   * If $recursive_element is set, it will make a recursive search using $recursive_element as node
   *
   * @param object[] $objects
   * @param string $attribute
   * @param string $value
   * @param string $recursive_element
   * @return object|boolean|\stdClass - In case boolean is returned it's always 'false'
   */
  public static function get_object_from_array( $objects, $attribute, $value, $recursive_element = '' )
  {
    foreach( $objects as $object )
    {
      if( $object->$attribute == $value )
      {
        return $object;
      }

      if( !empty( $object->$recursive_element ) )
      {
        $object = self::get_object_from_array( $object->$recursive_element, $attribute, $value, $recursive_element );

        if( $object !== false )
        {
          return $object;
        }
      }
    }

    return false;
  }



  /**
   * Replace given values in an array recursively.
   *
   * @param array $array
   * @param array $search_values
   * @return object
   *
   *  For example:
   *
   *  $array = array( 'key1' => 'apple', 'key2' => 'banana' );
   *  $search_values = array( 'apple' => 'orange', 'banana' => 'lemon' );
   *  $new_array = \Common\Utils\Arrays::replace_values( $array, $search_values );
   *  print_r( $new_array );
   *  -------------------
   *
   *  Array
   *  (
   *    [key1] => orange
   *    [key2] => lemon
   *  )
   */
  public static function replace_values( $array, $search_values = [] )
  {
    // Search in all array element
    foreach( $array as $key => &$value )
    {
      // If object is an array call recursively to same method
      if( is_array( $value ) )
      {
        $value = self::replace_values( $value, $search_values );
      }
      else if( !is_bool( $value ) )
      {
        if( array_key_exists( $value, $search_values ) )
        {
          $array[ $key ] = $search_values[ $value ];
        }
      }
    }

    return $array;
  }



  /**
   * Search value in array recursively
   *
   * @param mixed $needle
   * @param array $haystack
   * @return bool
   */
  public static function in_array_recursive( $needle, $haystack )
  {
    $iterator = new \RecursiveIteratorIterator( new \RecursiveArrayIterator( $haystack ) );

    foreach( $iterator AS $element )
    {
      if( $element == $needle ) /* @var $element mixed */
      {
        return true;
      }
    }

    return false;
  }



  /**
   * Apply the provided callback function to all elements of an associative array
   *
   * @param array $array
   * @param \callback $callback of the form function f($key,$value) and should return an array of the form [newkey,newvalue]
   * @return array
   */
  public static function array_map_assoc( $array, $callback )
  {
    $res = [];

    foreach( $array as $oldkey => $oldvalue )
    {
      list( $newkey, $newvalue ) = $callback( $oldkey, $oldvalue );
      $res[ $newkey ] = $newvalue;
    }

    return $res;
  }

}
