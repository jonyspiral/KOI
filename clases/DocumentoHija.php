<?php

/**
 * @property DocumentoAplicacionHaber		$documentoCancelatorio
 * @property DocumentoAplicacionDebe		$madre
 * @property Usuario						$usuario
 */

class DocumentoHija extends Base {
	const		_primaryKey = '["id"]';
	const		deltaErrorDesaplicar = 0.03;

	public		$id;						//Es clave_access, el autonumérico
	public		$anulado;
	public		$importe;
	public		$empresa;
	public		$cancelPuntoDeVenta;
	public		$cancelTipoDocumento;		//Enum TiposDocumento
	public		$cancelNumero;
	public		$cancelLetra;
	protected	$_documentoCancelatorio;
	public		$madrePuntoDeVenta;
	public		$madreTipoDocumento;		//Enum TiposDocumento
	public		$madreNumero;
	public		$madreLetra;
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
			$this->_documentoCancelatorio = Factory::getInstance()->getDocumentoAplicacionHaber($this->empresa, $this->cancelPuntoDeVenta, $this->cancelTipoDocumento, $this->cancelNumero, $this->cancelLetra);
		}
		return $this->_documentoCancelatorio;
	}
	protected function setDocumentoCancelatorio($documentoCancelatorio) {
		$this->_documentoCancelatorio = $documentoCancelatorio;
		return $this;
	}
	protected function getMadre() {
		if (!isset($this->_madre)){
			$this->_madre = Factory::getInstance()->getDocumentoAplicacionDebe($this->empresa, $this->madrePuntoDeVenta, $this->madreTipoDocumento, $this->madreNumero, $this->madreLetra);
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