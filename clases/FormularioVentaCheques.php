<?php

/**
 * Caja $caja
 * CuentaBancaria $cuentaBancaria
 * Array $cheques
 */

class FormularioVentaCheques extends Formulario {

	public $id;
	public $cajaSalida;
	public $cajaEntrada;
	public $cuentaBancaria;
	public $fecha;
	public $cheques;
	public $esDepositoTemporal;

	public function __construct() {	
		parent::__construct();
		$this->nombreDocumento = 'VentaCheques';
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
			$this->pdf->fileName = 'VentaCheques_' . $this->id;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['form_id'] = $this->id;
		$_POST['form_cajaOrigenId'] = $this->cajaSalida->id;
		$_POST['form_cajaOrigenNombre'] = $this->cajaSalida->nombre;
		$_POST['form_cajaDestinoId'] = $this->cajaEntrada->id;
		$_POST['form_cajaDestinoNombre'] = $this->cajaEntrada->nombre;
		$_POST['form_cuentaBancariaNombre'] = $this->cuentaBancaria->nombreCuenta;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_total'] = $this->importeTotal;
		$_POST['form_cheques'] = $this->cheques;
		$_POST['es_deposito_temporal'] = $this->esDepositoTemporal;
	}

	//GETS y SETS
}

?>