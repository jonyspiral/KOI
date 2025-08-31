<?php


/**
 * @property Documento			$documento
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property Cliente			$cliente
 * @property float				$precioFactura
 * @property int				$cantidadTotal
 * @property Imputacion			$imputacion
 * @property float				$importeTotal
 */

class DocumentoItem extends Base {
	const		_primaryKey = '["empresa", "puntoDeVenta", "documentoTipoDocumento", "documentoNumero", "documentoLetra", "numeroDeItem"]';

	public		$empresa;
	public		$puntoDeVenta;
	public		$documentoTipoDocumento;	//Enum TiposDocumento
	public		$documentoNumero;
	public		$documentoLetra;
	protected	$_documento;
	public		$anulado;
	public		$numeroDeItem;
	protected	$numeroDeItemFactura;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$idCliente;
	protected	$_cliente;
	public		$descuentoPedido;		//Porcentaje
	public		$recargoPedido;			//Porcentaje
	public		$ivaPorcentaje;
	protected	$_precioFactura;
	public		$precioUnitario;
	public		$precioUnitarioFinal;
	public		$descripcionItem;
	public		$cantidad;					//Array de 1 a 10
	protected	$_cantidadTotal;
	protected	$_importeTotal;
	public		$fechaAlta;
	public		$idImputacion;
	protected	$_imputacion;

	public function getPorcentajeIva() {
		if (!isset($this->empresa)) {
			throw new FactoryExceptionCustomException('Para llamar al método getPorcentajeIva de la clase DocumentoItem primero deberá tener asignado "empresa", "cliente" y "artículo"');
		}
		if ($this->empresa != 1) {
			return 0;
		}
		return $this->cliente->condicionIva->porcentajes[$this->articulo->rubroIva->columnaIva];
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
	protected function getDocumento() {
		if (!isset($this->_documento)){
			$this->_documento = Factory::getInstance()->getDocumento($this->empresa, $this->puntoDeVenta, $this->documentoTipoDocumento, $this->documentoNumero, $this->documentoLetra);
		}
		return $this->_documento;
	}
	protected function setDocumento($documento) {
		$this->_documento = $documento;
		return $this;
	}
	protected function getImporteTotal() {
		if (!isset($this->_importeTotal)){
			$pu = ($this->precioUnitarioFinal ? $this->precioUnitarioFinal : $this->precioUnitario);
			$this->_importeTotal = Funciones::toFloat($this->cantidadTotal * $pu, 2);
		}
		return $this->_importeTotal;
	}
	protected function setImporteTotal($importeTotal) {
		$this->_importeTotal = $importeTotal;
		return $this;
	}
	protected function getImputacion() {
		if (!isset($this->_imputacion)){
			$this->_imputacion = Factory::getInstance()->getImputacion($this->idImputacion);
		}
		return $this->_imputacion;
	}
	protected function setImputacion($imputacion) {
		$this->_imputacion = $imputacion;
		return $this;
	}
	protected function getPrecioFactura() {
		if (!isset($this->_precioFactura)){
			if ($this->documento->cliente->condicionIva->tratamiento != 'D')
				$this->_precioFactura = Funciones::toFloat((1 + Funciones::toFloat($this->ivaPorcentaje / 100)) * $this->precioUnitario);
			else
				$this->_precioFactura = $this->precioUnitario;
		}
		return $this->_precioFactura;
	}
	protected function setPrecioFactura($precioFactura) {
		$this->_precioFactura = $precioFactura;
		return $this;
	}
}

?>