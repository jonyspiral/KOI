<?php

/**
 * @property Cliente		$cliente
 * @property Documento		$documento
 * @property int			$numero
 */

class CuentaCorrienteHistoricaDocumento extends Base {
	public		$idCliente;
	protected	$_cliente;
	public		$empresa;
	public		$puntoDeVenta;
	public		$tipoDocumento;	//Enum TiposDocumento
	public		$letra;
	protected	$_numero;
	public		$numeroDocumento;
	public		$numeroComprobante;
	public		$fecha;
	public		$fechaVencimiento;
	public		$detalle;
	public		$importeTotal;
	public		$diasPromedioPago;
	protected	$_documento;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getDocumento() {
		if (!isset($this->_documento)){
			$this->_documento = Factory::getInstance()->getDocumento($this->empresa, $this->puntoDeVenta, $this->tipoDocumento, $this->numero, $this->letra);
		}
		return $this->_documento;
	}
	protected function setDocumento($documento) {
		$this->_documento = $documento;
		return $this;
	}
	protected function getNumero() {
		if (!isset($this->_numero)){
			$this->_numero = (isset($this->numeroComprobante) ? $this->numeroComprobante : $this->numeroDocumento);
		}
		return $this->_numero;
	}
	protected function setNumero($numero) {
		$this->_numero = $numero;
		return $this;
	}
}

?>