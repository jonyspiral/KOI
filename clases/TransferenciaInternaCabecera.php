<?php

/**
 * @property array				$detalle
 * @property AsientoContable	$asientoContable
 */

class TransferenciaInternaCabecera extends TransferenciaDobleCabecera implements DocumentoContable {
	const		_primaryKey = '["numero", "empresa"]';
	public		$fechaDocumento;
	public		$idAsientoContable;
	protected	$_asientoContable;

	protected function beforeCommit() {
		parent::beforeCommit();

		$this->idAsientoContable = null;
		$asiento = $this->contabilidad();
		$this->asientoContable = $asiento;
		$this->update();
	}

	public function validarNuevo() {
		$fechaDocumento = $this->datosSinValidar['fechaDocumento'];

		if(empty($fechaDocumento)){
			throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');
		}

		$this->fechaDocumento = $fechaDocumento;
	}

	/************************************** CONTABILIDAD **************************************/

	public function contabilidad() {
		return Contabilidad::contabilizarDocumento($this);
	}

	public function contabilidadEmpresa() {
		return $this->empresa;
	}

	public function contabilidadNombre() {
		$nombre = 'Transferencia interna' . ($this->observaciones ? ' (' . $this->observaciones . ')' : '');
		return $nombre;
	}

	public function contabilidadFecha() {
		$fecha = $this->fechaDocumento;
		return $fecha;
	}

	public function contabilidadDetalle() {
		$fecha = $this->contabilidadFecha();
		$det = array();

		/* AGREGO LAS FILAS DE LOS IMPORTES */
		$i = 1;
		foreach ($this->detalle as $tid) {
			/** @var TransferenciaInterna $tid */
			foreach ($tid->importePorOperacion->detalle as $ixod) {
				$fila = array();
				/** @var ImportePorOperacionItem $ixod */
				$importe = $ixod->importe;
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = $importe->getImputacion();
				$fila['importeDebe'] = $tid->entradaSalida == 'E' ? Funciones::toFloat($ixod->importe->importe, 2) : 0;
				$fila['importeHaber'] = $tid->entradaSalida == 'S' ? Funciones::toFloat($ixod->importe->importe, 2) : 0;
				$fila['observaciones'] = $importe->getObservacionContabilidad();
				$det[] = $fila;
				$i++;
			}
		}

		return $det;
	}

	public function contabilidadIdAsientoContable() {
		return $this->idAsientoContable;
	}

	/************************************** ************ **************************************/

	//GETS y SETS
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
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->numero)){
			$this->_detalle = Factory::getInstance()->getListObject('TransferenciaInterna', 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND cod_transferencia_int = ' . Datos::objectToDB($this->numero));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
}

?>