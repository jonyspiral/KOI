<?php

class GrupoEmpresa extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$comisionPorVentas;
	public		$nombre;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>