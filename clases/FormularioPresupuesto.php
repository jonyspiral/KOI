<?php

/**
 * Proveedor		$proveedor
 */

class FormularioPresupuesto extends Formulario {

	public $id;
	public $fecha;
	public $proveedor;
	public $montoTotal;

	public function __construct() {	
		parent::__construct();
		$this->nombreDocumento = 'Presupuesto';
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
			$this->pdf->fileName = 'Pedido_de_cotizacion_' . $this->id;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['form_id'] = $this->id;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_proveedor_id'] = $this->proveedor->id;
		$_POST['form_proveedor_nombre'] = $this->proveedor->razonSocial;
		$_POST['form_detalle'] = $this->detalle;
		$_POST['form_monto_total'] = $this->montoTotal;
		$_POST['form_observaciones'] = $this->observaciones;
	}

	//GETS y SETS
}

?>