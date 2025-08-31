<?php

/**
 * @property Pais	$pais
 */

class Provincia extends Base {
	const		_primaryKey = '["idPais", "id"]';

	public		$id;
	public		$idPais;
	protected	$_pais;
	public		$anulado;
	public		$nombre;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getPais() {
		if (!isset($this->_pais)){
			$this->_pais = Factory::getInstance()->getPais($this->idPais);
		}
		return $this->_pais;
	}
	protected function setPais($pais) {
		$this->_pais = $pais;
		return $this;
	}
}

?>