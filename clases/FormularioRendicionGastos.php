<?php

class FormularioRendicionGastos extends Formulario {

	public $id;
	public $fecha;
	public $proveedor;
	public $observaciones;
	public $aplicaciones;
	public $gastitos;

	public function __construct() {	
		parent::__construct();
		$this->nombreDocumento = 'RendicionGastos';
	}

	public function abrir(){
		$this->crearPdf();
		$this->pdf->open();
	}

	protected function crearPdf(){
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modelo' . $this->nombreDocumento . (Config::encinitas() ? '_ncnts' : '') . '.php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = $this->nombreDocumento . '_' . $this->id;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['form_id'] = $this->id;
		$_POST['form_empresa'] = $this->empresa;
		$_POST['form_importe_total'] = $this->importeTotal;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_observaciones'] = $this->observaciones;
		$_POST['form_aplicaciones'] = $this->aplicaciones;
		$_POST['form_gastitos'] = $this->gastitos;
	}

	//GETS y SETS
}

?>