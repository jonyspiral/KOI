<?php

/**
 * @property array				$detalle
 * @property Horma				$horma
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property Material			$material
 * @property ColorMateriaPrima	$colorMateriaPrima
 * @property SeccionProduccion	$seccion
 * @property Conjunto			$conjunto
 * @property Patron				$patron
 */

class PatronItem extends Base {
	const		_primaryKey = '["idArticulo", "idColorPorArticulo", "version", "numeroDeItem"]';

	protected	$_patron;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$version;
	public		$numeroDeItem;
	public		$idMaterial;
	protected	$_material;
	public		$idColorMateriaPrima;
	protected	$_colorMateriaPrima;
	public		$idSeccion;
	public		$_seccion;
	public		$fechaAlta;
	public		$itemNuevo;
	public		$consumoPar;
	public		$consumoBatch;
	public		$idConjunto;
	protected	$_conjunto;
	public		$varia;
	public		$escalado;
	public		$escalaDesplazada;
	public		$tipoPatron;
	public		$trazable;
	public		$asignadoLote;
	public		$cantEntregada;
	public		$entregado;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
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
	protected function getArticulo() {
		if (!isset($this->_articulo)){
			$this->_articulo = Factory::getInstance()->getArticulo($this->idArticulo);
		}
		return $this->_articulo;
	}
	protected function setArticulo($articulo) {
		$this->_articulo = $articulo;
		return $this;
	}
	protected function getColorPorArticulo() {
		if (!isset($this->_colorPorArticulo)){
			$this->_colorPorArticulo = Factory::getInstance()->getColorPorArticulo($this->idColorPorArticulo);
		}
		return $this->_colorPorArticulo;
	}
	protected function setColorPorArticulo($colorPorArticulo) {
		$this->_colorPorArticulo = $colorPorArticulo;
		return $this;
	}
	protected function setColorMateriaPrima($colorMateriaPrima) {
		$this->_colorMateriaPrima = $colorMateriaPrima;
		return $this;
	}
	protected function getColorMateriaPrima() {
		if (!isset($this->_colorMateriaPrima)){
			$this->_colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($this->idMaterial, $this->idColorMateriaPrima);
		}
		return $this->_colorMateriaPrima;
	}
	protected function setColorMaterialPrima($colorMaterialPrima) {
		$this->_colorMateriaPrima = $colorMaterialPrima;
		return $this;
	}
	protected function getConjunto() {
		if (!isset($this->_conjunto)){
			$this->_conjunto = Factory::getInstance()->getConjunto($this->idConjunto);
		}
		return $this->_conjunto;
	}
	protected function setConjunto($conjunto) {
		$this->_conjunto = $conjunto;
		return $this;
	}
	protected function getPatron() {
		if (!isset($this->_patron)){
			$this->_patron = Factory::getInstance()->getPatron($this->idArticulo, $this->idColorPorArticulo, $this->version);
		}
		return $this->_patron;
	}
	protected function setPatron($patron) {
		$this->_patron = $patron;
		return $this;
	}
	protected function getSeccion() {
		if (!isset($this->_seccion)){
			$this->_seccion = Factory::getInstance()->getSeccionProduccion($this->idSeccion);
		}
		return $this->_seccion;
	}
	protected function setSeccion($seccion) {
		$this->_seccion = $seccion;
		return $this;
	}
}

?>