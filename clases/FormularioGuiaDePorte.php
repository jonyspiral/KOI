<?php

class FormularioGuiaDePorte extends Formulario {
	//Los que están comentados es porque están definidos en la clase padre, Formulario

	public	$numeroGuia;
	//public	$fecha;
	public	$senores;
	public	$clienteNro;
	public	$direccionCalle;
	public	$direccionNumero;
	public	$direccionPiso;
	public	$direccionDpto;
	public	$direccionLocalidad;
	public	$direccionCP;
	//public	$cuit;
	public	$condicionIVA;
	public	$transportistaSenor;
	public	$transportistaDomicilio;
	public	$transportistaCUIT;
	public	$transportistaDNI;
	//public	$detalle; //Array de objetos [{cantidad: 2, descripcion: 'Avril Woman'}, {.}]

	public function __construct() {	
		parent::__construct();
	}

	protected function enviarDatos() {
		$_POST['form_letra'] = 'X';
		$_POST['form_numeroGuia'] = $this->numeroGuia;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_senores'] = $this->senores;
		$_POST['form_clienteNro'] = $this->clienteNro;
		$_POST['form_direccionCalle'] = $this->direccionCalle;
		$_POST['form_direccionNumero'] = $this->direccionNumero;
		$_POST['form_direccionPiso'] = $this->direccionPiso;
		$_POST['form_direccionDpto'] = $this->direccionDpto;
		$_POST['form_direccionLocalidad'] = $this->direccionLocalidad;
		$_POST['form_direccionCP'] = $this->direccionCP;
		$_POST['form_cuit'] = $this->cuit;
		$_POST['form_nombreCondicionIVA'] = $this->condicionIVA;
		$_POST['form_transportistaSenor'] = $this->transportistaSenor;
		$_POST['form_transportistaDomicilio'] = $this->transportistaDomicilio;
		$_POST['form_transportistaCUIT'] = $this->transportistaCUIT;
		$_POST['form_transportistaDNI'] = $this->transportistaDNI;
		$_POST['form_detalle'] = $this->detalle;
	}

	protected function crearPdf(){
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modeloGuiaDePorte' . (Config::encinitas() ? '_ncnts' : '') . 'php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = 'Formulario_guia_de_porte_' . $this->numeroGuia;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	//GETS y SETS
}

?>