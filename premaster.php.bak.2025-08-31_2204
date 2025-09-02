<?php

session_start();
session_cache_limiter("nocache");
error_reporting(0);
//header('Content-type: text/html; charset=utf-8;');
header('Content-type: text/html; charset=iso-8859-1;');

var_dump("entro a premaster");
require_once('includes.php');

var_dump("luego de includes");

function fatal_handler() {
	if(($error = error_get_last()) !== null) {
		Logger::addError('[' . get_err_type($error["type"]) . '] "' . $error["message"] . '" in file ' . $error["file"] . ' at line ' . $error["line"]);
	}
}

register_shutdown_function('fatal_handler');

//Login
try {
    UsuarioLogin::login();
    if ($_SERVER['REMOTE_ADDR'] == '190.104.245.136' || $_SERVER['REMOTE_ADDR'] == '190.190.22.173') {
		Logger::addInfo('U: ' . Usuario::logueado()->id);
	}
} catch (LoginFailException $ex){
    $onDocumentReady = 'loginFail("' . $ex->getMessage() . '");';
} catch (Exception $ex){
    throw $ex;
}

function get_err_type($type) {
	switch($type) {
		case E_ERROR:
			return 'E_ERROR';
		case E_WARNING:
			return 'E_WARNING';
		case E_PARSE:
			return 'E_PARSE';
		case E_NOTICE:
			return 'E_NOTICE';
		case E_CORE_ERROR:
			return 'E_CORE_ERROR';
		case E_CORE_WARNING:
			return 'E_CORE_WARNING';
		case E_COMPILE_ERROR:
			return 'E_COMPILE_ERROR';
		case E_COMPILE_WARNING:
			return 'E_COMPILE_WARNING';
		case E_USER_ERROR:
			return 'E_USER_ERROR';
		case E_USER_WARNING:
			return 'E_USER_WARNING';
		case E_USER_NOTICE:
			return 'E_USER_NOTICE';
		case E_STRICT:
			return 'E_STRICT';
		case E_RECOVERABLE_ERROR:
			return 'E_RECOVERABLE_ERROR';
		case E_DEPRECATED:
			return 'E_DEPRECATED';
		case E_USER_DEPRECATED:
			return 'E_USER_DEPRECATED';
	}
	return $type;
}
?>