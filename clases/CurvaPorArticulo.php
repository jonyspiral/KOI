<?php

/**
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property Curva				$curva
 */


class CurvaPorArticulo extends Base {
	const		_primaryKey = '["idArticulo", "idColorPorArticulo", "idCurva"]';

	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$idCurva;
	protected	$_curva;

	public function __construct() {
		parent::__construct();
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
	protected function getCurva() {
		if (!isset($this->_curva)){
			$this->_curva = Factory::getInstance()->getCurva($this->idCurva);
		}
		return $this->_curva;
	}
	protected function setCurva($curva) {
		$this->_curva = $curva;
		return $this;
	}
}

?>