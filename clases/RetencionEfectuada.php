<?php

/**
 * @property Proveedor		$proveedor
 */

class RetencionEfectuada extends Retencion {
	const		ID = 28;

	public		$idProveedor;
	protected	$_proveedor;
	public		$importeNeto;

	public function getTipoImporte(){
		return TiposImporte::retencionEfectuada;
	}

	public function simularComoImporte(){
		$importeRetencion = array(
			'id'				=> $this->id,
			'importe'			=> $this->importe,
			'importeNeto'		=> $this->importeNeto,
			'nombre'			=> $this->nombre,
			'numeroCertificado'	=> $this->numeroCertificado,
			'cuit'				=> $this->cuit,
			'fecha'				=> $this->fecha,
			'tipoRetencion'		=> array(
				'id'		=> $this->tipoRetencion->id
			)
		);
		return $importeRetencion;
	}

	public static function validar($obj) {
		parent::validar($obj);
		$returnObj = Factory::getInstance()->getRetencionEfectuada();
		if (!isset($obj['proveedor']['id']) && $obj['proveedor']['id'] != 'null') {
			throw new FactoryExceptionCustomException('No se reconoce el formato de una retención. Debe seleccionar proveedor');
		} else {
			$returnObj->proveedor = Factory::getInstance()->getProveedor($obj['proveedor']['id']);
		}
		isset($obj['importe']) && $returnObj->importe = Funciones::toFloat($obj['importe']);
		isset($obj['importeNeto']) && $returnObj->importeNeto = Funciones::toFloat($obj['importeNeto']);
		isset($obj['nombre']) && $returnObj->nombre = $obj['nombre'];
		isset($obj['numeroCertificado']) && $returnObj->cuit = $obj['numeroCertificado'];
		isset($obj['cuit']) && $returnObj->cuit = $obj['cuit'];
		isset($obj['fecha']) && $returnObj->fecha = $obj['fecha'];
		isset($obj['tipoRetencion']['id']) && $returnObj->tipoRetencion = Factory::getInstance()->getTipoRetencion($obj['tipoRetencion']['id']);
		return $returnObj;
	}

	//GETS Y SETS
	protected function getNumeroCertificado() {
		if (!isset($this->_numeroCertificado)){
			$this->_numeroCertificado = $this->id;
		}
		return $this->_numeroCertificado;
	}
	protected function getProveedor() {
		if (!isset($this->_proveedor)){
			$this->_proveedor = Factory::getInstance()->getProveedor($this->idProveedor);
		}
		return $this->_proveedor;
	}
	protected function setProveedor($proveedor) {
		$this->_proveedor = $proveedor;
		return $this;
	}
}

?>