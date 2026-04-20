<?php

abstract class AcreditarDebitarCheque extends TransferenciaDoble {
	const		_primaryKey = '["numero", "empresa", "entradaSalida"]';

	public function getCodigoPermiso() {}

	public function getTipoTransferenciaBase() {}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {}

	public function validarCantidadPermitidaEfectivo($cantidad) {}

	public function validarCantidadPermitidaCheque($cantidad) {}

	public function validarCantidadPermitidaTransferenciaBancaria($cantidad) {}

	public function validarCantidadPermitidaRetencionEfectuada($cantidad) {}

	public function validarCantidadPermitidaRetencionSufrida($cantidad) {}
}

?>