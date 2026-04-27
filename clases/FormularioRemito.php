<?php

class FormularioRemito extends Formulario {
	//Los que están comentados es porque están definidos en la clase padre, Formulario

	//public	$empresa;
	//public	$numero;
	//public	$fecha;
	public	$nombreCliente;
	public	$idCliente;
	//public	$direccion;
	public	$localidad;
	//public	$cuit;
	public	$idCondicionIva;
	public	$valorDeclarado;
	public	$cantidadPares;
	public	$cantidadBultos;
	public	$transportistaNombre;
	public	$transportistaDomicilio;
	public	$horarioEntrega1;
	public	$horarioEntrega2;
	//public	$detalle; //Array de objetos [{codArt: 350, nombreArt: 'Avril Woman', codColor: 'V', 'nombreColor': 'verde', cantidad: 2}, {.}]

	public function __construct() {	
		parent::__construct();
	}

	protected function enviarDatos() {
		$_POST['form_empresa'] = $this->empresa; //No sale impreso, pero es para saber qué documento se imprime
		$_POST['form_numero'] = $this->numero; //No sale impreso, pero es para el nombre del archivo
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_nombreCliente'] = $this->nombreCliente;
		$_POST['form_idCliente'] = $this->idCliente;
		$_POST['form_direccion'] = $this->direccion;
		$_POST['form_localidad'] = $this->localidad;
		$_POST['form_cuit'] = $this->cuit;
		$_POST['form_idCondicionIva'] = $this->idCondicionIva;
		$_POST['form_valorDeclarado'] = $this->valorDeclarado;
		$_POST['form_cantidadPares'] = $this->cantidadPares;
		$_POST['form_cantidadBultos'] = $this->cantidadBultos;
		$_POST['form_transportistaNombre'] = $this->transportistaNombre;
		$_POST['form_transportistaDomicilio'] = $this->transportistaDomicilio;
		$_POST['form_detalle'] = $this->detalle;
		$_POST['form_horarioEntrega1'] = $this->horarioEntrega1;
		$_POST['form_horarioEntrega2'] = $this->horarioEntrega2;
	}

	protected function crearPdf(){
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			//Acá hay que definir qué modelo de Factura se usa según si es empresa 1 o 2, o si eso se lo delego al documento!!!
			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modeloRemito.php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = 'Remito_' . $this->numero . '_' . $this->empresa;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	//GETS y SETS
}

?>