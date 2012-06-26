<?php

$libroot = getenv( 'LIBROOT' ) ?: '/usr/local/lib';

$paths = array(
  "libroot/vendor/Zend/1.11.7",
  realpath( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src')
);

set_include_path( get_include_path() . PATH_SEPARATOR . implode( PATH_SEPARATOR, $paths ) );

// Register Zend autoloader
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// Register Common autoload
require 'Common/Utils/Autoload.php';
$autoloader = new \Common\Utils\Autoload();
$autoloader->register();

