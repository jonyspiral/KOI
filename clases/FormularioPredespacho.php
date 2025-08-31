<?php

/**
 * Proveedor		$proveedor
 */

class FormularioPredespacho extends Formulario {

	public	$esPedido;
	public	$idPedido;
	public	$idCliente;
	public	$idSucursal;

	public function __construct() {
		parent::__construct();
		$this->nombreDocumento = 'Predespacho';
	}

	public function abrir(){
		$this->crearPdf();
		$this->pdf->open();
	}

	protected function crearPdf(){
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modelo' . $this->nombreDocumento . '.php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = $this->nombreDocumento . '_' . ($this->esPedido ? 'pedido_' . $this->idPedido : 'cliente_' . $this->idCliente);
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['form_es_pedido'] = $this->esPedido;
		$_POST['form_id_cliente'] = $this->idCliente;
		$_POST['form_id_sucursal'] = $this->idSucursal;
		$_POST['form_id_pedido'] = $this->idPedido;
		$_POST['form_detalle'] = $this->detalle;
	}

	//GETS y SETS
}

?>