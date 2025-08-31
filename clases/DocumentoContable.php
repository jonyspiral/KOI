<?php

interface DocumentoContable {
	/* Deberá tener el ID de asiento contable también (algo así)
	public		$idAsientoContable;
	protected	$_asientoContable;

	protected function getAsientoContable() {
		if (!isset($this->_asientoContable)){
			$this->_asientoContable = Factory::getInstance()->getAsientoContable($this->idAsientoContable);
		}
		return $this->_asientoContable;
	}
	protected function setAsientoContable($asientoContable) {
		$this->_asientoContable = $asientoContable;
		return $this;
	}

	FILL
	$recibo->idAsientoContable = $dr['cod_asiento_contable'];

	MAPPER
	$sql .= 'cod_asiento_contable, ';
	$sql .= Datos::objectToDB($recibo->asientoContable->id) . ', ';

	$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($recibo->asientoContable->id) . ', ';

	*/

	public function contabilidad();
	public function contabilidadEmpresa();
	public function contabilidadNombre();
	public function contabilidadFecha();
	public function contabilidadDetalle();
	public function contabilidadIdAsientoContable();
}

?>