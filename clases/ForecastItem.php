<?php

/**
 * @property Forecast			$forecast
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property ColorMateriaPrima	$colorMateriaPrima
 * @property Patron				$patron
 */

class ForecastItem extends Base {
	const		_primaryKey = '["id"]';

    public		$id;
    public		$idForecast;
    protected	$_forecast;
    public		$idArticulo;
    protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
    public		$version;
    protected	$_patron;
    public		$cantidadTotal;
    public		$cantidad;			//Array de 1 a 10

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
	protected function getPatron() {
		if (!isset($this->_patron)){
			$this->_patron = Factory::getInstance()->getPatron($this->idArticulo, $this->idColorPorArticulo, $this->version);
		}
		return $this->_patron;
	}
	protected function setPatron($patron) {
		$this->_patron = $patron;
		return $this;
	}
}

?>