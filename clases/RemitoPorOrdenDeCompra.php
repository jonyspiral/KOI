<?php

/**
 * @property RemitoProveedor		$remitoProveedor
 * @property RemitoProveedorItem	$remitoProveedorItem
 * @property OrdenDeCompra			$ordenDeCompra
 * @property OrdenDeCompraItem		$ordenDeCompraItem
 * @property Int[]					$cantidadesOc
 * @property Int[]					$cantidades
 */

class RemitoPorOrdenDeCompra extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idRemitoProveedor;
	protected	$_remitoProveedor;
	public		$numeroDeItemRemitoProveedor;
	protected	$_remitoProveedorItem;
	public		$idOrdenDeCompra;
	protected	$_ordenDeCompra;
	protected	$_ordenDeCompraItem;
	public		$numeroDeItemOrdenDeCompra;
	public		$cantidadOc;
	public		$cantidadesOc;
	public		$cantidad;
	public		$cantidades;
	public		$cantidadPendiente;
	public		$cantidadesPendientes;

	public function __construct() {
		$this->cantidades = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		$this->cantidadesOc = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		$this->cantidadesPendientes = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		parent::__construct();
	}

	public function aplicado(){
		return $this->cantidadPendiente <= 0;
	}

	//GETS y SETS
	protected function getOrdenDeCompra(){
		if (!isset($this->ordenDeCompra)){
			$this->ordenDeCompra = Factory::getInstance()->getOrdenDeCompra($this->idOrdenDeCompra);
		}
		return  $this->ordenDeCompra;
	}

	protected function setOrdenDeCompra($ordenDeCompra){
		$this->ordenDeCompra = $ordenDeCompra;
		return $this;
	}

	protected function getOrdenDeCompraItem(){
		if (!isset($this->_ordenDeCompraItem)){
			$this->_ordenDeCompraItem = Factory::getInstance()->getOrdenDeCompraItem($this->idOrdenDeCompra, $this->numeroDeItemOrdenDeCompra);
		}
		return  $this->_ordenDeCompraItem;
	}

	protected function getRemitoProveedor(){
		if (!isset($this->_remitoProveedor)){
			$this->_remitoProveedor = Factory::getInstance()->getRemitoProveedor($this->idRemitoProveedor);
		}
		return  $this->_remitoProveedor;
	}

	protected function setRemitoProveedor($remitoProveedor){
		$this->_remitoProveedor = $remitoProveedor;
		return $this;
	}

	protected function getRemitoProveedorItem(){
		if (!isset($this->_remitoProveedorItem)){
			$this->_remitoProveedorItem = Factory::getInstance()->getRemitoProveedorItem($this->idRemitoProveedor, $this->numeroDeItemRemitoProveedor);
		}
		return  $this->_remitoProveedorItem;
	}
}
?>