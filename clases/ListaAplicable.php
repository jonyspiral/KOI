<?php

class ListaAplicable extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$idArticulo;
	public		$idColorArticulo;
	public		$precioArticulo;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>