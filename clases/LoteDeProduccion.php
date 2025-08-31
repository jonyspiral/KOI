<?php

/**
 * @property OrdenDeFabricacion[]	$ordenesDeFabricacion
 * @property Forecast				$forecast
 */

class LoteDeProduccion extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	protected   $_ordenesDeFabricacion;
    public		$idForecast;
    protected   $_forecast;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;

	//GETS y SETS
    protected function getForecast() {
        if (!isset($this->_forecast)){
            $this->_forecast = Factory::getInstance()->getForecast($this->idForecast);
        }
        return $this->_forecast;
    }
    protected function setForecast($forecast) {
        $this->_forecast = $forecast;
        return $this;
    }
    protected function getOrdenesDeFabricacion() {
        if (!isset($this->_ordenesDeFabricacion) && $this->id) {
            $where = 'nro_plan = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N') . ' AND cod_articulo IS NOT NULL AND cod_color_articulo IS NOT NULL';
            $this->_ordenesDeFabricacion = Factory::getInstance()->getListObject('OrdenDeFabricacion', $where);
        }
        return $this->_ordenesDeFabricacion;
    }
    protected function setOrdenesDeFabricacion($ordenesDeFabricacion) {
        $this->_ordenesDeFabricacion = $ordenesDeFabricacion;
        return $this;
    }
}

?>