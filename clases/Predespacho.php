<?php

/**
 * @property Pedido				$pedido
 * @property PedidoItem			$pedidoItem
 * @property Cliente			$cliente
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 */

class Predespacho extends Base {
	const		_primaryKey = '["pedidoNumero", "pedidoNumeroDeItem"]';

	public		$empresa;
	public		$pedidoNumero;
	protected	$_pedido;
	public		$pedidoNumeroDeItem;
	protected	$_pedidoItem;
	public		$idCliente;
	protected	$_cliente;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$predespachados;	//Array de 1 a 10
	public		$tickeados;			//Array de 1 a 10
	public		$fechaAlta;
	public		$fechaUltimaMod;

	public function getTotalPredespachados() {
		return Funciones::sumaArray($this->predespachados);
	}

	public function getTotalTickeados() {
		return Funciones::sumaArray($this->tickeados);
	}

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
	protected function getCliente() {
		if (!isset($this->_cliente)){
			//Hago clienteTodos porque sino no funciona en el HtmlAutoSuggestBox
			$this->_cliente = Factory::getInstance()->getClienteTodos($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
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
	protected function getPedido() {
		if (!isset($this->_pedido)){
			$this->_pedido = Factory::getInstance()->getPedido($this->pedidoNumero);
		}
		return $this->_pedido;
	}
	protected function setPedido($pedido) {
		$this->_pedido = $pedido;
		return $this;
	}
	protected function getPedidoItem() {
		if (!isset($this->_pedidoItem)){
			$this->_pedidoItem = Factory::getInstance()->getPedidoItem($this->pedidoNumero, $this->pedidoNumeroDeItem);
		}
		return $this->_pedidoItem;
	}
	protected function setPedidoItem($pedidoItem) {
		$this->_pedidoItem = $pedidoItem;
		return $this;
	}
}

?>