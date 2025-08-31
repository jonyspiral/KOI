<?php

/**
 * @property PedidoCliente  $pedido
 */

class FormularioPedidoCliente extends Formulario {

	public	$pedido;

	public function __construct() {
		parent::__construct();
		$this->nombreDocumento = 'PedidoCliente';
	}

	protected function crearPdf() {
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modelo' . $this->nombreDocumento . (Config::encinitas() ? '_ncnts' : '') . '.php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = $this->nombreDocumento . '_' . $this->pedido->cliente->id . '_' . $this->pedido->id;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['form_pedido'] = $this->pedido;
	}

	//GETS y SETS
}

?>