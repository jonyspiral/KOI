<?php

/**
 * Caja			$caja
 * Personal 	$responsable
 * Array 		$cheques
 */

class FormularioCobroChequesVentanilla extends Formulario {

	public $id;
	public $caja;
	public $responsable;
	public $fecha;
	public $cheques;
	public $esTemporal;

	public function __construct() {	
		parent::__construct();
		$this->nombreDocumento = 'CobroChequesVentanilla';
	}

	public function abrir(){
		$this->crearPdf();
		$this->pdf->open(false);
	}

	protected function crearPdf(){
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modelo' . $this->nombreDocumento . (Config::encinitas() ? '_ncnts' : '') . '.php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = 'CobroChequesVentanilla_' . $this->id;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['form_id'] = $this->id;
		$_POST['form_cajaOrigenId'] = $this->caja->id;
		$_POST['form_cajaOrigenNombre'] = $this->caja->nombre;
		$_POST['form_responsable'] = $this->responsable;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_total'] = $this->importeTotal;
		$_POST['form_cheques'] = $this->cheques;
		$_POST['form_esTemporal'] = $this->esTemporal;
	}

	//GETS y SETS
}

?>