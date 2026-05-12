<?php

session_start();
session_cache_limiter("nocache");

ini_set('display_errors', 'Off');
header('Content-Type: application/json;');

$url = ltrim($_SERVER['PATH_INFO'], '/');
$urlArray = empty($url) ? null : explode('/', $url);
$wsPath = count($urlArray) > 1 ? $urlArray[0] : '';

if (empty($url) || !$wsPath || !file_exists($wsPath . DIRECTORY_SEPARATOR . 'bootstrap.php')) {
	echo json_encode(array('error' => 'An error occurred while looking for the webservice "' . $wsPath . '"'));
	exit;
}

//Path a la carpeta del webservice llamado
define('WSPATH', realpath($wsPath) . DIRECTORY_SEPARATOR);

// Boot the app
require WSPATH . 'bootstrap.php';