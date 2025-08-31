<?php

class CategoriaCalzadoUsuario extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$nombre;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>