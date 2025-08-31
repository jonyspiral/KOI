<?php

/**
 * @property Cliente		$cliente
 */

class RetencionSufrida extends Retencion {
	public		$idCliente;
	protected	$_cliente;

	public function getTipoImporte(){
		return TiposImporte::retencionSufrida;
	}

	public function simularComoImporte(){
		$importeRetencion = array(
			'id'				=> $this->id,
			'importe'			=> $this->importe,
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
		$returnObj = Factory::getInstance()->getRetencionSufrida();
		if (isset($obj['cliente']['id']) && $obj['cliente']['id'] != 'null') {
			$returnObj->cliente = Factory::getInstance()->getCliente($obj['cliente']['id']);
		}
		isset($obj['importe']) && $returnObj->importe = Funciones::toFloat($obj['importe']);
		isset($obj['nombre']) && $returnObj->nombre = $obj['nombre'];
		isset($obj['numeroCertificado']) && $returnObj->numeroCertificado = $obj['numeroCertificado'];
		isset($obj['cuit']) && $returnObj->cuit = $obj['cuit'];
		isset($obj['fecha']) && $returnObj->fecha = $obj['fecha'];
		isset($obj['tipoRetencion']['id']) && $returnObj->tipoRetencion = Factory::getInstance()->getTipoRetencion($obj['tipoRetencion']['id']);
		return $returnObj;
	}

	//GETS Y SETS
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getCliente($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
	protected function getNumeroCertificado() {
		return $this->_numeroCertificado;
	}
}

?>