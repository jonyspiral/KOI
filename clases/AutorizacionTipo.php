<?php

class AutorizacionTipo extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$cantidad;		//Es la cantidad de autorizaciones que se necesitan para autorizar algo
	public		$nombre;
	public		$nombreObjeto;	//Es para hacer un Factory::getInstance()->get$nombreObjeto($idEspecifico) y así obtener el objeto

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>