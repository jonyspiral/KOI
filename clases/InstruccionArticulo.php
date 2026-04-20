<?php

/**
 * @property Articulo			$articulo;
 * @property SeccionProduccion	$seccion;
 */

class InstruccionArticulo extends Base {
	const		_primaryKey = '["cod_articulo","cod_seccion","interna"]';

	public		$idArticulo;
	protected	$_articulo;
	public		$idSeccion;
	protected	$_seccion;
	public		$interna;
	public		$instruccion;
	public		$anulado;
	public		$fechaUltimaMod;

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
	protected function getSeccion() {
		if (!isset($this->_seccion)){
			$this->_seccion = Factory::getInstance()->getSeccionProduccion($this->idSeccion);
		}
		return $this->_seccion;
	}
	protected function setSeccion($seccion) {
		$this->_seccion = $seccion;
		return $this;
	}
}

?>