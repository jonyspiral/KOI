<?php

class DebitarChequeCabecera extends AcreditarDebitarChequeCabecera {
	const		_primaryKey = '["numero", "empresa"]';

	public		$tipo = 'D';

	public function validarNuevo() {
		if(is_null($this->datosSinValidar['fecha'])){
			throw new FactoryExceptionCustomException('Debe completar la fecha de acreditación.');
		}
		$this->fecha = $this->datosSinValidar['fecha'];
		parent::validarNuevo();
	}

	//GETS Y SETS
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->numero)){
			$this->_detalle = Factory::getInstance()->getListObject('DebitarCheque', 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND cod_deposito_cheque = ' . Datos::objectToDB($this->numero));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
}

?>