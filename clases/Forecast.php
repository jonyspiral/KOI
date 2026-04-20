<?php

/**
 * @property ForecastItem[]		$detalle
 */

class Forecast extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$fecha;
	public		$fechaInicio;
	public		$fechaFin;
	public		$importado;
    public		$observaciones;
    public		$anulado;
    protected	$_detalle;

	public function addDetalle($item) {
		return $this->_detalle[] = $item;
	}

	//GETS y SETS
	protected function getDetalle() {
		if (!isset($this->_detalle)){
			$where =  'IdForecast = ' . Datos::objectToDB($this->id) . ' AND (anulado IS NULL OR anulado = ' . Datos::objectToDB('N') . ')';
			$order = ' ORDER BY cod_articulo, cod_color_articulo';

			$this->_detalle = Factory::getInstance()->getListObject('ForecastItem', $where . $order);
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
}

?>