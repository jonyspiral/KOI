<?php

class RechazoCheque extends TransferenciaDoble {
	const		_primaryKey = '["numero", "empresa", "entradaSalida"]';
	protected	$claseCabecera = 'RechazoChequeCabecera';

	public function beforeSave(){
		if($this->esOperacionEntrada()){
			foreach($this->importesSinValidar[$this->entradaSalida][TiposImporte::cheque] as $cheque) {
				$cheque->rechazoCheque = Factory::getInstance()->getRechazoChequeCabecera();
				$cheque->rechazoCheque->numero = $this->numero;
				$cheque->rechazoCheque->empresa = $this->empresa;
				$this->transaction()->persistir($cheque);
			}
		}
		return true;
	}

	public function getCodigoPermiso() {
		return PermisosUsuarioPorCaja::rechazoCheque;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::rechazoCheque;
	}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo + ($delete ? -1 : 1) * $importe;
	}

	public function validarCantidadPermitidaEfectivo($cantidad) {
		return false;
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
