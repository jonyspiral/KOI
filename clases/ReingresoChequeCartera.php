<?php

/**
 * @property Proveedor					$proveedor
 * @property AsientoContable			$asientoContable
 */

class ReingresoChequeCartera extends TransferenciaBase implements DocumentoContable {
	protected	$_entradaSalida = 'E';

	public		$idAsientoContable;
	protected	$_asientoContable;
	public		$idProveedor;
	protected	$_proveedor;

	protected function validarNuevo() {
		$this->proveedor = $this->datosSinValidar['proveedor'];

		parent::validarNuevo();
	}

	protected function beforeSave() {
		$t = 0;
		foreach($this->importesSinValidar[$this->getEntradaSalida()] as $imps) {
			foreach($imps as $imp) {
				/* @var $imp Importe */
				$t += $imp->importe;
				if ($imp->getTipoImporte() == TiposImporte::cheque) {
					/* @var $imp Cheque */
					$imp->idProveedor = null;
					$imp->concluido = 'N';
					$imp->esperandoEnBanco = null;
					$imp->guardar();
				}
			}
		}
		$this->importeTotal = $t;
	}

	public function getCodigoPermiso(){
		return PermisosUsuarioPorCaja::reingresoChequeCartera;
	}

	public function getTipoTransferenciaBase(){
		return TiposTransferenciaBase::reingresoChequeCartera;
	}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo + ($delete ? -1 : 1) * $importe;
	}

	public function validarCantidadPermitidaEfectivo($cantidad) {
		return false;
	}

	public function validarCantidadPermitidaCheque($cantidad){
		return $cantidad == 1;
	}

	public function validarCantidadPermitidaTransferenciaBancaria($cantidad){
		return false;
	}

	public function validarCantidadPermitidaRetencionEfectuada($cantidad) {
		return false;
	}

	public function validarCantidadPermitidaRetencionSufrida($cantidad) {
		return false;
	}

	protected function beforeCommit() {
		parent::beforeCommit();

		if (!$this->proveedor->id) {
			$this->idAsientoContable = null;
			$asiento = $this->contabilidad();
			$this->asientoContable = $asiento;
			$this->update();
		}
	}

	protected function beforeDelete(){
		Contabilidad::descontabilizarDocumento($this);
	}

	/************************************** CONTABILIDAD **************************************/

	public function contabilidad() {
		return Contabilidad::contabilizarDocumento($this);
	}

	public function contabilidadEmpresa() {
		return $this->empresa;
	}

	public function contabilidadNombre() {
		return 'Reingreso de cheque';
	}

	public function contabilidadFecha() {
		return ($this->fechaAlta ? $this->fechaAlta : Funciones::hoy());
	}

	public function contabilidadDetalle() {
		$fecha = $this->contabilidadFecha();
		$det = array();

		/* AGREGO LAS FILAS DE LOS IMPORTES */
		$importeTotal = 0;
		$i = 1;
		foreach ($this->importePorOperacion->detalle as $ixod) {
			$fila = array();
			/** @var ImportePorOperacionItem $ixod */
			$importe = $ixod->importe;
			$fila['numeroFila'] = $i;
			/** @var Cheque $importe */
			$fila['fechaVencimiento'] = ($importe->getTipoImporte() == TiposImporte::cheque) ? $importe->fechaVencimiento : $fecha;
			$fila['imputacion'] = $importe->getImputacion();
			$fila['importeDebe'] = Funciones::toFloat($ixod->importe->importe, 2);
			$fila['importeHaber'] = 0;
			$fila['observaciones'] = $importe->getObservacionContabilidad();
			$det[] = $fila;
			$importeTotal += $fila['importeDebe'];
			$i++;
		}

		/* AGREGO LA FILA DEL HABER */
		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $fecha;
		$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::proveedores)->idImputacion;
		$fila['importeDebe'] = 0;
		$fila['importeHaber'] = $importeTotal;
		$fila['observaciones'] = $this->observaciones;
		$det[] = $fila;

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
	protected function getProveedor() {
		if (!isset($this->_proveedor)){
			$this->_proveedor = Factory::getInstance()->getProveedor($this->idProveedor);
		}
		return $this->_proveedor;
	}
	protected function setProveedor($proveedor) {
		$this->_proveedor = $proveedor;
		return $this;
	}
}

?>