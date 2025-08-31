<?php

/**
 * @property Indicador	$indicador
 */

class IndicadorPorRol extends Rol {
	const		_primaryKey = '["id", "idIndicador"]';

	public		$idIndicador;
	protected	$_indicador;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getIndicador(){
		if (!isset($this->_indicador)){
			$this->_indicador = Factory::getInstance()->getIndicador($this->idIndicador);
		}
		return  $this->_indicador;
	}

	protected function setIndicador($indicador){
		$this->_indicador = $indicador;
		return $this;
	}
}
?>