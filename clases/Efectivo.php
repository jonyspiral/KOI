<?php

class Efectivo extends Importe {

	public function getTipoImporte(){
		return TiposImporte::efectivo;
	}

	public static function validar($obj) {
		parent::validar($obj);

		if (!isset($obj['importe']) || $obj['importe'] <= 0) {
			throw new FactoryExceptionCustomException('El efectivo no puede tener un importe menor o igual a cero.');
		}

		$returnObj = Factory::getInstance()->getEfectivo();
		$returnObj->importe = Funciones::toFloat($obj['importe']);
		return $returnObj;
	}

	public function simularComoImporte(){
		$importeEfectivo = array();
		if (!empty($this->id)) {
			$importeEfectivo['id'] = $this->id;
		} else {
			$importeEfectivo['importe'] = $this->importe;
		}

		return $importeEfectivo;
	}

	public static function validarExistencia(Caja $caja, $importe, $validarEfectivoParcial) {
		if (($validarEfectivoParcial ? ($caja->importeEfectivo) : $caja->importeEfectivoFinal) + abs($caja->importeDescubierto) < $importe) {
			throw new FactoryExceptionCustomException('La caja no tiene el efectivo suficiente para realizar esta operación');
		}
		return true;
	}

	public function getImputacion() {
		return $this->importePorOperacionItem->importePorOperacion->caja->idImputacion;
	}

	public function getObservacionContabilidad() {
		return '';
	}
}

?>