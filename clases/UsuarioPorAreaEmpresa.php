<?php

/**
 * @property AreaEmpresa	$areaEmpresa
 */

class UsuarioPorAreaEmpresa extends Usuario {
	const		_primaryKey = '["id", "idAreaEmpresa"]';

	public		$idAreaEmpresa;
	protected	$_areaEmpresa;

	//GETS y SETS
	protected function getAreaEmpresa() {
		if (!isset($this->_areaEmpresa)){
			$this->_areaEmpresa = Factory::getInstance()->getAreaEmpresa($this->idAreaEmpresa);
		}
		return $this->_areaEmpresa;
	}
	protected function setAreaEmpresa($areaEmpresa) {
		$this->_areaEmpresa = $areaEmpresa;
		return $this;
	}
}

?>