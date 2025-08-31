<?php

/**
 * @property array				$hijas
 * @property Proveedor			$proveedor
 * @property DocumentoProveedor	$documento
 */

class DocumentoProveedorAplicacion extends Base {
	public		$id;
	public		$empresa;
	public		$puntoDeVenta;
	public		$tipoDocumento;
	public		$nroDocumento;
	public		$letra;
	public		$idProveedor;
	protected	$_proveedor;
	public		$fecha;
	public		$importeTotal;
	public		$importePendiente;
	protected	$_hijas;

	protected	$_documento;

	public function anulado() {
		return $this->documento->anulado();
	}

	public function esHaber() {
		return in_array($this->tipoDocumento, array('NCR', 'OP', 'REN'));
	}

	//GETS y SETS
	protected function getDocumento() {
		if (!isset($this->_documento)){
			switch ($this->tipoDocumento) {
				case 'FAC':
					$this->_documento = Factory::getInstance()->getFacturaProveedor($this->id);
					break;
				case 'NCR':
					$this->_documento = Factory::getInstance()->getNotaDeCreditoProveedor($this->id);
					break;
				case 'NDB':
					$this->_documento = Factory::getInstance()->getNotaDeDebitoProveedor($this->id);
					break;
				case 'OP':
					$this->_documento = Factory::getInstance()->getOrdenDePago($this->id, $this->empresa);
					break;
				case 'REN':
					$this->_documento = Factory::getInstance()->getRendicionGastos($this->id, $this->empresa);
					break;
			}
		}
		return $this->_documento;
	}
	protected function setDocumento($documento) {
		$this->_documento = $documento;
		return $this;
	}
	protected function getHijas() {
		if (!isset($this->_hijas)){
			$where = 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND ';
			$where .= 'cod_madre = ' . Datos::objectToDB($this->id);
			$this->_hijas = Factory::getInstance()->getListObject('DocumentoProveedorHija', $where);
		}
		return $this->_hijas;
	}
	protected function setHijas($hijas) {
		$this->_hijas = $hijas;
		return $this;
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