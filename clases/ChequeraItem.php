<?php

/**
 * @property Chequera	$chequera
 */

class ChequeraItem extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idChequera;
	protected	$_chequera;
	public		$numero;
	public		$utilizado;

	public function utilizar() {
		$this->utilizado = 'S';
		$this->guardar();
	}

	//GETS y SETS
	protected function getChequera() {
		if (!isset($this->_chequera)){
			$this->_chequera = Factory::getInstance()->getChequera($this->idChequera);
		}
		return $this->_chequera;
	}
	protected function setChequera($chequera) {
		$this->_chequera = $chequera;
		return $this;
	}
}
