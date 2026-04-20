<?php

class FajaHoraria extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$nombre;
	public		$horarioEntrada;
	public		$horarioSalida;
	
	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>