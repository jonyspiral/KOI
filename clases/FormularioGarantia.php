<?php

/**
 * Proveedor		$proveedor
 */

class FormularioGarantia extends Formulario {

	public	$id;
	public	$cliente;
	public	$order;
	public	$motivo;

	public function __construct() {
		parent::__construct();
		$this->nombreDocumento = 'Garantia';
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
			$this->pdf->fileName = $this->nombreDocumento . '_' . ($this->order->id ? 'order_' . $this->order->id : 'cliente_' . $this->cliente->id);
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['form_id'] = $this->id;
		$_POST['form_cliente'] = $this->cliente;
		$_POST['form_order'] = $this->order;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_detalle'] = $this->detalle;
		$_POST['form_motivo'] = $this->motivo;
		$_POST['form_observaciones'] = $this->observaciones;
	}

	//GETS y SETS
}

?>