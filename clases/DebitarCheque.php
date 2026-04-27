<?php

class DebitarCheque extends AcreditarDebitarCheque {
	const		_primaryKey = '["numero", "empresa", "entradaSalida"]';
	protected	$claseCabecera = 'DebitarChequeCabecera';

	public function beforeSave(){
		foreach($this->importesSinValidar[$this->entradaSalida][TiposImporte::cheque] as $cheque) {
			/** @var Cheque $cheque */
			$cheque->concluido = 'S';
			$cheque->fechaCreditoDebito = $this->datosSinValidar['fecha_debito'];
		}

		return true;
	}

	protected function validarNuevo() {
		parent::validarNuevo();

		if(is_null($this->datosSinValidar['fecha_debito']))
			throw new FactoryExceptionCustomException('Debe especificar la fecha de débito.');
	}

	public function getCodigoPermiso() {
		return PermisosUsuarioPorCaja::debitarCheque;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::debitarCheque;
	}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo + ($delete ? -1 : 1) * ($this->esOperacionEntrada() ? 1 : -1) * $importe;
	}

	public function validarCantidadPermitidaEfectivo($cantidad) {
		return $cantidad == 1;
	}

	public function validarCantidadPermitidaCheque($cantidad) {
		return true;
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
}

?>