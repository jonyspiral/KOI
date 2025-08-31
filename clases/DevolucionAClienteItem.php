<?php

/**
 * @property DevolucionACliente		$devolucionACliente
 * @property Almacen				$almacen
 * @property Articulo				$articulo
 * @property ColorPorArticulo		$colorPorArticulo
 * @property int					$cantidadTotal
 * @property Usuario				$usuario
 */

class DevolucionAClienteItem extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idDevolucionACliente;
	protected	$_devolucionACliente;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$cantidad;				//Array de 1 a 10
	protected	$_cantidadTotal;
	public		$idUsuario;
	protected	$_usuario;
	public		$fechaAlta;

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
	protected function getDevolucionACliente() {
		if (!isset($this->_devolucionACliente)){
			$this->_devolucionACliente = Factory::getInstance()->getDevolucionACliente($this->idDevolucionACliente);
		}
		return $this->_devolucionACliente;
	}
	protected function setDevolucionACliente($devolucionACliente) {
		$this->_devolucionACliente = $devolucionACliente;
		return $this;
	}
}

?>