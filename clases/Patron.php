<?php

/**
 * @property PatronItem[]		$detalle
 * @property Horma				$horma
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 */

class Patron extends Base {
	const		_primaryKey = '["idArticulo", "idColorPorArticulo", "version"]';

	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$version;
	public		$tipoPatron;
	public		$fecha;
	public		$confirmado;
	public		$versionActual;
	public		$borrador;
	public		$idHorma;
	protected	$_horma;
	public		$disenio;
	public		$borradorViejo;
	protected	$_detalle;
	public		$costo;

	public function esVersionActual() {
		return $this->versionActual == 'S';
	}

	public function addDetalle($item) {
		return $this->_detalle[] = $item;
	}

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
	protected function getDetalle() {
		if (!isset($this->_detalle)){
			$where =  'cod_articulo = ' . Datos::objectToDB($this->articulo->id) . ' AND ';
			$where .=  'cod_color_articulo = ' . Datos::objectToDB($this->colorPorArticulo->id) . ' AND ';
			$where .=  'version = ' . Datos::objectToDB($this->version);
			$orderBy = ' ORDER BY cod_seccion, conjunto';

			$this->_detalle = Factory::getInstance()->getListObject('PatronItem', $where . $orderBy);
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getHorma() {
		if (!isset($this->_horma)){
			$this->_horma = Factory::getInstance()->getHorma($this->idHorma);
		}
		return $this->_horma;
	}
	protected function setHorma($horma) {
		$this->_horma = $horma;
		return $this;
	}
}

?>