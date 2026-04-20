<?php

class FormularioNotaDeDebito extends Formulario {

	public function __construct() {	
		parent::__construct();
		$this->nombreDocumento = 'NotaDeDebito';
	}

	protected function enviarDatos() {
		parent::enviarDatos();
	}

	//GETS y SETS
}

?>