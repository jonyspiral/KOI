<?php

/**
 * @property Garantia			$garantia
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property int				$cantidadTotal
 */

class GarantiaItem extends Base implements OperacionStock {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idGarantia;
	protected	$_garantia;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$importeNcr;
	public		$cantidad;			//Array de 1 a 10
	protected	$_cantidadTotal;

	/************************************** STOCK **************************************/

	public function stock() {
		return Stock::registrarOperacion($this);
	}

	public function stockTipoMovimiento() {
		return ($this->modo == Modos::delete) ? TiposMovimientoStock::negativo :  TiposMovimientoStock::positivo;
	}

	public function stockTipoOperacion() {
		return TiposOperacionStock::garantia;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'Garantía Nº ' . $this->idGarantia;
	}

	public function stockDetalle() {
		return array(
			$this->almacen->id => array(
				$this->articulo->id => array(
					$this->colorPorArticulo->id => $this->cantidad
				)
			)
		);
	}

	/************************************** ***** **************************************/

	//GETS y SETS
	protected function getAlmacen() {
		if (!isset($this->_almacen)){
			$this->_almacen = Factory::getInstance()->getAlmacen($this->idAlmacen);
		}
		return $this->_almacen;
	}
	protected function setAlmacen($almacen) {
		$this->_almacen = $almacen;
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
	protected function getCantidadTotal() {
		if (!isset($this->_cantidadTotal)){
			$this->_cantidadTotal = 0;
			for ($i = 1; $i <= 10; $i++)
				$this->_cantidadTotal += $this->cantidad[$i];
		}
		return $this->_cantidadTotal;
	}
	protected function setCantidadTotal($cantidadTotal) {
		$this->_cantidadTotal = $cantidadTotal;
		return $this;
	}
	protected function getColorPorArticulo() {
		if (!isset($this->_colorPorArticulo)){
			$this->_colorPorArticulo = Factory::getInstance()->getColorPorArticulo($this->idArticulo, $this->idColorPorArticulo);
		}
		return $this->_colorPorArticulo;
	}
	protected function setColorPorArticulo($colorPorArticulo) {
		$this->_colorPorArticulo = $colorPorArticulo;
		return $this;
	}
	protected function getGarantia() {
		if (!isset($this->_garantia)){
			$this->_garantia = Factory::getInstance()->getGarantia($this->idGarantia);
		}
		return $this->_garantia;
	}
	protected function setGarantia($garantia) {
		$this->_garantia = $garantia;
		return $this;
	}
}

?>