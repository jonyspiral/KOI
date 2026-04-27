<?php

/**
 * @property DocumentoAplicacionDebe	$documentoAplicacion
 * @property CuentaBancaria				$cuentaBancaria
 * @property AsientoContable			$asientoContable
 */

class Prestamo extends TransferenciaBase implements DocumentoContable {
	protected	$_entradaSalida = 'E';

	public		$idCuentaBancaria;
	protected	$_cuentaBancaria;
	public		$fecha;
	public		$importePendiente;
	public		$idAsientoContable;
	protected	$_asientoContable;

	protected function validarNuevo() {
		$caja = $this->datosSinValidar['idCaja_E'];

		if(empty($caja))
			throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');

		$cuentasBancarias = Factory::getInstance()->getListObject('CuentaBancaria', 'cod_caja = ' . Datos::objectToDB(Factory::getInstance()->getCaja($this->datosSinValidar['idCaja_E'])->id));
		$cuentaBancaria = $cuentasBancarias[0];

		if(empty($cuentaBancaria))
			throw new FactoryExceptionCustomException('La caja seleccionada no es una caja bancaria');

		$this->cuentaBancaria = $cuentaBancaria;
		$this->fecha = $this->datosSinValidar['fechaDocumento'] ? $this->datosSinValidar['fechaDocumento'] : null;

		parent::validarNuevo();
	}

	protected function beforeSave() {
		$t = 0;
		foreach($this->importesSinValidar[$this->getEntradaSalida()] as $imps) {
			foreach($imps as $imp) {
				/* @var $imp Importe */
				$t += $imp->importe;
			}
		}
		$this->importeTotal = $t;
		$this->importePendiente = $t;
	}

	public function getCodigoPermiso(){
		return PermisosUsuarioPorCaja::prestamo;
	}

	public function getTipoTransferenciaBase(){
		return TiposTransferenciaBase::prestamo;
	}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo + ($delete ? -1 : 1) * $importe;
	}

	public function validarCantidadPermitidaEfectivo($cantidad) {
		if ($cantidad > 1) {
			throw new FactoryExceptionCustomException('Sólo se puede ingresar un importe de tipo efectivo');
		}
		return true;
	}

	public function validarCantidadPermitidaCheque($cantidad){
		return false;
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

	public function getTextoDe($conTipo = false) {
		return ($conTipo ? 'Cuenta: ' : '') . $this->cuentaBancaria->getIdNombre();
	}

	protected function beforeCommit() {
		parent::beforeCommit();

		$this->idAsientoContable = null;
		$asiento = $this->contabilidad();
		$this->asientoContable = $asiento;
		$this->update();
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
		$nombre = 'Préstamo' . ($this->observaciones ? ' (' . $this->observaciones . ')' : '');
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
		foreach ($this->importePorOperacion->detalle as $ixod) {
			$fila = array();
			/** @var ImportePorOperacionItem $ixod */
			$importe = $ixod->importe;
			$fila['numeroFila'] = $i;
			$fila['fechaVencimiento'] = $fecha;
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
		$fila['imputacion'] = $this->getImputacionHaber();
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

	public function getImputacionHaber() {
		$return = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::prestamosBancarios)->idImputacion;
		return $return;
	}

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
	protected function getCuentaBancaria() {
		if (!isset($this->_cuentaBancaria)){
			$this->_cuentaBancaria = Factory::getInstance()->getCuentaBancaria($this->idCuentaBancaria);
		}
		return $this->_cuentaBancaria;
	}
	protected function setCuentaBancaria($cuentaBancaria) {
		$this->_cuentaBancaria = $cuentaBancaria;
		return $this;
	}
	/*protected function getDocumentoAplicacion() {
		if (!isset($this->_documentoAplicacion)){
			$this->_documentoAplicacion = Factory::getInstance()->getDocumentoAplicacionHaber($this->empresa, 1, 'REC', $this->numero, 'R');
		}
		return $this->_documentoAplicacion;
	}
	protected function setDocumentoAplicacion($documentoAplicacion) {
		$this->_documentoAplicacion = $documentoAplicacion;
		return $this;
	}
	protected function getLetra() {
		if (!isset($this->_letra)){
			$this->_letra = 'R';
		}
		return $this->_letra;
	}*/
}

?>