<?php

/**
 * @property Despacho			$despacho
 * @property Pedido				$pedido
 * @property PedidoItem			$pedidoItem
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property Cliente			$cliente
 * @property Sucursal			$sucursal
 * @property Remito				$remito
 * @property Factura			$factura
 * @property float				$precioFactura
 * @property float				$precioUnitario
 * @property float				$precioUnitarioFinal
 * @property float				$precioUnitarioFacturar
 * @property float				$precioUnitarioFacturarFinal
 * @property int				$cantidadTotal
 * @property float				$importeTotal
 */

class DespachoItem extends Base {
	const		_primaryKey = '["despachoNumero", "numeroDeItem"]';

	public		$despachoNumero;
	protected	$_despacho;
	public		$numeroDeItem;
	public		$empresa;
	public		$anulado;
	public		$pedidoNumero;
	protected	$_pedido;
	public		$pedidoNumeroDeItem;
	protected	$_pedidoItem;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$idCliente;
	protected	$_cliente;
	public		$idSucursal;
	protected	$_sucursal;
	public		$remitoNumero;
	public		$remitoLetra;
	protected	$_remito;
	public		$facturaPuntoDeVenta;
	public		$facturaTipoDocumento;	//Enum TiposDocumento
	public		$facturaNumero;
	public		$facturaLetra;
	protected	$_factura;
	public		$precioAlFacturar;		// S/N
	public		$descuentoPedido;		//Porcentaje
	public		$recargoPedido;			//Porcentaje
	public		$ivaPorcentaje;
	protected	$_precioFactura;		//Es el precio que debe mostrarse en la factura. Va multiplicado por el IVA
	protected	$_precioUnitario;
	protected	$_precioUnitarioFinal;
	protected	$_precioUnitarioFacturar;
	protected	$_precioUnitarioFacturarFinal;
	public		$cantidad;				//Array de 1 a 10
	protected	$_cantidadTotal;
	protected	$_importeTotal;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function __construct() {
		parent::__construct();
	}

	public function actualizarPrecioFacturar() {
		if ($this->_precioUnitarioFacturar != $this->colorPorArticulo->getPrecioSegunCliente($this->cliente)) {
			$this->_precioUnitarioFacturar = $this->colorPorArticulo->getPrecioSegunCliente($this->cliente);
			$this->_precioUnitarioFacturarFinal = $this->calcularPrecioUnitarioFacturarFinal();
			Factory::getInstance()->persistir($this);
		}
	}

	public function getPorcentajeIva() {
		if ($this->empresa != 1)
			return 0;
		return $this->cliente->condicionIva->porcentajes[$this->articulo->rubroIva->columnaIva];
	}

	public function calcularPrecioUnitarioFinal() {
		$importeDescuento = Funciones::toFloat($this->precioUnitario * ($this->descuentoPedido / 100));
		$importeRecargo = Funciones::toFloat($this->precioUnitario * ($this->recargoPedido / 100));
		return Funciones::toFloat($this->precioUnitario - $importeDescuento + $importeRecargo);
	}

	public function calcularPrecioUnitarioFacturarFinal() {
		$importeDescuento = Funciones::toFloat($this->_precioUnitarioFacturar * ($this->descuentoPedido / 100));
		$importeRecargo = Funciones::toFloat($this->_precioUnitarioFacturar * ($this->recargoPedido / 100));
		return Funciones::toFloat($this->_precioUnitarioFacturar - $importeDescuento + $importeRecargo);
	}

	public function remitido() {
		return !is_null($this->remitoNumero);
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
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getCliente($this->idCliente);
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
	protected function getDespacho() {
		if (!isset($this->_despacho)){
			$this->_despacho = Factory::getInstance()->getDespacho($this->despachoNumero);
		}
		return $this->_despacho;
	}
	protected function setDespacho($despacho) {
		$this->_despacho = $despacho;
		return $this;
	}
	protected function getFactura() {
		if (!isset($this->_factura)){
			$this->_factura = Factory::getInstance()->getFactura($this->empresa, $this->facturaPuntoDeVenta, $this->facturaTipoDocumento, $this->facturaNumero, $this->facturaLetra);
		}
		return $this->_factura;
	}
	protected function setFactura($factura) {
		$this->_factura = $factura;
		return $this;
	}
	protected function getImporteTotal() {
		if (!isset($this->_importeTotal)){
			$this->_importeTotal = $this->getCantidadTotal() * $this->getPrecioUnitario();
		}
		return $this->_importeTotal;
	}
	protected function setImporteTotal($importeTotal) {
		$this->_importeTotal = $importeTotal;
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
	protected function getPrecioFactura() {
		if (!isset($this->_precioFactura)){
			if ($this->cliente->condicionIva->tratamiento != 'D')
				$this->_precioFactura = Funciones::toFloat((1 + Funciones::toFloat($this->ivaPorcentaje / 100)) * $this->getPrecioUnitario());
			else
				$this->_precioFactura = $this->getPrecioUnitario();
		}
		return $this->_precioFactura;
	}
	protected function setPrecioFactura($precioFactura) {
		$this->_precioFactura = $precioFactura;
		return $this;
	}
	protected function getPrecioUnitario() {
		if (isset($this->_precioUnitarioFacturar)){
			return $this->_precioUnitarioFacturar;
		}
		return $this->_precioUnitario;
	}
	protected function setPrecioUnitario($precioUnitario) {
		$this->_precioUnitario = $precioUnitario;
		return $this;
	}
	protected function getPrecioUnitarioFinal() {
		if (isset($this->_precioUnitarioFacturarFinal)){
			return $this->_precioUnitarioFacturarFinal;
		}
		return $this->_precioUnitarioFinal;
	}
	protected function setPrecioUnitarioFinal($precioUnitarioFinal) {
		$this->_precioUnitarioFinal = $precioUnitarioFinal;
		return $this;
	}
	protected function getPrecioUnitarioFacturar() {
		return $this->_precioUnitarioFacturar;
	}
	protected function setPrecioUnitarioFacturar($precioUnitarioFacturar) {
		$this->_precioUnitarioFacturar = $precioUnitarioFacturar;
		return $this;
	}
	protected function getPrecioUnitarioFacturarFinal() {
		return $this->_precioUnitarioFacturarFinal;
	}
	protected function setPrecioUnitarioFacturarFinal($precioUnitarioFacturarFinal) {
		$this->_precioUnitarioFacturarFinal = $precioUnitarioFacturarFinal;
		return $this;
	}
	protected function getRemito() {
		if (!isset($this->_remito)){
			$this->_remito = Factory::getInstance()->getRemito($this->empresa, $this->remitoNumero, $this->remitoLetra);
		}
		return $this->_remito;
	}
	protected function setRemito($remito) {
		$this->_remito = $remito;
		return $this;
	}
	protected function getSucursal() {
		if (!isset($this->_sucursal)){
			$this->_sucursal = Factory::getInstance()->getSucursal($this->idCliente, $this->idSucursal);
		}
		return $this->_sucursal;
	}
	protected function setSucursal($sucursal) {
		$this->_sucursal = $sucursal;
		return $this;
	}
}

?>