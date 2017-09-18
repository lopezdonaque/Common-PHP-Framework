<?php

namespace Common\Utils;


/**
 * Simple handler for mime-type lines.
 * Parses mime-type definitions and offers helpers to map from extension to mime type and back.
 *
 * @see {http://en.wikipedia.org/wiki/Internet_media_type}
 */
class Mimetype
{

  /**
   * Type constants
   */
  const TYPE_APPLICATION = 'application';
  const TYPE_AUDIO = 'audio';
  const TYPE_IMAGE = 'image';
  const TYPE_MESSAGE = 'message';
  const TYPE_MODEL = 'model';
  const TYPE_MULTIPART = 'multipart';
  const TYPE_TEXT = 'text';
  const TYPE_VIDEO = 'video';


  /**
   * Subtype constants
   */
  const SUBTYPE_PLAIN = 'plain';
  const SUBTYPE_PDF = 'pdf';
  const SUBTYPE_XML = 'xml';
  const SUBTYPE_HTML = 'html';
  const SUBTYPE_JPEG = 'jpeg';


  /**
   * Mime to extensions
   *
   * @var array
   */
  private static $_mime_to_extensions =
  [
    'application/envoy'                       => [ 'evy' ],
    'application/fractals'                    => [ 'fif' ],
    'application/futuresplash'                => [ 'spl' ],
    'application/hta'                         => [ 'hta' ],
    'application/internet-property-stream'    => [ 'acx' ],
    'application/mac-binhex40'                => [ 'hqx' ],
    'application/msword'                      => [ 'doc', 'dot' ],
    'application/octet-stream'                => [ 'bin', 'class', 'dms', 'exe', 'lha', 'lzh' ],
    'application/oda'                         => [ 'oda' ],
    'application/olescript'                   => [ 'axs' ],
    'application/pdf'                         => [ 'pdf' ],
    'application/pics-rules'                  => [ 'prf' ],
    'application/pkcs10'                      => [ 'p10' ],
    'application/pkix-crl'                    => [ 'crl' ],
    'application/postscript'                  => [ 'ai', 'eps', 'ps' ],
    'application/rtf'                         => [ 'rtf' ],
    'application/set-payment-initiation'      => [ 'setpay' ],
    'application/set-registration-initiation' => [ 'setreg' ],
    'application/vnd.ms-excel'                => [ 'xls', 'xlc', 'xlm', 'xla', 'xlt', 'xlw' ],
    'application/vnd.ms-pkicertstore'         => [ 'sst' ],
    'application/vnd.ms-pkiseccat'            => [ 'cat' ],
    'application/vnd.ms-pkistl'               => [ 'stl' ],
    'application/vnd.ms-powerpoint'           => [ 'ppt', 'pps', 'pot' ],
    'application/vnd.ms-project'              => [ 'mpp' ],
    'application/vnd.ms-works'                => [ 'wks', 'wdb', 'wcm', 'wps' ],
    'application/winhlp'                      => [ 'hlp' ],
    'application/x-bcpio'                     => [ 'bcpio' ],
    'application/x-cdf'                       => [ 'cdf' ],
    'application/x-compress'                  => [ 'z' ],
    'application/x-compressed'                => [ 'tgz' ],
    'application/x-cpio'                      => [ 'cpio' ],
    'application/x-csh'                       => [ 'csh' ],
    'application/x-director'                  => [ 'dir', 'dcr', 'dxr' ],
    'application/x-dvi'                       => [ 'dvi' ],
    'application/x-gtar'                      => [ 'gtar' ],
    'application/x-gzip'                      => [ 'gz' ],
    'application/x-hdf'                       => [ 'hdf' ],
    'application/x-internet-signup'           => [ 'ins', 'isp' ],
    'application/x-iphone'                    => [ 'iii' ],
    'application/x-javascript'                => [ 'js' ],
    'application/x-latex'                     => [ 'latex' ],
    'application/x-msaccess'                  => [ 'mdb' ],
    'application/x-mscardfile'                => [ 'crd' ],
    'application/x-msclip'                    => [ 'clp' ],
    'application/x-msdownload'                => [ 'dll' ],
    'application/x-msmediaview'               => [ 'm13', 'm14', 'mvb' ],
    'application/x-msmetafile'                => [ 'wmf' ],
    'application/x-msmoney'                   => [ 'mny' ],
    'application/x-mspublisher'               => [ 'pub' ],
    'application/x-msschedule'                => [ 'scd' ],
    'application/x-msterminal'                => [ 'trm' ],
    'application/x-mswrite'                   => [ 'wri' ],
    'application/x-perfmon'                   => [ 'pma', 'pmc', 'pml', 'pmr', 'pmw' ],
    'application/x-pkcs12'                    => [ 'p12', 'pfx' ],
    'application/x-pkcs7-certificates'        => [ 'p7b', 'spc' ],
    'application/x-pkcs7-certreqresp'         => [ 'p7r' ],
    'application/x-pkcs7-mime'                => [ 'p7c', 'p7m' ],
    'application/x-pkcs7-signature'           => [ 'p7s' ],
    'application/x-sh'                        => [ 'sh' ],
    'application/x-shar'                      => [ 'shar' ],
    'application/x-stuffit'                   => [ 'sit' ],
    'application/x-sv4cpio'                   => [ 'sv4cpio' ],
    'application/x-sv4crc'                    => [ 'sv4crc' ],
    'application/x-tar'                       => [ 'tar' ],
    'application/x-tcl'                       => [ 'tcl' ],
    'application/x-tex'                       => [ 'tex' ],
    'application/x-texinfo'                   => [ 'texi', 'texinfo' ],
    'application/x-troff'                     => [ 'roff', 't', 'tr' ],
    'application/x-troff-man'                 => [ 'man' ],
    'application/x-troff-me'                  => [ 'me' ],
    'application/x-troff-ms'                  => [ 'ms' ],
    'application/x-ustar'                     => [ 'ustar' ],
    'application/x-wais-source'               => [ 'src' ],
    'application/x-x509-ca-cert'              => [ 'cer', 'crt', 'der' ],
    'application/ynd.ms-pkipko'               => [ 'pko' ],
    'application/zip'                         => [ 'zip' ],
    'audio/basic'                             => [ 'au', 'snd' ],
    'audio/mid'                               => [ 'mid', 'rmi' ],
    'audio/mpeg'                              => [ 'mp3' ],
    'audio/x-aiff'                            => [ 'aif', 'aifc', 'aiff' ],
    'audio/x-mpegurl'                         => [ 'm3u' ],
    'audio/x-pn-realaudio'                    => [ 'ra', 'ram' ],
    'audio/x-wav'                             => [ 'wav' ],
    'image/bmp'                               => [ 'bmp' ],
    'image/cis-cod'                           => [ 'cod' ],
    'image/gif'                               => [ 'gif' ],
    'image/ief'                               => [ 'ief' ],
    'image/jpeg'                              => [ 'jpg', 'jpe', 'jpeg' ],
    'image/pipeg'                             => [ 'jfif' ],
    'image/svg+xml'                           => [ 'svg' ],
    'image/tiff'                              => [ 'tiff', 'tif' ],
    'image/x-cmu-raster'                      => [ 'ras' ],
    'image/x-cmx'                             => [ 'cmx' ],
    'image/x-icon'                            => [ 'ico' ],
    'image/x-portable-anymap'                 => [ 'pnm' ],
    'image/x-portable-bitmap'                 => [ 'pbm' ],
    'image/x-portable-graymap'                => [ 'pgm' ],
    'image/x-portable-pixmap'                 => [ 'ppm' ],
    'image/x-rgb'                             => [ 'rgb' ],
    'image/x-xbitmap'                         => [ 'xbm' ],
    'image/x-xpixmap'                         => [ 'xpm' ],
    'image/x-xwindowdump'                     => [ 'xwd' ],
    'message/rfc822'                          => [ 'mht', 'mhtml', 'nws' ],
    'text/css'                                => [ 'css' ],
    'text/h323'                               => [ '323' ],
    'text/html'                               => [ 'html', 'htm', 'stm' ],
    'text/iuls'                               => [ 'uls' ],
    'text/plain'                              => [ 'txt', 'c', 'h', 'bas' ],
    'text/richtext'                           => [ 'rtx' ],
    'text/scriptlet'                          => [ 'sct' ],
    'text/tab-separated-values'               => [ 'tsv' ],
    'text/webviewhtml'                        => [ 'htt' ],
    'text/x-component'                        => [ 'htc' ],
    'text/x-setext'                           => [ 'etx' ],
    'text/x-vcard'                            => [ 'vcf' ],
    'video/mpeg'                              => [ 'mpg', 'mpa', 'mpeg', 'mpe', 'mp2', 'mpv2' ],
    'video/quicktime'                         => [ 'mov', 'qt' ],
    'video/x-la-asf'                          => [ 'lsf', 'lsx' ],
    'video/x-ms-asf'                          => [ 'asf', 'asr', 'asx' ],
    'video/x-msvideo'                         => [ 'avi' ],
    'video/x-sgi-movie'                       => [ 'movie' ],
    'x-world/x-vrml'                          => [ 'wrl', 'vrml', 'flr', 'wrz', 'xaf', 'xof' ]
  ];


