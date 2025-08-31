<?php

/**
 * @property Cliente	$cliente
 * @property array		$cheques
 * @property array		$retenciones
 */

class FormularioRecibo extends Formulario {

	public $id;
	public $fecha;
	public $cliente;
	public $recibidoDe;
	public $montoEfectivo;
	public $montoCheques;
	public $montoTransferencias;
	public $montoTotal;
	public $cheques;
	public $retenciones;
	public $aplicaciones;

	public function __construct() {	
		parent::__construct();
		$this->nombreDocumento = 'Recibo';
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
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_cliente_id'] = $this->cliente->id;
		$_POST['form_recibido_de'] = $this->recibidoDe;
		$_POST['form_cliente_nombre'] = $this->cliente->razonSocial;
		$_POST['form_monto_efectivo'] = $this->montoEfectivo;
		$_POST['form_monto_cheques'] = $this->montoCheques;
		$_POST['form_monto_transferencias'] = $this->montoTransferencias;
		$_POST['form_monto_total'] = $this->montoTotal;
		$_POST['form_cheques'] = $this->cheques;
		$_POST['form_retenciones'] = $this->retenciones;
		$_POST['form_empresa'] = $this->empresa;
		$_POST['form_aplicaciones'] = $this->aplicaciones;
		$_POST['form_observaciones'] = $this->observaciones;
	}

	//GETS y SETS
}

?>