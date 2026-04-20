<?php

/**
 * Class CajaPosiblesTransferenciaInterna
 * @property Caja	$cajaEntrada
 * @property Caja	$cajaSalida
 */

class CajaPosiblesTransferenciaInterna extends Base {
	const		_primaryKey = '["idCajaEntrada", "idCajaSalida"]';

	public		$idCajaEntrada;
	protected	$_cajaEntrada;
	public		$idCajaSalida;
	protected	$_cajaSalida;

	//GETS Y SETS
	protected function getCajaEntrada() {
		if (!isset($this->_cajaEntrada)){
			$this->_cajaEntrada = Factory::getInstance()->getCaja($this->idCajaEntrada);
		}
		return $this->_cajaEntrada;
	}
	protected function setCajaEntrada($cajaEntrada) {
		$this->_cajaEntrada = $cajaEntrada;
		return $this;
	}
	protected function getCajaSalida() {
		if (!isset($this->_cajaSalida)){
			$this->_cajaSalida = Factory::getInstance()->getCaja($this->idCajaSalida);
		}
		return $this->_cajaSalida;
	}
	protected function setCajaSalida($cajaSalida) {
		$this->_cajaSalida = $cajaSalida;
		return $this;
	}
}

?>