<?php

error_reporting(E_ERROR | E_PARSE);
//ini_set('display_errors', 0);

/**
 * Path to the webservice base directory.
 */
define('WSNAME', 'ecommerce');

/**
 * Path to the webservice base directory.
 */
define('BASEPATH', realpath(getcwd() . '/' . WSNAME . '/') . DIRECTORY_SEPARATOR);

/**
 * Path to the application directory.
 */
define('APPPATH', BASEPATH . 'api/');

/**
 * The path to the core.
 */
define('COREPATH', BASEPATH . 'core/');

/**
 * The path to the core.
 */
define('LOGSPATH', BASEPATH . 'logs/');

// Load in the Autoloader
require COREPATH . 'autoloader.php';

// Bootstrap the framework DO NOT edit this
require COREPATH . 'bootstrap.php';

require_once BASEPATH . '../../includes.php';

// Register the autoloader
Ecommerce_Core_Autoloader::register();

try {
	$response = Ecommerce_Core_Request::forge()->execute()->response();
} catch (Ecommerce_Core_HttpNotFoundException $e) {
	$response = $e->response()->set_header('Content-Type', 'application/json');
	//$response = Ecommerce_Core_Request::forge('404')->execute()->response();
}

$response->send(true);
