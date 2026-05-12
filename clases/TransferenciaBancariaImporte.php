<?php

/**
 * @property TransferenciaBancariaOperacion		$transferenciaBancariaOperacion
 * @property string								$entradaSalida
 * @property CuentaBancaria						$cuentaBancaria
 *
 * @property string								$fechaTransferencia
 */

class TransferenciaBancariaImporte extends Importe {
	public		$numeroTransferenciaBancariaOperacion;
	protected	$_transferenciaBancariaOperacion;
	protected	$_entradaSalida;
	protected	$_cuentaBancaria;
	public		$fechaTransferencia; //Sirve para asignarlo después en el guardar. No se persiste en el importe, sino en la operación
	public		$numeroTransferencia;

	public function getTipoImporte(){
		return TiposImporte::transferenciaBancariaImporte;
	}

	public function expand(){
		/** @noinspection PhpUnusedLocalVariableInspection */
		$uselessVar = $this->cuentaBancaria;
	}

	public static function validar($obj) {
		parent::validar($obj);

		if (!isset($obj['importe']) || !isset($obj['cuentaBancaria']['id'])) {
			throw new FactoryExceptionCustomException('No se reconoce el formato de una transferencia bancaria.');
		}

		if (!isset($obj['importe']) || Funciones::toFloat($obj['importe']) <= 0) {
			throw new FactoryExceptionCustomException('Las transferencias bancarias no puede tener un importe menor o igual a cero.');
		}

		$returnObj = Factory::getInstance()->getTransferenciaBancariaImporte();
		$returnObj->importe = Funciones::toFloat($obj['importe']);
		$returnObj->fechaTransferencia = $obj['transferenciaBancariaOperacion']['fechaTransferencia'];
		$returnObj->entradaSalida = $obj['entradaSalida'];
		$returnObj->cuentaBancaria = Factory::getInstance()->getCuentaBancaria($obj['cuentaBancaria']['id']);
		$returnObj->numeroTransferencia = $obj['transferenciaBancariaOperacion']['numeroTransferencia'];
		return $returnObj;
	}

	public static function validarExistencia(&$cajas) {
		foreach ($cajas as $idCaja => &$importe) {
			$caja = Factory::getInstance()->getCaja($idCaja);
			/*$diferencia = abs(($caja->importeEfectivoFinal + abs($caja->importeDescubierto)) - $importe);
			if ($diferencia < 0.01) {
				$importe = $caja->importeEfectivoFinal;
			} else {
				throw new FactoryExceptionCustomException('La caja del banco no tiene el efectivo suficiente para realizar esta operación');
			}*/
			if (($caja->importeEfectivoFinal + abs($caja->importeDescubierto)) < $importe) {
				throw new FactoryExceptionCustomException('La caja del banco no tiene el efectivo suficiente para realizar esta operación');
			}
		}
		return true;
	}

	public function getImputacion() {
		return $this->cuentaBancaria->imputacion->id;
	}

	public function getObservacionContabilidad() {
		return 'Nº de transferencia: ' . $this->transferenciaBancariaOperacion->numeroTransferencia;
	}

	//GETS Y SETS
	protected function getCuentaBancaria() {
		if (!isset($this->_cuentaBancaria)){
			$this->_cuentaBancaria = $this->transferenciaBancariaOperacion->cuentaBancaria;
		}
		return $this->_cuentaBancaria;
	}
	protected function setCuentaBancaria($cuentaBancaria) {
		$this->_cuentaBancaria = $cuentaBancaria;
		return $this;
	}
	protected function getEntradaSalida() {
		if (!isset($this->_entradaSalida)){
			$this->_entradaSalida = $this->transferenciaBancariaOperacion->entradaSalida;
		}
		return $this->_entradaSalida;
	}
	protected function setEntradaSalida($entradaSalida) {
		$this->_entradaSalida = $entradaSalida;
		return $this;
	}
	protected function getTransferenciaBancariaOperacion() {
		if (!isset($this->_transferenciaBancariaOperacion)){
			$this->_transferenciaBancariaOperacion = Factory::getInstance()->getTransferenciaBancariaOperacion($this->numeroTransferenciaBancariaOperacion);
		}
		return $this->_transferenciaBancariaOperacion;
	}
	protected function setTransferenciaBancariaOperacion($transferenciaBancariaOperacion) {
		$this->_transferenciaBancariaOperacion = $transferenciaBancariaOperacion;
		return $this;
	}
}
