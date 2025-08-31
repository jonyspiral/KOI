<?php

/**
 * @property OrdenDeFabricacion		$ordenDeFabricacion
 * @property Articulo				$articulo
 * @property ColorPorArticulo		$colorPorArticulo
 * @property TareaProduccion		$tareaDeriva
 * @property TareaProduccion		$tareaOriginal
 * @property Operador				$operadorEntregado
 * @property TareaProduccionItem[]	$detalle
 * @property integer            	$cantidadTotal
 */

class TareaProduccion extends Base {
	const		_primaryKey = '["idOrdenDeFabricacion", "numero"]';

	public		$idOrdenDeFabricacion;
	protected	$_ordenDeFabricacion;
	public		$numero;
	public		$idArticulo;			//Lo traigo de la view
	protected	$_articulo;
	public		$idColorPorArticulo;	//Lo traigo de la view
	protected	$_colorPorArticulo;
	public		$situacion;				//"S" o "T" o "I" o "P" (????)
	public		$tipoTarea;
	public		$idTareaDeriva;
	protected	$_tareaDeriva;
	public		$idTareaOriginal;
	protected	$_tareaOriginal;
	public		$pasoDeriva;
	public		$ultimoPasoCumplido;
	public		$cantidadModulos;
	public		$impresa;
    public		$idOperadorEntregado;
    protected	$_operadorEntregado;
    //public		$cantidadTotal;
    protected 	$_cantidadTotal;
    public		$cantidad;				//Array de 1 a 10
	public		$observaciones;
	public		$fechaProgramacion;
	public		$fechaCorte;
	public		$fechaAparado;
	public		$fechaArmado;
	protected	$_detalle;
	public		$anulado;

	//GETS y SETS
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
        $this->_cantidadTotal = Funciones::sumaArray($this->cantidad);
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
	protected function getDetalle() {
		if (!isset($this->_detalle)) {
			$where = 'nro_orden_fabricacion = ' . Datos::objectToDB($this->idOrdenDeFabricacion) . ' AND nro_tarea = ' . Datos::objectToDB($this->numero) . ' ORDER BY nro_paso ASC';
			$this->_detalle = Factory::getInstance()->getListObject('TareaProduccionItem', $where);
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getOperadorEntregado() {
		if (!isset($this->_operadorEntregado)){
			$this->_operadorEntregado = Factory::getInstance()->getOperador($this->idOperadorEntregado);
		}
		return $this->_operadorEntregado;
	}
	protected function setOperadorEntregado($operadorEntregado) {
		$this->_operadorEntregado = $operadorEntregado;
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
	protected function getTareaDeriva() {
		if (!isset($this->_tareaDeriva)){
			$this->_tareaDeriva = Factory::getInstance()->getTareaProduccion($this->idOrdenDeFabricacion, $this->idTareaDeriva);
		}
		return $this->_tareaDeriva;
	}
	protected function setTareaDeriva($tareaDeriva) {
		$this->_tareaDeriva = $tareaDeriva;
		return $this;
	}
	protected function getTareaOriginal() {
		if (!isset($this->_tareaOriginal)){
			$this->_tareaOriginal = Factory::getInstance()->getTareaProduccion($this->idOrdenDeFabricacion, $this->idTareaOriginal);
		}
		return $this->_tareaOriginal;
	}
	protected function setTareaOriginal($tareaOriginal) {
		$this->_tareaOriginal = $tareaOriginal;
		return $this;
	}
}

?>