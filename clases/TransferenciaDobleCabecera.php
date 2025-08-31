<?php

abstract class TransferenciaDobleCabecera extends TransferenciaBase {
	protected	$_detalle;

	public function validarNuevo() {
		//Lleno los datos que tienen en común todas las TransferenciaBase
		$this->empresa = $this->datosSinValidar['empresa'];
		$this->usuario = $this->datosSinValidar['usuario'];
		$this->observaciones = $this->datosSinValidar['observaciones'];
	}

	public function guardarNuevo() {
		$this->numero = $this->getIds($this);
		$this->beforeInsert();
		$this->transaction()->persistir(Factory::getInstance()->marcarParaInsertar($this));
	}

	public function getCodigoPermiso() {
		return true;
	}
	public function getTipoTransferenciaBase() {
		return true;
	}
	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo;
	}
	public function validarCantidadPermitidaEfectivo($cantidad) {
		return true;
	}
	public function validarCantidadPermitidaCheque($cantidad) {
		return true;
	}
	public function validarCantidadPermitidaTransferenciaBancaria($cantidad) {
		return true;
	}
	public function validarCantidadPermitidaRetencionEfectuada($cantidad) {
		return true;
	}
	public function validarCantidadPermitidaRetencionSufrida($cantidad) {
		return true;
	}
}

?>