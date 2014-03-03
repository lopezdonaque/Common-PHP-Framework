<?php

namespace Common\Orm;


/**
 * EntityBase
 *
 */
class EntityBase
{

  /**
   * Magic __call method
   *
   * http://wildlyinaccurate.com/integrating-doctrine-2-with-codeigniter-2/
   *
   * Attempt to set/get a property. Only supports all-lowercase properties.
   * This differs from __set() and __get(), as we want to be able to have custom get()
   * and set() methods (e.g. setPassword() encrypts the password before setting it).
   *
   * @access public
   * @param  string $method
   * @param  mixed  $args
   * @return void|mixed
   * @throws \Exception
   */
  public function __call( $method, $args )
  {
    // Find words by camelCase (e.g. setUsername, getUserGroup)
    $method_words = preg_split( '/(?=[A-Z])/', $method );
    $method_name = $method_words[0];

    // Remove the method name from method_words
    unset( $method_words[0] );

    // The remaining method_words make up the property name
    // UserGroup becomes user_group
    $property = strtolower( implode( '_', $method_words ) );

    // Property doesn't exist
    if( !property_exists( $this, $property ) )
    {
      throw new \Exception( "Tried to call {$method}() on " . __CLASS__ . ". Property '{$property}' doesn't exist." );
    }

    // Set() methods
    if( $method_name == 'set' )
    {
      // More than one argument was given
      if( count( $args ) > 1 )
      {
        throw new \Exception( "Tried to set " . __CLASS__ . "->{$property}. 1 argument expected; " . count($args) . " given.");
      }

      $this->$property = $args[0];
    }

    // Get() methods
    if( $method_name == 'get' )
    {
      return $this->$property;
    }
  }

}
