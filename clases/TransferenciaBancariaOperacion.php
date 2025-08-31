<?php

/**
 * @property CuentaBancaria		$cuentaBancaria
 */

class TransferenciaBancariaOperacion extends TransferenciaBase {
	public		$entradaSalida;
	public		$idCuentaBancaria; //Emisor o Receptor
	protected	$_cuentaBancaria;
	public		$fechaTransferencia;
	public		$numeroTransferencia;
	public		$haciaDesde;

	public function getCodigoPermiso() {
		return TiposTransferenciaBase::transferenciaBancariaOperacion;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::transferenciaBancariaOperacion;
	}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo + ($this->entradaSalida == 'E' ? 1 : -1) * ($delete ? -1 : 1) * $importe;
	}

	public function validarCantidadPermitidaEfectivo($cantidad) {
		if ($cantidad != 1) {
			throw new FactoryExceptionCustomException('Sólo se puede ingresar un importe de tipo efectivo');
		}
		return true;
	}

	public function validarCantidadPermitidaCheque($cantidad) {
		return false;
	}

	public function validarCantidadPermitidaTransferenciaBancaria($cantidad) {
		return false;
	}

	public function validarCantidadPermitidaRetencionEfectuada($cantidad) {
		return false;
	}

	public function validarCantidadPermitidaRetencionSufrida($cantidad) {
		return false;
	}

	//GETS y SETS
	protected function getCuentaBancaria() {
		if (!isset($this->_cuentaBancaria)){
			$this->_cuentaBancaria = Factory::getInstance()->getCuentaBancaria($this->idCuentaBancaria);
		}
		return $this->_cuentaBancaria;
	}
	protected function setCuentaBancaria($cuentaBancaria) {
		$this->_cuentaBancaria = $cuentaBancaria;
		return $this;
	}

	protected function eos() {
		return $this->entradaSalida == 'E';
	}

	protected function esOperacionSalida() {
		return $this->entradaSalida == 'S';
	}
}

?>