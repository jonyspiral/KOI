<?php

/**
 * @property DocumentoProveedor		$documentoProveedor
 * @property Impuesto				$impuesto
 */

class ImpuestoPorDocumentoProveedor extends Base {
	const		_primaryKey = '["idDocumentoProveedor", "idImpuesto"]';

	public		$idDocumentoProveedor;
	protected	$_documentoProveedor;
	public		$idImpuesto;
	protected	$_impuesto;
	public		$porcentaje;
	public		$importe;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getDocumentoProveedor() {
		if (!isset($this->_documentoProveedor)){
			$this->_documentoProveedor = Factory::getInstance()->getDocumentoProveedor($this->idDocumentoProveedor);
		}
		return $this->_documentoProveedor;
	}
	protected function setDocumentoProveedor($documentoProveedor) {
		$this->_documentoProveedor = $documentoProveedor;
		return $this;
	}

	protected function getImpuesto() {
		if (!isset($this->_impuesto)){
			$this->_impuesto = Factory::getInstance()->getImpuesto($this->idImpuesto);
		}
		return $this->_impuesto;
	}
	protected function setImpuesto($impuesto) {
		$this->_impuesto = $impuesto;
		return $this;
	}
}

?>