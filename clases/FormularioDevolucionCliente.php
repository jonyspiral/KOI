<?php

/**
 * Cliente		$cliente
 */

class FormularioDevolucionCliente extends Formulario {

	public	$id;
	public	$cliente;
	public	$order;

	public function __construct() {
		parent::__construct();
		$this->nombreDocumento = 'DevolucionCliente';
	}

	public function abrir() {
		$this->crearPdf();
		$this->pdf->open();
	}

	protected function crearPdf() {
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modelo' . $this->nombreDocumento . '.php');
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
		$_POST['form_cliente'] = $this->cliente;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_detalle'] = $this->detalle;
		$_POST['form_observaciones'] = $this->observaciones;
	}

	//GETS y SETS
}

?>