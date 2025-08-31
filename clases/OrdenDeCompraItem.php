<?php

/**
 * @property OrdenDecompra			$ordenDeCompra
 * @property Material				$material
 * @property ColorMateriaPrima		$colorMateriaPrima
 * @property Array					$cantidades;
 * @property Array					$precios;
 * @property Array					$cantidadesPendientes;
 * @property Impuesto				$impuesto;
 * @property Float					$importePendiente;
 */

class OrdenDeCompraItem extends Base {
	const		_primaryKey = '["idOrdenDeCompra","nroItem"]';

	public		$idOrdenDeCompra;
	protected	$_ordenDeCompra;
	public		$numeroDeItem;
	public		$idMaterial;
	protected	$_material;
	public		$idColorMaterial;
	protected	$_colorMateriaPrima;
	public		$fechaEntrega;
	public		$precioUnitario;
	public		$cantidad;
	public		$cantidadPendiente;
	public		$importe;
	protected	$_importePendiente;
	public		$cantidades;
	public		$precios;
	public		$cantidadesPendientes;
	public		$letraFactura;
	public		$sucursalFactura;
	public		$loteDeCompra;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	public		$idImpuesto;
	protected	$_impuesto;
	public		$importeImpuesto;

	public function __construct(){
		$this->cantidades = array();
		$this->precios = array();
		$this->cantidadesPendientes = array();
		parent::__construct();
	}

	//GETS y SETS
	protected function getColorMateriaPrima() {
		if (!isset($this->_colorMateriaPrima)){
			$this->_colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($this->material->id, $this->idColorMaterial);
		}
		return $this->_colorMateriaPrima;
	}
	protected function setColorMateriaPrima($colorMateriaPrima) {
		$this->_colorMateriaPrima = $colorMateriaPrima;
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
	protected function getImportePendiente() {
		if (!isset($this->_importePendiente)){
			if ($this->material->usaRango()) {
				$importeTotal = 0;
				for($i = 0; $i < 16; $i++){
					$importeTotal += $this->cantidadesPendientes[$i] * $this->precios[$i];
				}
				$this->_importePendiente = $importeTotal;
			} else {
				$this->_importePendiente = $this->cantidadPendiente * $this->precioUnitario;
			}
		}
		return $this->_importePendiente;
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
	protected function getOrdenDeCompra() {
		if (!isset($this->_ordenDeCompra)){
			$this->_ordenDeCompra = Factory::getInstance()->getOrdenDeCompra($this->idOrdenDeCompra);
		}
		return $this->_ordenDeCompra;
	}
	protected function setOrdenDeCompra($ordenDeCompra) {
		$this->_ordenDeCompra = $ordenDeCompra;
		return $this;
	}
}

?>