<?php
header("Content-Type: text/plain; charset=utf-8");
var_dump("entro a master");

require_once('premaster.php');
 var_dump("luego del include de premaster ");
var_dump("user: ".Usuario::logueado());
function puedeSinLoguear($pagename) {
	//Esto es para las funcionalidades que no necesitan login
	$arrayExcepciones = array(
		'api',
		'fichaje'
	);
	if (in_array($pagename, $arrayExcepciones)) {
		return true;
	}
	return false;
}

function findRealPath($filename) {
	if (realpath($filename) == $filename) {
		return $filename;
	}
	$paths = explode(PATH_SEPARATOR, get_include_path());
	foreach ($paths as $path) {
		if (substr($path, -1) == DIRECTORY_SEPARATOR) {
			$fullpath = $path . $filename;
		} else {
			$fullpath = $path . DIRECTORY_SEPARATOR . $filename;
		}
		if (file_exists($fullpath)) {
			return $fullpath;
		}
	}
	return false;
}

$prefix = Usuario::logueado(true)->esCliente() ? 'cliente/' : '';
$pagename = $prefix . 'index'; //Default
$auxPagename = Funciones::get('pagename');
$idBuscar = Funciones::get('buscar');
if (Usuario::logueado() || puedeSinLoguear($auxPagename)) {
	$tit = explode('/', $auxPagename);
	$tit = ucfirst(implode(' ', explode('_', $tit[count($tit) - 1])));
	if (isset($auxPagename)) {
		$pagename = $prefix . Funciones::get('pagename');
	}
	if (file_exists($pagename . '.php')) {
		require_once($pagename . '.php');
	} else {
		if (!findRealPath('content/' . $pagename . '.php')) {
			if (findRealPath('content/' . $pagename . '/index.php')) {
                $pagename .= '/index';
			} else {
				$pagename = $prefix . 'index';
			}
		}
		if (!Usuario::logueado(true)->puede(substr($pagename, 0, -5))){
			$pagename = $prefix . 'index';
		}

        if (Usuario::logueado(true)->esCliente()) {
            include_once('content/cliente/main.php');
        } else {
            include_once('main.php');
        }
	}
} else { //Login
    include_once('login.php');
}
?>