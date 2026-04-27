<?php

/**
 * @property Articulo			$articulo
 */


class CurvaProduccionPorArticulo extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$tipoDeCurva;	//"C"omercial o "P"roducción ("P" no las voy a usar)
	public		$idArticulo;
	protected	$_articulo;
    public		$orden;
    public		$nombre;
    public		$activo;
    public		$cantidadTotal;
    public		$cantidad;		//Array de 1 a 10

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
}

?>