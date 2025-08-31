<?php

/**
 * @property Array				$detalle
 * @property Motivo 			$motivo
 * @property AsientoContable	$asientoContable
 */

class RechazoChequeCabecera extends TransferenciaDobleCabecera implements DocumentoContable {
	const		_primaryKey = '["numero", "empresa"]';

	public		$idMotivo;
	protected	$_motivo;
	public		$idAsientoContable;
	protected	$_asientoContable;

	public function validarNuevo() {
		parent::validarNuevo();
		$this->motivo = Factory::getInstance()->getMotivo($this->datosSinValidar['idMotivoRechazo']);
	}

	protected function beforeCommit() {
		parent::beforeCommit();

		$cheque = $this->detalle[0]->importePorOperacion->detalle[0]->importe;

		if (!$cheque->entregadoProveedor()) {
			$this->idAsientoContable = null;
			$asiento = $this->contabilidad();
			$this->asientoContable = $asiento;
			$this->update();
		}
	}

	/************************************** CONTABILIDAD **************************************/

	public function contabilidad() {
		return Contabilidad::contabilizarDocumento($this);
	}

	public function contabilidadEmpresa() {
		return $this->empresa;
	}

	public function contabilidadNombre() {
		$nombre = 'Rechazo de cheque' . ($this->observaciones ? ' (' . $this->observaciones . ')' : '');
		return $nombre;
	}

	public function contabilidadFecha() {
		$fecha = ($this->fechaAlta ? $this->fechaAlta : Funciones::hoy());
		return $fecha;
	}

	public function contabilidadDetalle() {
		$fecha = $this->contabilidadFecha();
		$det = array();

		/* AGREGO LAS FILAS DE LOS IMPORTES */
		$importeTotal = 0;
		$i = 1;
		foreach ($this->detalle as $rechazoCheque) {
			/** @var RechazoCheque $rechazoCheque */
			if ($rechazoCheque->entradaSalida == 'S') {
				foreach ($rechazoCheque->importePorOperacion->detalle as $ixod) {
					$fila = array();
					/** @var ImportePorOperacionItem $ixod */
					$importe = $ixod->importe;
					$fila['numeroFila'] = $i;
					$fila['fechaVencimiento'] = $fecha;
					$fila['imputacion'] = $importe->getImputacion();
					$fila['importeDebe'] = 0;
					$fila['importeHaber'] = Funciones::toFloat($ixod->importe->importe, 2);
					$fila['observaciones'] = $importe->getObservacionContabilidad();
					$det[] = $fila;
					$importeTotal += $fila['importeHaber'];
					$i++;
				}
			}
		}
		foreach ($this->detalle as $rechazoCheque) {
			/** @var RechazoCheque $rechazoCheque */
			if ($rechazoCheque->entradaSalida == 'E') {
				/* AGREGO LA FILA DEL DEBE */
				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = Funciones::sumarTiempo($fecha, 2);
				$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::chequesRechazados)->imputacion->id;
				$fila['importeDebe'] = $importeTotal;
				$fila['importeHaber'] = 0;
				$fila['observaciones'] = $this->observaciones;

				//Agrego la fila DEBE antes de los detalles, para que aparezca primera
				$det = array_merge(array($fila), $det);
			}
		}

		return $det;
	}

	public function contabilidadIdAsientoContable() {
		return $this->idAsientoContable;
	}

	/************************************** ************ **************************************/

	//GETS Y SETS
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
			$this->_detalle = Factory::getInstance()->getListObject('RechazoCheque', 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND cod_rechazo_cheque = ' . Datos::objectToDB($this->numero));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getMotivo() {
		if (!isset($this->_motivo)){
			$this->_motivo = Factory::getInstance()->getVendedor($this->idMotivo);
		}
		return $this->_motivo;
	}
	protected function setMotivo($vendedor) {
		$this->_motivo = $vendedor;
		return $this;
	}
}

?>