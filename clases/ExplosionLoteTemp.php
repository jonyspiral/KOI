<?php

/**
 * @property Proveedor				$proveedor
 * @property Material				$material
 * @property ColorMateriaPrima		$colorMateriaPrima
 */

class ExplosionLoteTemp extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$pi;
	public		$rubro;
	public		$idProveedor;
	protected	$_proveedor;
	public		$idMaterial;
	protected	$_material;
	public		$idColor;
	protected	$_colorMateriaPrima;
	public		$consumo;
	public		$pendiente;
	public		$stockUms;
	public		$comprometido;
	public		$precioUnitario;
	public		$preferente;
	public		$stockMinimo;
	public		$factorConversion;
	public		$necesidad;
	public		$cantidadComprar;
	public		$cantidadesComprar;
	public		$importe;
	public		$unidadMedida;
	public		$unidadMedidaCompra;
	public		$stockUmc;
	public		$fechaUltimaMod;
	public		$idUsuarioUltimaMod;

	//GETS y SETS
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
}

?>