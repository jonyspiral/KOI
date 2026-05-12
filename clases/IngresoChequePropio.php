<?php

class IngresoChequePropio extends TransferenciaBase {
	protected	$_entradaSalida = 'E';

	/*public function beforeCommit(){
		foreach($this->importesSinValidar['C'] as $importe){
			self::$transaction->persistir(Factory::getInstance()->getChequera($importe->banco->idBanco, $importe->idSucursalBanco, $importe->numero));
		}
		return true;
	}*/

	protected function validarNuevo() {
		parent::validarNuevo();
		$this->validarSiHayChequesConcluidos();
	}

	protected function borrarCheque(Cheque $cheque){
		if(!$cheque->esperandoEnBanco && !$cheque->concluido() && $this->importePorOperacion->caja->id == $cheque->cajaActual->id){
			Factory::getInstance()->marcarParaBorrar($cheque);
			$this->transaction()->persistir($cheque);
		}else{
			throw new FactoryExceptionCustomException('El recibo no puede borrarse ya que el cheque Nº ' . $cheque->numero . ' ya fue utilizado en alguna operación');
		}
	}

	//GETS y SETS
	public function getCodigoPermiso() {
		return PermisosUsuarioPorCaja::ingresoChequePropio;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::ingresoChequePropio;
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

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo + ($delete ? -1 : 1) * $importe;
	}
}

?>