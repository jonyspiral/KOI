<?php

/**
 * @property CobroChequeVentanillaCabecera			$cabecera
 */

class CobroChequeVentanilla extends TransferenciaDoble {
	const		_primaryKey = '["numero", "empresa", "entradaSalida"]';
	protected	$claseCabecera = 'CobroChequeVentanillaCabecera';

	public function beforeSave(){
		foreach($this->importesSinValidar[$this->entradaSalida][TiposImporte::cheque] as $cheque) {
			/** @var Cheque $cheque */
			if($cheque->esPropio()){
				$debitarCheque = Factory::getInstance()->getDebitarCheque();
				$debitarCheque->datosSinValidar = array(
					'observaciones' => 'Débito por cobro de cheque por ventanilla',
					'fecha' => $this->cabecera->fecha,
					'fecha_debito' => $this->cabecera->fecha,
					'usuario' => Usuario::logueado(),
					'idCaja_S' => $cheque->cuentaBancaria->caja->id,
					'idCaja_E' => $cheque->cuentaBancaria->caja->id
				);

				$importesSinValidarEntrada = Factory::getInstance()->getCheque()->simularArrayImportes();
				$importesSinValidarEntrada['C'][] = $cheque->simularComoImporte();

				$efectivo = Factory::getInstance()->getEfectivo();
				$efectivo->importe = $cheque->importe;
				$importesSinValidarSalida = Factory::getInstance()->getEfectivo()->simularArrayImportes();
				$importesSinValidarSalida['E'][] = $efectivo->simularComoImporte();

				$debitarCheque->importesSinValidar['S'] = $importesSinValidarSalida;
				$debitarCheque->importesSinValidar['E'] = $importesSinValidarEntrada;
				$debitarCheque->empresa = $cheque->empresa;
				$debitarCheque->guardar();
			}else {
				$cheque->concluido = 'S';
			}
		}

		return true;
	}

	protected function validarNuevo() {
		parent::validarNuevo();

		foreach($this->importesSinValidar[$this->entradaSalida]['C'] as $cheque){
			/** @var Cheque $cheque */
			if($cheque->diasVencimiento > 0)
				throw new FactoryExceptionCustomException('El cheque Nº ' . $cheque->numero . ' no puede cobrarse por ventanilla por no estar vencido');

			if(abs($cheque->diasVencimiento) > 30)
				throw new FactoryExceptionCustomException('El cheque Nº ' . $cheque->numero . ' no puede cobrarse por ventanilla por estar vencido');

			if($cheque->cruzado())
				throw new FactoryExceptionCustomException('El cheque Nº ' . $cheque->numero . ' no puede cobrarse por ventanilla por estar cruzado');
		}
	}

	public function getCodigoPermiso() {
		return PermisosUsuarioPorCaja::cobroChequesVentanilla;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::cobroChequeVentanilla;
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