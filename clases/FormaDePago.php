<?php

class FormaDePago extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$dias;
	public		$nombre;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>