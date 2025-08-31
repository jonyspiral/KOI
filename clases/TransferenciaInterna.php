<?php

/**
 * @property TransferenciaInterna   $contrapartida
 */

class TransferenciaInterna extends TransferenciaDoble {
	const		_primaryKey = '["numero", "empresa", "entradaSalida"]';
	protected	$claseCabecera = 'TransferenciaInternaCabecera';

	protected function validarNuevo() {
		$idCaja_E = $this->datosSinValidar['idCaja_E'];
		$idCaja_S = $this->datosSinValidar['idCaja_S'];
		if (empty($idCaja_E) || empty($idCaja_S)) {
			throw new FactoryExceptionCustomException('Debe completar la caja de salida y la caja de entrada');
		}
		try {
			$uselessvar = Factory::getInstance()->getCajaPosiblesTransferenciaInterna($idCaja_E, $idCaja_S);
		} catch (Exception $ex) {
			throw new FactoryExceptionCustomException('La caja de salida no tiene permiso para hacer transferencias a la caja de entrada');
		}
		parent::validarNuevo();
	}

	public function getCodigoPermiso() {
		return PermisosUsuarioPorCaja::transferenciaInterna;
	}

	public function getTipoTransferenciaBase() {
		TiposTransferenciaBase::transferenciaInterna;
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
		return true;
	}

	public function validarCantidadPermitidaRetencionEfectuada($cantidad) {
		return false;
	}

	public function validarCantidadPermitidaRetencionSufrida($cantidad) {
		return false;
	}

	//GETS y SETS
}

?>