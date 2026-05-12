<?php

/**
 * @property FormularioRendicionGastos			$formulario
 * @property DocumentoProveedorAplicacionHaber	$documentoAplicacion
 * @property array								$gastitos
 * @property AsientoContable					$asientoContable
 */

class RendicionGastos extends TransferenciaBase implements DocumentoContable {
	protected	$_entradaSalida = 'S';

	public		$fecha;
	public		$importePendiente;
	public		$validarEfectivoParcial = true;
	public		$formulario;
	protected	$_documentoAplicacion;
	protected	$_gastitos;
	public		$idAsientoContable;
	protected	$_asientoContable;

	public function beforeSave(){
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

	protected function validarNuevo() {
		$fechaDocumento = $this->datosSinValidar['fechaDocumento'];

		if(empty($fechaDocumento))
			throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');

		$this->fecha = $fechaDocumento;

		parent::validarNuevo();
	}

	public function getCodigoPermiso() {
		return PermisosUsuarioPorCaja::rendicionGastos;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::rendicionGastos;
	}

	public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false) {
		return $importeViejo - ($delete ? -1 : 1) * $importe;
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

	//Formulario
	public function abrir() {
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
	}

	public function crear() {
		$this->crearFormulario();
		$this->llenarFormulario();
		return $this->formulario->crear();
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioRendicionGastos();
	}

	protected function llenarFormulario() {
		$this->formulario->id = $this->numero;
		$this->formulario->empresa = $this->empresa;
		$this->formulario->importeTotal = $this->importeTotal;
		$this->formulario->fecha = $this->fecha;
		$this->formulario->observaciones = $this->observaciones;
		$this->formulario->aplicaciones = $this->documentoAplicacion->hijas;
		$this->formulario->gastitos = $this->gastitos;
	}

	public function beforeCommitDelete() {
		foreach ($this->gastitos as $gastito) {
			/** @var Gastito $gastito */
			$gastito->idRendicionGastos = null;
			$gastito->rendicionGastos = null;
			$gastito->guardar();
		}
	}

	public function beforeCommit() {
		parent::beforeCommit();

		foreach ($this->datosSinValidar['gastitos'] as $gastito) {
			/** @var Gastito $gastito */
			$gastito->rendicionGastos = $this;
			$gastito->guardar();
		}

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
		$nombre = 'Rendición de gastos' . ($this->observaciones ? ' (' . $this->observaciones . ')' : '');
		return $nombre;
	}

	public function contabilidadFecha() {
		$fecha = ($this->fecha ? $this->fecha : Funciones::hoy());
		return $fecha;
	}

	public function contabilidadDetalle() {
		$fecha = $this->contabilidadFecha();
		$det = array();

		$i = 1;

		/* AGREGO LA FILA DEL DEBE */
		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $fecha;
		$fila['imputacion'] = $this->getImputacionDebe();
		$fila['importeDebe'] = $this->importeTotal;
		$fila['importeHaber'] = 0;
		$fila['observaciones'] = $this->observaciones;
		$det[] = $fila;
		$i++;

		/* AGREGO LA FILA DEL HABER */
		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $fecha;
		$fila['imputacion'] = $this->getImputacionHaber();
		$fila['importeDebe'] = 0;
		$fila['importeHaber'] = $this->importeTotal;
		$fila['observaciones'] = $this->observaciones;
		$det[] = $fila;

		return $det;
	}

	public function contabilidadIdAsientoContable() {
		return $this->idAsientoContable;
	}

	/************************************** ************ **************************************/

	public function getImputacionDebe() {
		$return = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::gastosARendir)->idImputacion;
		return $return;
	}

	public function getImputacionHaber() {
		$return = $this->importePorOperacion->caja->idImputacion;
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
	protected function getDocumentoAplicacion() {
		if (!isset($this->_documentoAplicacion)){
			$this->_documentoAplicacion = Factory::getInstance()->getDocumentoProveedorAplicacionHaber($this->empresa, $this->numero, 'REN');
		}
		return $this->_documentoAplicacion;
	}

	protected function setDocumentoAplicacion($documentoAplicacion) {
		$this->_documentoAplicacion = $documentoAplicacion;
		return $this;
	}
	protected function getGastitos() {
		if (!isset($this->_gastitos)){
			$where = 'cod_rendicion_gastos = ' . Datos::objectToDB($this->numero) . ' AND empresa = ' . Datos::objectToDB($this->empresa);
			$this->_gastitos = Factory::getInstance()->getListObject('Gastito', $where);
		}
		return $this->_gastitos;
	}
}

?>