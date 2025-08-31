<?php

class PersonalOperador extends Operador {

	public function __construct() {
		parent::__construct();
		$this->tipo = TiposOperador::personal;
	}

	//GETS y SETS
}

?>