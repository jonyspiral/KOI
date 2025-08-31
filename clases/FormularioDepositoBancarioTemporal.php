<?php

/**
 * Caja $caja
 * CuentaBancaria $cuentaBancaria
 * Array $cheques
 */

class FormularioDepositoBancarioTemporal extends Formulario {

	public $id;
	public $caja;
	public $cuentaBancaria;
	public $fecha;
	public $esVentaCheque;
	public $numeroBoleta;
	public $efectivo;
	public $cheques;
	public $esDepositoTemporal;

	public function __construct() {	
		parent::__construct();
		$this->nombreDocumento = 'DepositoBancario';
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
			$this->pdf->fileName = ($this->esVentaCheque ? 'VentaCheques' : $this->nombreDocumento) . '_' . $this->id;
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
		$_POST['form_cuentaBancariaNombre'] = $this->cuentaBancaria->nombreCuenta;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_ventaDeCheque'] = $this->esVentaCheque;
		$_POST['form_numeroBoleta'] = $this->numeroBoleta;
		$_POST['form_efectivo'] = $this->efectivo;
		$_POST['form_cheques'] = $this->cheques;
		$_POST['es_deposito_temporal'] = $this->esDepositoTemporal;
	}

	//GETS y SETS
}

?>