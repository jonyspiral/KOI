<?php

/**
 * @property int  $usuarioId
 */

class FormularioFavoritoCliente extends Formulario {

	public $usuarioId;

	public function __construct() {
		parent::__construct();
		$this->nombreDocumento = 'FavoritoCliente';
	}

	protected function crearPdf() {
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modelo' . $this->nombreDocumento . (Config::encinitas() ? '_ncnts' : '') . '.php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = $this->nombreDocumento . '_' . $this->usuarioId;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['usuario_id'] = $this->getUsuarioId();
	}

	public function abrir(){
		$this->crearPdf();
		$this->pdf->open(false);
		//$this->pdf->deleteFiles();//Lo dejo? Lo saco? Lo guardo en otro lado el PDF? Sí, guardarlo en otro lado sería lo mejor!
	}

	//GETS y SETS
	public function setUsuarioId($usuarioId)
	{
		$this->usuarioId = $usuarioId;
	}

	public function getUsuarioId()
	{
		return $this->usuarioId;
	}
}

?>