  /**
   * Extensions to Mime
   *
   * @var array
   */
  private static $_extensions_to_mime =
  [
    'evy'     => 'application/envoy',
    'fif'     => 'application/fractals',
    'spl'     => 'application/futuresplash',
    'hta'     => 'application/hta',
    'acx'     => 'application/internet-property-stream',
    'hqx'     => 'application/mac-binhex40',
    'doc'     => 'application/msword',
    'dot'     => 'application/msword',
    'bin'     => 'application/octet-stream',
    'class'   => 'application/octet-stream',
    'dms'     => 'application/octet-stream',
    'exe'     => 'application/octet-stream',
    'lha'     => 'application/octet-stream',
    'lzh'     => 'application/octet-stream',
    'oda'     => 'application/oda',
    'axs'     => 'application/olescript',
    'pdf'     => 'application/pdf',
    'prf'     => 'application/pics-rules',
    'p10'     => 'application/pkcs10',
    'crl'     => 'application/pkix-crl',
    'ai'      => 'application/postscript',
    'eps'     => 'application/postscript',
    'ps'      => 'application/postscript',
    'rtf'     => 'application/rtf',
    'setpay'  => 'application/set-payment-initiation',
    'setreg'  => 'application/set-registration-initiation',
    'xla'     => 'application/vnd.ms-excel',
    'xlc'     => 'application/vnd.ms-excel',
    'xlm'     => 'application/vnd.ms-excel',
    'xls'     => 'application/vnd.ms-excel',
    'xlt'     => 'application/vnd.ms-excel',
    'xlw'     => 'application/vnd.ms-excel',
    'sst'     => 'application/vnd.ms-pkicertstore',
    'cat'     => 'application/vnd.ms-pkiseccat',
    'stl'     => 'application/vnd.ms-pkistl',
    'pot'     => 'application/vnd.ms-powerpoint',
    'pps'     => 'application/vnd.ms-powerpoint',
    'ppt'     => 'application/vnd.ms-powerpoint',
    'mpp'     => 'application/vnd.ms-project',
    'wcm'     => 'application/vnd.ms-works',
    'wdb'     => 'application/vnd.ms-works',
    'wks'     => 'application/vnd.ms-works',
    'wps'     => 'application/vnd.ms-works',
    'hlp'     => 'application/winhlp',
    'bcpio'   => 'application/x-bcpio',
    'cdf'     => 'application/x-cdf',
    'z'       => 'application/x-compress',
    'tgz'     => 'application/x-compressed',
    'cpio'    => 'application/x-cpio',
    'csh'     => 'application/x-csh',
    'dcr'     => 'application/x-director',
    'dir'     => 'application/x-director',
    'dxr'     => 'application/x-director',
    'dvi'     => 'application/x-dvi',
    'gtar'    => 'application/x-gtar',
    'gz'      => 'application/x-gzip',
    'hdf'     => 'application/x-hdf',
    'ins'     => 'application/x-internet-signup',
    'isp'     => 'application/x-internet-signup',
    'iii'     => 'application/x-iphone',
    'js'      => 'application/x-javascript',
    'latex'   => 'application/x-latex',
    'mdb'     => 'application/x-msaccess',
    'crd'     => 'application/x-mscardfile',
    'clp'     => 'application/x-msclip',
    'dll'     => 'application/x-msdownload',
    'm13'     => 'application/x-msmediaview',
    'm14'     => 'application/x-msmediaview',
    'mvb'     => 'application/x-msmediaview',
    'wmf'     => 'application/x-msmetafile',
    'mny'     => 'application/x-msmoney',
    'pub'     => 'application/x-mspublisher',
    'scd'     => 'application/x-msschedule',
    'trm'     => 'application/x-msterminal',
    'wri'     => 'application/x-mswrite',
    'pma'     => 'application/x-perfmon',
    'pmc'     => 'application/x-perfmon',
    'pml'     => 'application/x-perfmon',
    'pmr'     => 'application/x-perfmon',
    'pmw'     => 'application/x-perfmon',
    'p12'     => 'application/x-pkcs12',
    'pfx'     => 'application/x-pkcs12',
    'p7b'     => 'application/x-pkcs7-certificates',
    'spc'     => 'application/x-pkcs7-certificates',
    'p7r'     => 'application/x-pkcs7-certreqresp',
    'p7c'     => 'application/x-pkcs7-mime',
    'p7m'     => 'application/x-pkcs7-mime',
    'p7s'     => 'application/x-pkcs7-signature',
    'sh'      => 'application/x-sh',
    'shar'    => 'application/x-shar',
    'sit'     => 'application/x-stuffit',
    'sv4cpio' => 'application/x-sv4cpio',
    'sv4crc'  => 'application/x-sv4crc',
    'tar'     => 'application/x-tar',
    'tcl'     => 'application/x-tcl',
    'tex'     => 'application/x-tex',
    'texi'    => 'application/x-texinfo',
    'texinfo' => 'application/x-texinfo',
    'roff'    => 'application/x-troff',
    't'       => 'application/x-troff',
    'tr'      => 'application/x-troff',
    'man'     => 'application/x-troff-man',
    'me'      => 'application/x-troff-me',
    'ms'      => 'application/x-troff-ms',
    'ustar'   => 'application/x-ustar',
    'src'     => 'application/x-wais-source',
    'cer'     => 'application/x-x509-ca-cert',
    'crt'     => 'application/x-x509-ca-cert',
    'der'     => 'application/x-x509-ca-cert',
    'pko'     => 'application/ynd.ms-pkipko',
    'zip'     => 'application/zip',
    'au'      => 'audio/basic',
    'snd'     => 'audio/basic',
    'mid'     => 'audio/mid',
    'rmi'     => 'audio/mid',
    'mp3'     => 'audio/mpeg',
    'aif'     => 'audio/x-aiff',
    'aifc'    => 'audio/x-aiff',
    'aiff'    => 'audio/x-aiff',
    'm3u'     => 'audio/x-mpegurl',
    'ra'      => 'audio/x-pn-realaudio',
    'ram'     => 'audio/x-pn-realaudio',
    'wav'     => 'audio/x-wav',
    'bmp'     => 'image/bmp',
    'cod'     => 'image/cis-cod',
    'gif'     => 'image/gif',
    'ief'     => 'image/ief',
    'jpeg'    => 'image/jpeg',
    'jpe'     => 'image/jpeg',
    'jpg'     => 'image/jpeg',
    'jfif'    => 'image/pipeg',
    'svg'     => 'image/svg+xml',
    'tiff'    => 'image/tiff',
    'tif'     => 'image/tiff',
    'ras'     => 'image/x-cmu-raster',
    'cmx'     => 'image/x-cmx',
    'ico'     => 'image/x-icon',
    'pnm'     => 'image/x-portable-anymap',
    'pbm'     => 'image/x-portable-bitmap',
    'pgm'     => 'image/x-portable-graymap',
    'ppm'     => 'image/x-portable-pixmap',
    'rgb'     => 'image/x-rgb',
    'xbm'     => 'image/x-xbitmap',
    'xpm'     => 'image/x-xpixmap',
    'xwd'     => 'image/x-xwindowdump',
    'mht'     => 'message/rfc822',
    'mhtml'   => 'message/rfc822',
    'nws'     => 'message/rfc822',
    'css'     => 'text/css',
    '323'     => 'text/h323',
    'html'    => 'text/html',
    'htm'     => 'text/html',
    'stm'     => 'text/html',
    'uls'     => 'text/iuls',
    'bas'     => 'text/plain',
    'c'       => 'text/plain',
    'h'       => 'text/plain',
    'txt'     => 'text/plain',
    'rtx'     => 'text/richtext',
    'sct'     => 'text/scriptlet',
    'tsv'     => 'text/tab-separated-values',
    'htt'     => 'text/webviewhtml',
    'htc'     => 'text/x-component',
    'etx'     => 'text/x-setext',
    'vcf'     => 'text/x-vcard',
    'mp2'     => 'video/mpeg',
    'mpa'     => 'video/mpeg',
    'mpeg'    => 'video/mpeg',
    'mpe'     => 'video/mpeg',
    'mpg'     => 'video/mpeg',
    'mpv2'    => 'video/mpeg',
    'mov'     => 'video/quicktime',
    'qt'      => 'video/quicktime',
    'lsf'     => 'video/x-la-asf',
    'lsx'     => 'video/x-la-asf',
    'asf'     => 'video/x-ms-asf',
    'asr'     => 'video/x-ms-asf',
    'asx'     => 'video/x-ms-asf',
    'avi'     => 'video/x-msvideo',
    'movie'   => 'video/x-sgi-movie',
    'flr'     => 'x-world/x-vrml',
    'vrml'    => 'x-world/x-vrml',
    'wrl'     => 'x-world/x-vrml',
    'wrz'     => 'x-world/x-vrml',
    'xaf'     => 'x-world/x-vrml',
    'xof'     => 'x-world/x-vrml'
  ];


