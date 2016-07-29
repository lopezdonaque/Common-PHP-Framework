<?php

namespace Common\Utils;


/**
 * Filesystem utility methods
 *
 */
class Filesystem
{

  /**
   * Simple path cache for windows, form: $cache[basepath][filename] = fullpath
   *
   * @static
   * @var array $filefind_cache
   */
  private static $_filefind_cache = array();



  /**
   * Finds a file into directories or subdirectories and returns the complete filename path
   *
   * @param string $basedirectory
   * @param string $needle
   * @param string $top
   * @return string
   * @throws \Exception
   */
  public static function filefind( $basedirectory, $needle, $top = null )
  {
    if( $top == null )
    {
      $top = $basedirectory;
    }

    if( isset( self::$_filefind_cache[$top] ) )
    {
      if( isset( self::$_filefind_cache[$top][$needle] ) )
      {
        return self::$_filefind_cache[$top][$needle];
      }
    }
    else
    {
      self::$_filefind_cache[$top] = array();
    }

    if( ( $handle = opendir( $basedirectory ) ) === false )
    {
      throw new \Exception( "Unable to open dir [$basedirectory]" );
    }

    while( ( $file = readdir( $handle ) ) )
    {
      if( ( $file == '.' ) || ( $file == '..' ) || substr( $file, 0, 1 ) == '.' )
      {
        continue;
      }

      if( is_dir( $basedirectory . DIRECTORY_SEPARATOR . $file ) )
      {
        $subDirResult = self::filefind( $basedirectory . DIRECTORY_SEPARATOR . $file, $needle, $top );

        if( $subDirResult != '' )
        {
          closedir( $handle );
          return $subDirResult;
        }
      }

      self::$_filefind_cache[$top][$file] = $basedirectory . DIRECTORY_SEPARATOR . $file;

      if( strcmp( $file, $needle ) == 0 )
      {
        closedir( $handle );
        return $basedirectory . DIRECTORY_SEPARATOR . $needle;
      }
    }

    closedir( $handle );
    return '';
  }



  /**
   * Unlink a file, even if not in the working path
   *
   * @param string $file
   * @return bool
   */
  public static function unlink( $file )
  {
    $rpath = realpath( $file );

    if( $rpath === false )
    {
      return false;
    }

    $dir = dirname( $rpath );
    $curdir = getcwd();

    if( !@chdir( $dir ) )
    {
      return false;
    }

    $result = @unlink( basename( $rpath ) );
    @chdir( $curdir );
    return $result;
  }

}
