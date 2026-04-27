<?php

class DepositoBancario extends TransferenciaDoble {
	const		_primaryKey = '["numero", "empresa", "entradaSalida"]';
	protected	$claseCabecera = 'DepositoBancarioCabecera';

	public function beforeSave(){
		foreach($this->importesSinValidar[$this->entradaSalida][TiposImporte::cheque] as $cheque) {
			/** @var Cheque $cheque */
			$cheque->esperandoEnBanco = 'C';
			$cheque->cajaActual = Factory::getInstance()->getCaja($this->datosSinValidar['idCaja_E']);
		}

		return true;
	}

	protected function validarNuevo() {
		parent::validarNuevo();

		if(!$this->datosSinValidar['esVentaDeCheque']){
			foreach($this->importesSinValidar[$this->entradaSalida]['C'] as $cheque){
				/** Cheque $cheque */
				if(Funciones::esFechaMenor(Funciones::hoy(), $cheque->fechaVencimiento))
					throw new FactoryExceptionCustomException('El cheque número ' . $cheque->numero . ' no puede ser depositado ya que no está vencido.');

				if(Funciones::diferenciaFechas($cheque->fechaVencimiento, Funciones::hoy()) >= 31)
					throw new FactoryExceptionCustomException('El cheque número ' . $cheque->numero . ' no puede ser depositado ya que pasaron 30 días desde su fecha de vencimiento.');
			}
		}

		//$this->validarSiHayChequesConcluidos();
	}

	public function getCodigoPermiso() {
		return PermisosUsuarioPorCaja::depositoBancario;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::depositoBancario;
	}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo + ($delete ? -1 : 1) * ($this->esOperacionEntrada() ? 1 : -1) * $importe;
	}

	public function validarCantidadPermitidaEfectivo($cantidad) {
		return false;
	}

	public function validarCantidadPermitidaCheque($cantidad) {
		return $cantidad == 1;
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