  /**
   * Did parse() parse a correct mimetype?
   *
   * @var boolean
   */
  private $_is_valid = false;


  /**
   * Main type
   *
   * @var string
   */
  private $_type;


  /**
   * Subtype
   *
   * @var string
   */
  private $_subtype;


  /**
   * Parameters for the line (map)
   *
   * @var string[string]
   */
  private $_parameters;



  /**
   * Constructor
   *
   * @param string $str a mime-type line (e.g. text/plain;charset=utf-8)
   */
  public function __construct( $str )
  {
    if( $str )
    {
      $this->parse( $str );
    }
  }



  /**
   * Parse a mime-type line
   *
   * @param string $line
   * @return boolean true if parsed or false if unable.
   */
  public function parse( $line )
  {
    if( !preg_match( "%([\w\-\+\.]+)/([\w\-\+\.]+)(;.*)?%", $line, $matches ) )
    {
      $this->_is_valid = false;
      return false;
    }

    $this->_type = $matches[ 1 ];
    $this->_subtype = $matches[ 2 ];

    if( isset( $matches[ 3 ] ) && strlen( $matches[ 3 ] ) > 0 )
    {
      $params = substr( $matches[ 3 ], 1 ); //eat ;
      $parts = explode( ';', $params );

      foreach( $parts as $part )
      {
        $parline = explode( '=', $part, 2 );
        $this->_parameters[ $parline[ 0 ] ] = $parline[ 1 ];
      }
    }

    $this->_is_valid = true;
    return true;
  }



