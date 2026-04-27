<?php

/**
 * @property Pedido				$pedido
 * @property ClienteTodos		$cliente
 * @property Vendedor			$vendedor
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property Predespacho		$predespacho
 */

class PedidoItem extends Base {
	const		_primaryKey = '["numero", "numeroDeItem"]';

	public		$empresa;
	public		$numero;
	protected	$_pedido;
	public		$numeroDeItem;
	public		$anulado;
	public		$idCliente;
	protected	$_cliente;
	public		$idVendedor;
	protected	$_vendedor;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$precioUnitario;
	public		$cantidad;				//Array de 1 a 10
	public		$pendiente;				//Array de 1 a 10
	public		$predespachados;		//Array de 1 a 10
	public		$tickeados;				//Array de 1 a 10
	protected	$_predespacho;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function getTotalCantidad() {
		return Funciones::sumaArray($this->cantidad);
	}

	public function getTotalPendiente() {
		return Funciones::sumaArray($this->pendiente);
	}

	public function getTotalPredespachados() {
		return Funciones::sumaArray($this->predespachados);
	}

	public function getTotalTickeados() {
		return Funciones::sumaArray($this->tickeados);
	}

	public function getTotalAnulados() {
		return $this->anulado == 'S' ? $this->getTotalPendiente() + $this->getTotalPredespachados() + $this->getTotalTickeados() : 0;
	}

	public function getImportePendiente() {
		return Funciones::toFloat(Funciones::sumaArray($this->pendiente) * $this->precioUnitario);
	}
	
	public function getImporteTickeado() {
		return Funciones::toFloat(Funciones::sumaArray($this->tickeados) * $this->precioUnitario);
	}
	
	public function getImportePredespachado() {
		return Funciones::toFloat(Funciones::sumaArray($this->predespachados) * $this->precioUnitario);
	}

	public function getImporteTotal() {
		return Funciones::toFloat(Funciones::sumaArray($this->cantidad) * $this->precioUnitario);
	}

	public function getIdCombinado($separador = '_', $conAlmacen = true) {
		return ($conAlmacen ? $this->idAlmacen . $separador : '') . $this->idArticulo . $separador . $this->idColorPorArticulo;
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
			$this->_pedido = Factory::getInstance()->getPedido($this->numero);
		}
		return $this->_pedido;
	}
	protected function setPedido($pedido) {
		$this->_pedido = $pedido;
		return $this;
	}
	protected function getPredespacho() {
		if (!isset($this->_predespacho)) {
			$this->_predespacho = Factory::getInstance()->getPredespacho($this->numero, $this->numeroDeItem);
		}
		return $this->_predespacho;
	}
	protected function getVendedor() {
		if (!isset($this->_vendedor)){
			$this->_vendedor = Factory::getInstance()->getVendedor($this->idVendedor);
		}
		return $this->_vendedor;
	}
	protected function setVendedor($vendedor) {
		$this->_vendedor = $vendedor;
		return $this;
	}
}

?>