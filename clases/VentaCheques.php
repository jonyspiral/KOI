<?php

class VentaCheques extends TransferenciaDoble {
	const		_primaryKey = '["numero", "empresa", "entradaSalida"]';
	protected	$claseCabecera = 'VentaChequesCabecera';

	public function beforeSave(){
		$proveedor = Factory::getInstance()->getProveedor($this->datosSinValidar['idProveedor']);
		foreach($this->importesSinValidar[$this->entradaSalida][TiposImporte::cheque] as $cheque) {
			/** @var Cheque $cheque */
			!$cheque->esPropio() && $cheque->concluido = 'S';
			$cheque->esPropio() && $cheque->esperandoEnBanco = 'D';
			$cheque->proveedor = $proveedor;
		}

		return true;
	}

	public function beforeCommitSave(){
		foreach($this->importesSinValidar[$this->entradaSalida][TiposImporte::cheque] as $cheque) {
			/** @var Cheque $cheque */
			$cheque->cajaActual = Factory::getInstance()->getCaja($this->datosSinValidar['idCaja_E']);
			$cheque->guardar();
		}

		return true;
	}

	public function getCodigoPermiso() {
		return PermisosUsuarioPorCaja::ventaCheques;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::ventaCheques;
	}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo + ($delete ? -1 : 1) * ($this->esOperacionEntrada() ? 1 : -1) * $importe;
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