<?php

class Curva extends Base {
	const		_primaryKey = '["id"]';

	public		$id;			//Es un autonumérico
	public		$nombre;
	public		$tipoDeCurva;	//"C"omercial o "P"roducción ("P" no las voy a usar)
	public		$anulado;
	public		$cantidad;		//Array de 1 a 10

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
}

?>