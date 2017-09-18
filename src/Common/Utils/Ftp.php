<?php

namespace Common\Utils;


/**
 * Ftp utility methods
 *
 */
class Ftp
{

  /**
   * Returns file contents from FTP
   * Do not use "file_get_contents" method to allow to intercept each process (connect, login, chdir, get)
   *
   * @param string $server
   * @param string $user
   * @param string $password
   * @param string $dir
   * @param string $filename
   * @return string
   * @throws \Exception
   */
  public static function get_file_contents_from_ftp( $server, $user, $password, $dir, $filename )
  {
    // Prepare local file name
    $local_filename = $filename;

    // Try to connect to FTP
    if( ( $conn_id = ftp_connect( $server, 21 ) ) === false )
    {
      throw new \Exception( "Couldn't connect to " . $server );
    }

    // Try to login
    if( !ftp_login( $conn_id, $user, $password ) )
    {
      throw new \Exception( sprintf( "Could not connect to %s as %s", $server, $user ) );
    }

    // Turns on passive mode
    ftp_pasv( $conn_id, true );

    // Change FTP dir
    if( !@ftp_chdir( $conn_id, $dir ) )
    {
      throw new \Exception( sprintf( "Could not find FTP dir %s", $dir ) );
    }

    // Retrieve remote file from the FTP server, and saves it into a local file
    if( !@ftp_get( $conn_id, $local_filename, $filename, FTP_ASCII ) )
    {
      throw new \Exception( 'File not exists in remote server:' . $filename );
    }

    // Check if local file exists
    if( !file_exists( $local_filename ) )
    {
      throw new \Exception( 'File not exists in local server:' . $local_filename );
    }

    if( !( $contents = @file_get_contents( $local_filename ) ) )
    {
      throw new \Exception( 'Unable to get local file contents:' . $local_filename );
    }

    if( !unlink( $local_filename ) )
    {
      throw new \Exception( 'Unable to delete local file:' . $local_filename );
    }

    return $contents;
  }



  /**
   * Returns all file contents from FTP that matches with a pattern
   * Do not use "file_get_contents" method to allow to intercept each process (connect, login, chdir, get)
   *
   * @param string $server
   * @param string $user
   * @param string $password
   * @param string $dir
   * @param string $pattern
   * @return array
   * @throws \Exception
   */
  public static function get_all_file_contents_from_ftp_directory( $server, $user, $password, $dir, $pattern )
  {
    // Try to connect to FTP
    if( ( $conn_id = ftp_connect( $server, 21 ) ) === false )
    {
      throw new \Exception( "Couldn't connect to " . $server );
    }

    // Try to login
    if( !ftp_login( $conn_id, $user, $password ) )
    {
      throw new \Exception( sprintf( "Could not connect to %s as %s", $server, $user ) );
    }

    // Turns on passive mode
    ftp_pasv( $conn_id, true );

    // Change FTP dir
    if( !@ftp_chdir( $conn_id, $dir ) )
    {
      throw new \Exception( sprintf( "Could not find FTP dir %s", $dir ) );
    }

    // Get files list from directory
    if( ( $dir_files_list = @ftp_nlist( $conn_id, "." ) ) === false )
    {
      throw new \Exception( 'Unable to get file list from dir:' . $dir );
    }

    $files_contents = [];

    // For each file name check against pattern
    foreach( $dir_files_list as $dir_file )
    {
      if( preg_match( $pattern, $dir_file ) )
      {
        // If name matches, retrieve remote file from the FTP server, and saves it into a local file
        if( !@ftp_get( $conn_id, $dir_file, $dir_file, FTP_ASCII ) )
        {
          throw new \Exception( 'File not exists in remote server:' . $dir_file );
        }

        // Check if local file exists
        if( !file_exists( $dir_file ) )
        {
          throw new \Exception( 'File not exists in local server:' . $dir_file );
        }

        if( !( $contents = @file_get_contents( $dir_file ) ) )
        {
          throw new \Exception( 'Unable to get local file contents:' . $dir_file );
        }

        if( !unlink( $dir_file ) )
        {
          throw new \Exception( 'Unable to delete local file:' . $dir_file );
        }

        // Add filename and contents to array
        $files_contents[ $dir_file ] = $contents;
      }
    }

    ftp_close( $conn_id );
    return $files_contents;
  }



  /**
   * Puts file contents to FTP
   * Do not use "file_put_contents" method to allow to intercept each process (connect, login, chdir, get)
   *
   * @param string $server
   * @param string $user
   * @param string $password
   * @param string $dir
   * @param string $filename
   * @param string $contents
   * @return boolean
   * @throws \Exception
   */
  public static function put_file_contents_to_ftp( $server, $user, $password, $dir, $filename, $contents )
  {
    $temp_file_name = tempnam( sys_get_temp_dir(), 'upload' );

    if( !file_put_contents( $temp_file_name, $contents ) )
    {
      throw new \Exception( 'Unable to put file contents into temp file' );
    }

    // Connect to ftp
    if( ( $conn_id = ftp_connect( $server, 21 ) ) === false )
    {
      throw new \Exception( 'Could not connect to ' . $server );
    }

    // Try to login
    if( !ftp_login( $conn_id, $user, $password ) )
    {
      throw new \Exception( sprintf( "Could not connect to %s as %s", $server, $user ) );
    }

    // Turns on passive mode
    ftp_pasv( $conn_id, true );

    // Change FTP dir
    if( !ftp_chdir( $conn_id, $dir ) )
    {
      throw new \Exception( sprintf( "Could not find FTP dir %s", $dir ) );
    }

    // Send file to ftp
    if( !ftp_put( $conn_id, $filename, $temp_file_name, FTP_ASCII ) )
    {
      throw new \Exception( 'Unable to send file to ftp' );
    }

    if( !unlink( $temp_file_name ) )
    {
      throw new \Exception( 'Unable to delete local temp file:' . $temp_file_name );
    }

    ftp_close( $conn_id );
    return true;
  }

}
