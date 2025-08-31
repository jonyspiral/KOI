<?php

/**
 * @property PresupuestoItem		$presupuestoItem
 * @property OrdenDeCompraItem		$ordenDeCompraItem
 */

class PresupuestoOrdenCompra extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idPresupuesto;
	public		$numeroDeItemPresupuesto;
	protected	$_presupuestoItem;
	public		$idOrdenDeCompra;
	public		$numeroDeItemOrdenDeCompra;
	protected	$_ordenDeCompraItem;

	//GETS y SETS
	protected function getOrdenDeCompraItem() {
		if (!isset($this->_ordenDeCompraItem)){
			$this->_ordenDeCompraItem = Factory::getInstance()->getOrdenDeCompraItem($this->idOrdenDeCompra, $this->numeroDeItemOrdenDeCompra);
		}
		return $this->_ordenDeCompraItem;
	}
	protected function setOrdenDeCompraItem($ordenDeCompra) {
		$this->_ordenDeCompraItem = $ordenDeCompra;
		return $this;
	}

	protected function getPresupuestoItem() {
		if (!isset($this->_presupuestoItem)){
			$this->_presupuestoItem = Factory::getInstance()->getPresupuestoItem($this->idPresupuesto, $this->numeroDeItemPresupuesto);
		}
		return $this->_presupuestoItem;
	}
	protected function setPresupuestoItem($presupuestoItem) {
		$this->_presupuestoItem = $presupuestoItem;
		return $this;
	}
}

?>