  /**
   * Get the type and subtype as "type/subtype"
   *
   * @return string
   */
  public function get_type_and_subtype()
  {
    return $this->_type . '/' . $this->_subtype;
  }



  /**
   * Did we parse a correct mime-type?
   *
   * @return bool
   */
  public function is_valid()
  {
    return $this->_is_valid;
  }



  /**
   * Method to set type value
   *
   * @param string $type
   */
  public function set_type( $type )
  {
    $this->_type = $type;
  }



  /**
   * Method to get type value
   *
   * @return string
   */
  public function get_type()
  {
    return $this->_type;
  }



  /**
   * Method to set subtype value
   *
   * @param string $subtype
   */
  public function set_subtype( $subtype )
  {
    $this->_subtype = $subtype;
  }



  /**
   * Method to get subtype value
   *
   * @return string
   */
  public function get_subtype()
  {
    return $this->_subtype;
  }



  /**
   * Method to set parameters value
   *
   * @param string[string] $parameters
   */
  public function set_parameters( $parameters )
  {
    $this->_parameters = $parameters;
  }



  /**
   * Return the (most usual) extension related to a given mime type
   *
   * @static
   * @param string|\Common\Utils\Mimetype $mime_type The mime type
   * @return string|null The type or null if not found
   */
  public static function extension_for_mime( $mime_type )
  {
    if( $mime_type instanceof \Common\Utils\Mimetype )
    {
      $type = $mime_type;
    }
    else
    {
      $type = new self( $mime_type );
    }

    if( !$type->is_valid() )
    {
      return null;
    }

    if( isset( self::$_mime_to_extensions[ $type->get_type_and_subtype() ] ) )
    {
      return self::$_mime_to_extensions[ $type->get_type_and_subtype() ];
    }

    return null;
  }



  /**
   * Given a file extension (without .), return its target mime-type
   *
   * @static
   * @param string $extension
   * @return string the mime-type or null if not found
   */
  public static function mimetype_for_extension( $extension )
  {
    if( isset( self::$_extensions_to_mime[ $extension ] ) )
    {
      return self::$_extensions_to_mime[ $extension ];
    }

    return null;
  }



  /**
   * Get a guessed extension for a file from the type
   *
   * @return string or null if not found
   */
  public function get_extension()
  {
    return self::extension_for_mime( $this );
  }



  /**
   * Method to get parameters value
   *
   * @return string[string]
   */
  public function get_parameters()
  {
    return $this->_parameters;
  }

}
