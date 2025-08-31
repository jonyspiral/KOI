<?php

/**
 * @property array	$clientes
 */

class Vendedor extends Operador {
	protected	$_clientes;

	public function __construct() {
		parent::__construct();
		$this->tipo = TiposOperador::vendedor;
	}

	//GETS y SETS
	protected function getClientes() {
		if (!isset($this->_clientes) && isset($this->id)){
			$this->_clientes = Factory::getInstance()->getListObject('Cliente', 'cod_vendedor = ' . Datos::objectToDB($this->id));
		}
		return $this->_clientes;
	}
	protected function setClientes($clientes) {
		$this->_clientes = $clientes;
		return $this;
	}
}

?>