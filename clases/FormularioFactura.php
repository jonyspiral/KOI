<?php

class FormularioFactura extends Formulario {

	public	$cantidadPares;
	public	$remitosIncluidos; //Array con los número de remitos que componen la factura

	public function __construct() {	
		parent::__construct();
		$this->nombreDocumento = 'Factura';
	}

	protected function enviarDatos() {
		parent::enviarDatos();
		$_POST['form_cantidadPares'] = $this->cantidadPares;
		$_POST['form_remitosIncluidos'] = $this->remitosIncluidos;
	}

	//GETS y SETS
}

?>