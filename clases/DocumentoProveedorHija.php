<?php

/**
 * @property DocumentoProveedorAplicacionHaber		$documentoCancelatorio
 * @property DocumentoProveedorAplicacionDebe		$madre
 * @property Usuario								$usuario
 */

class DocumentoProveedorHija extends Base {
	const		_primaryKey = '["id"]';
	const		deltaErrorDesaplicar = 0.03;

	public		$id;
	public		$anulado;
	public		$importe;
	public		$empresa;
	public		$idDocumentoCancelatorio;
	public		$cancelTipoDocumento;
	protected	$_documentoCancelatorio;
	public		$idMadre;
	protected	$_madre;
	public		$idUsuario;
	protected	$_usuario;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function desaplicar() {
		$this->madre->importePendiente += $this->importe;
		$this->documentoCancelatorio->importePendiente += $this->importe;

		if(abs($this->madre->importePendiente - $this->madre->importeTotal) < self::deltaErrorDesaplicar){
			$this->madre->importePendiente = $this->madre->importeTotal;
		}

		if(abs($this->documentoCancelatorio->importePendiente - $this->documentoCancelatorio->importeTotal) < self::deltaErrorDesaplicar){
			$this->documentoCancelatorio->importePendiente = $this->documentoCancelatorio->importeTotal;
		}

		$this->borrar();
	}

	//GETS y SETS
	protected function getDocumentoCancelatorio() {
		if (!isset($this->_documentoCancelatorio)){
			$this->_documentoCancelatorio = Factory::getInstance()->getDocumentoProveedorAplicacionHaber($this->empresa, $this->idDocumentoCancelatorio, $this->cancelTipoDocumento);
		}
		return $this->_documentoCancelatorio;
	}
	protected function setDocumentoCancelatorio($documentoCancelatorio) {
		$this->_documentoCancelatorio = $documentoCancelatorio;
		return $this;
	}
	protected function getMadre() {
		if (!isset($this->_madre)){
			$this->_madre = Factory::getInstance()->getDocumentoProveedorAplicacionDebe($this->empresa, $this->idMadre);
		}
		return $this->_madre;
	}
	protected function setMadre($madre) {
		$this->_madre = $madre;
		return $this;
	}
	protected function getUsuario() {
		if (!isset($this->_usuario)){
			$this->_usuario = Factory::getInstance()->getUsuario($this->idUsuario);
		}
		return $this->_usuario;
	}
	protected function setUsuario($usuario) {
		$this->_usuario = $usuario;
		return $this;
	}
}

?>