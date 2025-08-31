<?php

/**
 * @property Proveedor		$proveedor
 * @property Documento		$documento
 * @property int			$numero
 */

class CuentaCorrienteHistoricaDocumentoProveedor extends Base {
	const		_primaryKey = '["idDocumento"]';

	public		$idDocumento;
	protected	$_documento;
	public		$idProveedor;
	protected	$_proveedor;
	public		$empresa;
	public		$puntoDeVenta;
	public		$tipoDocumento;	//Enum TiposDocumento
	public		$letra;
	protected	$_numero;
	public		$numeroDocumento;
	public		$numeroComprobante;
	public		$fecha;
	public		$detalle;
	public		$importeTotal;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getDocumento() {
		if (!isset($this->_documento)){
			$this->_documento = Factory::getInstance()->getDocumentoProveedor($this->idDocumento);
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