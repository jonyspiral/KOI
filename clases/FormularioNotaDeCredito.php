<?php

class FormularioNotaDeCredito extends Formulario {
	public	$cantidadPares;

	public function __construct() {
		parent::__construct();
		$this->nombreDocumento = 'NotaDeCredito';
	}

	protected function enviarDatos() {
		$_POST['form_cantidadPares'] = $this->cantidadPares;
		parent::enviarDatos();
	}

	//GETS y SETS
}

?>