<?php

/**
 * @property Almacen	$almacen
 */

class UsuarioPorAlmacen extends Usuario {
	const		_primaryKey = '["id", "idAlmacen"]';

	public		$idAlmacen;
	protected	$_almacen;

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
}

?>