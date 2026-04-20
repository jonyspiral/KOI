<?php

/**
 * @property OrdenDeFabricacion	$ordenDeFabricacion
 * @property SeccionProduccion	$seccionProduccion
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property Operador			$operador
 */

class TareaProduccionItem extends Base {
	const		_primaryKey = '["idOrdenDeFabricacion", "numero", "idSeccionProduccion"]';

	public		$idOrdenDeFabricacion;
	protected	$_ordenDeFabricacion;
	public		$numeroTarea;
	public		$idSeccionProduccion;
	protected	$_seccionProduccion;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;			//Lo traigo de la view
	protected	$_articulo;
	public		$idColorPorArticulo;	//Lo traigo de la view
	protected	$_colorPorArticulo;
	public		$idUnidadProduccion;
	protected	$_unidadProduccion;
	public		$ejecucion;				//"S" o "T" o "I"
	public		$numeroPaso;
	public		$subPaso;
	public		$cantidadEntrada;
	public		$cantidadSalida;
	public		$fechaEntradaProgramada;
	public		$fechaEntradaReal;
	public		$horaEntradaReal;
	public		$fechaSalidaReal;
	public		$horaSalidaReal;
	public		$idOperador;
	protected	$_operador;
	public		$duracionPaso;
	public		$cumplidoPaso;
	public		$cantidad;				//Array de 1 a 10
	public		$pendiente;				//Array de 1 a 10
	public		$pendienteTotal;
	public		$entradaConfirmada;
	public		$rendido;
	public		$valorAplicable;
	public		$liquidado;
	public		$liquidacionNumero;
	public		$liquidacionFecha;

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
	protected function getOperador() {
		if (!isset($this->_operador)){
			$this->_operador = Factory::getInstance()->getOperador($this->idOperador);
		}
		return $this->_operador;
	}
	protected function setOperador($operador) {
		$this->_operador = $operador;
		return $this;
	}
	protected function getOrdenDeFabricacion() {
		if (!isset($this->_ordenDeFabricacion)){
			$this->_ordenDeFabricacion = Factory::getInstance()->getOrdenDeFabricacion($this->idOrdenDeFabricacion);
		}
		return $this->_ordenDeFabricacion;
	}
	protected function setOrdenDeFabricacion($ordenDeFabricacion) {
		$this->_ordenDeFabricacion = $ordenDeFabricacion;
		return $this;
	}
	protected function getSeccionProduccion() {
		if (!isset($this->_seccionProduccion)){
			$this->_seccionProduccion = Factory::getInstance()->getSeccionProduccion($this->idSeccionProduccion);
		}
		return $this->_seccionProduccion;
	}
	protected function setSeccionProduccion($seccionProduccion) {
		$this->_seccionProduccion = $seccionProduccion;
		return $this;
	}
}

?>