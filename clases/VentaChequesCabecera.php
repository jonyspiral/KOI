<?php

/**
 * @property Array									$detalle
 * @property AsientoContable						$asientoContable
 * @property FormularioDepositoBancarioTemporal		$formulario
 */

class VentaChequesCabecera extends TransferenciaDobleCabecera implements DocumentoContable {
	const		_primaryKey = '["numero", "empresa"]';

	public		$fecha;
	public		$idAsientoContable;
	protected	$_asientoContable;
	private		$formulario;

	public function validarNuevo() {
		$this->fecha = $this->datosSinValidar['fecha'];
		parent::validarNuevo();
	}

	//formulario
	public function abrir() {
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioVentaCheques();
	}

	protected function llenarFormulario() {
		if($this->detalle[0]->esOperacionEntrada()){
			$entrada = $this->detalle[0];
			$salida = $this->detalle[1];
		}else{
			$entrada = $this->detalle[1];
			$salida = $this->detalle[0];
		}

		$cuentasBancarias = Factory::getInstance()->getListObject('CuentaBancaria', 'cod_caja = ' . Datos::objectToDB($entrada->importePorOperacion->caja->id));
		$cuentaBancaria = $cuentasBancarias[0];

		$this->formulario->id = $this->numero;
		$this->formulario->cajaSalida = $salida->importePorOperacion->caja;
		$this->formulario->cajaEntrada = $entrada->importePorOperacion->caja;
		$this->formulario->cuentaBancaria = $cuentaBancaria;
		$this->formulario->fecha = $this->fecha;
		$this->formulario->cheques = $salida->importePorOperacion->getCheques();
		$this->formulario->importeTotal = $salida->importeTotal;
	}

	protected function beforeCommit() {
		parent::beforeCommit();

		$this->idAsientoContable = null;
		$asiento = $this->contabilidad();
		$this->asientoContable = $asiento;
		$this->update();
	}

	/************************************** CONTABILIDAD **************************************/

	public function contabilidad() {
		return Contabilidad::contabilizarDocumento($this);
	}

	public function contabilidadEmpresa() {
		return $this->empresa;
	}

	public function contabilidadNombre() {
		$nombre = 'Venta de cheques' . ($this->observaciones ? ' (' . $this->observaciones . ')' : '');
		return $nombre;
	}

	public function contabilidadFecha() {
		$fecha = ($this->fecha ? $this->fecha : Funciones::hoy());
		return $fecha;
	}

	public function contabilidadDetalle() {
		$fecha = $this->contabilidadFecha();
		$det = array();

		/* AGREGO LAS FILAS DE LOS IMPORTES */
		$importeTotal = 0;
		$i = 1;
		foreach ($this->detalle as $tid) {
			/** @var TransferenciaInterna $tid */
			if ($tid->entradaSalida == 'S') {
				foreach ($tid->importePorOperacion->detalle as $ixod) {
					$fila = array();
					/** @var ImportePorOperacionItem $ixod */
					$importe = $ixod->importe;
					$fila['numeroFila'] = $i;
					/** @var Cheque $importe */ //PUEDE NO SER UN CHEQUE, pero lo pongo así para que me tome ->fechaVencimiento
					$fila['fechaVencimiento'] = ($importe->getTipoImporte() == TiposImporte::cheque) ? $importe->fechaVencimiento : $fecha;
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
		foreach ($this->detalle as $tid) {
			/** @var TransferenciaInterna $tid */
			if ($tid->entradaSalida == 'E') {
				/* AGREGO LA FILA DEL DEBE */
				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = $tid->importePorOperacion->caja->idImputacion;
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
			$this->_detalle = Factory::getInstance()->getListObject('VentaCheques', 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND cod_venta_cheques = ' . Datos::objectToDB($this->numero));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
}

?>