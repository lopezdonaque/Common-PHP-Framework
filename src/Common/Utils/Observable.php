<?php

namespace Common\Utils;


/**
 * Class to add Observable methods
 *
 * Based on: http://dhotson.tumblr.com/post/14608885912/whats-new-in-php-5-4
 *
 */
class Observable
{

  /**
   * Listeners
   *
   * @var array
   */
  private $_listeners = array();



  /**
   * Adds a calllback to execute on event fired
   *
   * @param string $event
   * @param array|callback $callback A function or an array composed of either ("object", "method") or ($object, "method").
   */
  public function on( $event, $callback )
  {
    if( !isset( $this->_listeners[ $event ] ) )
    {
      $this->_listeners[ $event ] = array();
    }

    $this->_listeners[ $event ][] = $callback;
  }



  /**
   * Fires an event
   *
   * @param string $event
   * @param mixed $data
   */
  public function fireEvent( $event, $data = null )
  {
    if( isset( $this->_listeners[ $event ] ) )
    {
      foreach( $this->_listeners[ $event ] as $listener )
      {
        if( is_callable( $listener ) )
        {
          call_user_func_array( $listener, array( $data ) );
        }
        else
        {
          if( is_array( $listener ) && count( $listener ) == 2 )
          {
            list( $objectname, $method ) = $listener;

            if( is_string( $objectname ) && is_string( $method ) )
            {
              $obj = new $objectname();
              call_user_func_array( array( $obj, $method ), array( $data ) );
            }
          }
        }
      }
    }
  }

}
