<?php

spl_autoload_register('autoload');

function autoload($class) {
	try {
		$paths = array(Config::pathBase . 'clases/', Config::pathBase . 'factory/', Config::pathBase . 'test/');
		if (($pos = strrpos($class, '_')) !== false) {
			$paths[] = Config::pathBase . 'clases/' . strtolower(str_replace('_', '/', substr($class, 0, $pos))) . '/';
			$class = implode('_', array_map('ucfirst', explode('_', $class)));
		}
		foreach ($paths as $path) {
			if (file_exists($path . ucfirst($class) . '.php')){
				require_once($path . ucfirst($class) . '.php');
				return;
			}
		}
	} catch (Exception $ex) {
	}
}

require_once('factory/Config.php'); //Necesito que config se cargue primero para que ande el Autoload.
require_once('factory/Enums.php'); //No carga con el Autoload.

require_once('factory/Datos.php');
require_once('factory/Funcionalidades.php');
require_once('factory/Factory.php');
require_once('factory/FactoryExceptions.php');
require_once('factory/Funciones.php');
require_once('factory/Mapper.php');

/*
require_once('clases/Base.php');

require_once('clases/Cliente.php');
require_once('clases/CondicionIva.php');
*/
?>