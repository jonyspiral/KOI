<?php
/**
 * @property Material				$material
 * @property ColorMateriaPrima		$colorMateriaPrima
 * @property Proveedor				$proveedor
 * @property String					$codigoPropio
 */

class ProveedorMateriaPrima extends Base {
	const		_primaryKey = '["idProveedor",idMaterial","idColor"]';

	public		$idProveedor;
	protected	$_proveedor;
	public		$idMaterial;
	protected	$_material;
	public		$idColor;
	public		$preferente;
	public		$precioCompra;
	public		$fecha;
	public		$codigoInterno;
	protected	$_codigoPropio;
	protected	$_colorMateriaPrima;
	public		$anulado;
	public		$fechaBaja;

	public function esPreferente(){
		return $this->preferente == 'S';
	}

	//GETS y SETS
	protected function getCodigoPropio() {
		if (!isset($this->_codigoPropio)){
			$this->_codigoPropio = '[' . $this->material->id . '] ' . $this->material->nombre . ' - Color: ' . Factory::getInstance()->getColorMateriaPrima($this->idMaterial, $this->idColor)->nombreColor;
		}
		return $this->_codigoPropio;
	}
	protected function getMaterial() {
		if (!isset($this->_material)){
			$this->_material = Factory::getInstance()->getMaterial($this->idMaterial);
		}
		return $this->_material;
	}
	protected function setMaterial($material) {
		$this->_material = $material;
		return $this;
	}
	protected function getColorMateriaPrima() {
		if (!isset($this->_colorMateriaPrima)){
			$this->_colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($this->idMaterial, $this->idColor);
		}
		return $this->_colorMateriaPrima;
	}
	protected function setColorMateriaPrima($colorMateriaPrima) {
		$this->_colorMateriaPrima = $colorMateriaPrima;
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