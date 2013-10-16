<?php

namespace Common\Api\Utils;


/**
 * Utility api methods
 *
 */
class Utils
{

  /**
   * Get an attachment provided by the transport layer.
   *
   * Location can be:
   *
   * - A local file, in case the API is called locally. For example, in unit tests. URL like: "../../testfile.pdf"
   *
   * - In a HTTP POST using multipart/form-data encoding (see RFC1867), either the value "name" attribute
   *   (as would come from a form POST) or the "filename" attribute.
   *
   * - A URL accessible by the API server (note that depending on the location of the API server it might not have open
   *   internet access). URL like: "http://www.google.es/images/srpr/logo3w.png"
   *
   * @param string $location The file location identifier (URL, local path, POST variable or POST filename)
   * @return string Local path of the attachment (or null if not found).
   * @throws \Common\Api\Exception
   */
  public static function get_attachment( $location )
  {
    if( $location == null )
    {
      return null;
    }

    $uriobj = \Common\Uri\Uri::get_instance( $location );

    if( $uriobj == null )
    {
      if( isset( $_FILES ) )
      {
        if( isset( $_FILES[ $location ] ) )
        {
          if( ( $error_code = $_FILES[ $location ][ 'error' ] ) > 0 )
          {
            throw new \Common\Api\Exception( "Error uploading file [Code=$error_code]" );
          }

          return $_FILES[ $location ][ 'tmp_name' ];
        }

        foreach( $_FILES as $file )
        {
          if( $file[ 'name' ] == $location )
          {
            return $file[ 'tmp_name' ];
          }
        }
      }

      if( is_file( $location ) )
      {
        return $location;
      }

      return null;
    }
    else
    {
      return $location;
    }
  }

}
