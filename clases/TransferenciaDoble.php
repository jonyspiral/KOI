<?php

/**
 * @property TransferenciaDobleCabecera		$cabecera
 * @property TransferenciaInterna   		$contrapartida
 */

abstract class TransferenciaDoble extends TransferenciaBase {
	const		_primaryKey = '["numero", "empresa", "entradaSalida"]';
	protected	$claseCabecera = 'TransferenciaDobleCabecera';
	protected	$_contrapartida;
	protected	$_cabecera;

	public function getTextoDe($conTipo = false) {
		return $this->entradaSalida == 'S' ? $this->getTextoCaja($conTipo) : $this->contrapartida->getTextoCaja($conTipo);
	}

	public function getTextoPara($conTipo = false) {
		return $this->entradaSalida == 'S' ? $this->contrapartida->getTextoCaja($conTipo) : $this->getTextoCaja($conTipo);
	}

	public function guardar() {
		$this->mutex();
		try {
			$this->transaction();
			if ($this->modo == Modos::insert) {
				//Creo la cabecera y la lleno
				/** @var $cabecera TransferenciaDobleCabecera */
				$method = 'get' . $this->claseCabecera;
				$cabecera = Factory::getInstance()->$method();
				$cabecera->datosSinValidar = $this->datosSinValidar;
				$cabecera->validarNuevo();
				$cabecera->empresa = $this->empresa;
				$cabecera->importeTotal = $this->importeTotal;
				$cabecera->beforeSave();
				($cabecera->modo == Modos::update) && $cabecera->beforeUpdate();
				($cabecera->modo == Modos::insert) && $cabecera->beforeInsert();
				$cabecera->guardarNuevo();

				//Lleno el de salida
				$salida = clone $this;
				$salida->entradaSalida = 'S';
				$salida->numero = $cabecera->numero;
				$salida->validarNuevo();
				$salida->beforeSave();
				($salida->modo == Modos::update) && $salida->beforeUpdate();
				($salida->modo == Modos::insert) && $salida->beforeInsert();
				$salida->guardarNuevo();

				//Lleno el de entrada
				$entrada = clone $this;
				$entrada->entradaSalida = 'E';
				$entrada->numero = $cabecera->numero;
				$entrada->validarNuevo();
				$entrada->beforeSave();
				($entrada->modo == Modos::update) && $entrada->beforeUpdate();
				($entrada->modo == Modos::insert) && $entrada->beforeInsert();
				$entrada->guardarNuevo();

				$salida->beforeCommitSave();
				$entrada->beforeCommitSave();
				$cabecera->beforeCommit();
				$this->transaction(true);
			} elseif ($this->modo == Modos::update) {

			} else {
				throw new FactoryExceptionCustomException('No se puede guardar un objeto que no esté en modo insert');
			}
			$this->mutex(true);
		} catch (Exception $ex) {
			$this->mutex(true);
			throw $ex;
		}

		return $this;
	}

	public function borrar() {
		$this->validarBorrar();
		$this->mutex();
		try {
			$this->transaction();
			$this->prepararImportesAlternados();

			$salida = clone $this;
			//Lleno el de salida
			//$this->validarBorrar(); (?) podría estar y llamar al validateNuevo si es que no queremos que haga algo distinto
			$salida->validarNuevo();
			if($salida->beforeSave()){
				$salida->guardarNuevo();
			}

			$entrada = clone $this;
			//Lleno el de entrada
			//$this->validarBorrar(); (?) podría estar y llamar al validateNuevo si es que no queremos que haga algo distinto
			$entrada->validarNuevo();
			if($entrada->beforeSave()){
				$entrada->guardarNuevo();
			}

			$salida->beforeCommitSave();
			$entrada->beforeCommitSave();
			$this->transaction(true);

			$this->mutex(true);
		} catch (Exception $ex) {
			$this->mutex(true);
			throw $ex;
		}

		return $this;
	}

	public function beforeCommitSave(){
		return true;
	}

	public function beforeSave(){
		return true;
	}

	protected function getIds($obj) {
		$clase = Funciones::getType($obj);
		if(($obj == $this) && ($obj->entradaSalida == 'E')){
			return self::$ids[$clase];
		}
		return parent::getIds($obj);
	}

	//GETS y SETS
	protected function getCabecera() {
		if (!isset($this->_cabecera)) {
			$method = 'get' . $this->claseCabecera;
			$this->_cabecera = Factory::getInstance()->$method($this->numero, $this->empresa);
		}
		return $this->_cabecera;
	}
	protected function setCabecera($cabecera) {
		$this->_cabecera = $cabecera;
		return $this;
	}
	protected function getContrapartida() {
		if (!isset($this->_contrapartida)) {
			$method = 'get' . get_class($this);
			$this->_contrapartida = Factory::getInstance()->$method($this->numero, $this->empresa, $this->entradaSalida == 'E' ? 'S' : 'E');
		}
		return $this->_contrapartida;
	}
	protected function setContrapartida($contrapartida) {
		$this->_contrapartida = $contrapartida;
		return $this;
	}
}

?>