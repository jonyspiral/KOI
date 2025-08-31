<?php

class Zona extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$descripcion;
	public		$nombre;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>