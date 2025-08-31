<?php

class RubroIva extends Base {
	const		_primaryKey = '["id"]';

	//Esta clase es la que relaciona los artículos con el porcentaje de IVA que llevan según la condición de IVA del cliente
	public		$id;
	public		$nombre;
	public		$anulado;
	public		$columnaIva;	//Es el número de columna de la cual tiene que obtener el IVA

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>