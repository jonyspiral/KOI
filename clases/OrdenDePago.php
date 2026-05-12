<?php

/**
 * @property string								$tipoOP
 * @property Imputacion							$imputacion
 * @property Proveedor							$proveedor
 * @property FormularioOrdenDePago				$formulario
 * @property DocumentoProveedorAplicacionHaber	$documentoAplicacion
 * @property AsientoContable					$asientoContable
 */

class OrdenDePago extends TransferenciaBase implements DocumentoContable {
	protected	$_entradaSalida = 'S';

	public		$idProveedor;
	protected	$_proveedor;
	protected	$_tipoOP; //P u O
	public		$tipoOperacion;	//O o P
	public		$idImputacion;
	protected	$_imputacion;
	public		$beneficiario;
	public		$mailEnviado;
	public		$retieneGanancias;
	public		$fecha;
	public		$importePendiente;
	public		$importeSujetoRetencion;
	public		$idAsientoContable;
	protected	$_asientoContable;
	public		$formulario;
	protected	$_documentoAplicacion;
	//public		$jurisdicciones; (?)

	public function expand() {
		$this->imputacion;
		return parent::expand();
	}

	public function esAutonoma() {
		return empty($this->idProveedor);
	}

	protected function validarNuevo() {
		//validaciones propias de la orden de pago
		$tipoOP = $this->datosSinValidar['tipoOP'];
		$idProv = $this->datosSinValidar['idProveedor'];
		$benef = $this->datosSinValidar['beneficiario'];
		$idImputacion = $this->datosSinValidar['idImputacion'];
		$fechaDocumento = Funciones::hoy();
		$caja = $this->datosSinValidar['idCaja_' . $this->getEntradaSalida()];

		if ($tipoOP == 'O') {
			if (empty($benef) || empty($benef)) {
				throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');
			} else {
				$this->beneficiario = $benef;
				$this->imputacion = Factory::getInstance()->getImputacion($idImputacion);
			}
		} elseif ($tipoOP == 'P') {
			try {
				$this->proveedor = Factory::getInstance()->getProveedor($idProv);
				$this->beneficiario = $this->proveedor->razonSocial;
				$this->imputacion = $this->proveedor->imputacionGeneral;
			} catch (Exception $ex) {
				throw new FactoryExceptionCustomException('Debe completar el campo proveedor');
			}
		} else {
			throw new FactoryExceptionCustomException('Ocurrió un error de inconsistencia de datos');
		}

		if(empty($fechaDocumento) || empty($caja))
			throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');

		$this->mailEnviado = 'N';
		$this->retieneGanancias = $this->datosSinValidar['retieneGanancias'] != 'S' ? 'N' : 'S';
		$this->tipoOperacion = $tipoOP;
		$this->fecha = $fechaDocumento;

		parent::validarNuevo();

		$this->validarSiHayChequesConcluidos();
	}

	public function beforeSave(){
		$t = 0;
		foreach($this->importesSinValidar[$this->getEntradaSalida()] as $imps) {
			foreach($imps as $imp) {
				/* @var $imp Importe */
				$t += $imp->importe;
				if ($imp->getTipoImporte() == TiposImporte::cheque) {
					/* @var $imp Cheque */
					!$imp->esPropio() && $imp->concluido = 'S';
					$imp->esPropio() && $imp->esperandoEnBanco = 'D';
					$imp->proveedor = $this->proveedor;
				}
			}
		}
		$this->importeTotal = $t;
		$this->importePendiente = $t;
	}

	public function getCodigoPermiso() {
		return TiposTransferenciaBase::ordenDePago;
	}

	public function getTipoTransferenciaBase() {
		return TiposTransferenciaBase::ordenDePago;
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
		return true;
	}

	public function validarCantidadPermitidaTransferenciaBancaria($cantidad){
		return true;
	}

	public function validarCantidadPermitidaRetencionEfectuada($cantidad) {
		if ($cantidad > 1) {
			throw new FactoryExceptionCustomException('Sólo se puede ingresar una retención en una órden de pago');
		}
		return true;
	}

	public function validarCantidadPermitidaRetencionSufrida($cantidad) {
		return false;
	}

	public function getTextoPara($conTipo = false) {
		return $this->esAutonoma() ? (($conTipo ? 'Beneficiario: ' : '') . $this->beneficiario) : (($conTipo ? 'Proveedor: ' : '') . $this->proveedor->getIdNombre());
	}

	//formulario
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
		$this->formulario = new FormularioOrdenDePago();
	}

	protected function llenarFormulario() {
		$cheques = $this->getCheques();
		$transferencias = $this->getTransferencias();
		$retencionesEfectuadas = $this->getRetencionesEfectuadas();
		$totalCheques = 0;
		$totalTransferencias = 0;

		foreach($cheques as $cheque)
			$totalCheques += $cheque->importe;

		foreach($transferencias as $transferencia)
			$totalTransferencias += $transferencia->importe;

		$this->formulario->id = $this->numero;
		$this->formulario->fecha = $this->fecha;
		$this->formulario->proveedor = $this->proveedor;
		$this->formulario->beneficiario = $this->beneficiario;
		$this->formulario->cheques = $cheques;
		$this->formulario->transferencias = $transferencias;
		$this->formulario->retenciones = $retencionesEfectuadas;
		$this->formulario->montoEfectivo = $this->getEfectivo();
		$this->formulario->montoCheques = $totalCheques;
		$this->formulario->montoTransferencias = $totalTransferencias;
		$this->formulario->montoTotal = $this->importeTotal;
		$this->formulario->montoSujetoRetenciones = $this->importeSujetoRetencion;
		$this->formulario->empresa = $this->empresa;
		$this->formulario->imputacionNombre = $this->imputacion->nombre;
		$this->formulario->aplicaciones = ($this->anulado() ? array() : $this->documentoAplicacion->hijas);
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
		$nombre = 'Orden de pago de tipo irreconocible (probable error)';
		if ($this->tipoOperacion == 'P') {
			$nombre = 'Pago a proveedores: "' . $this->proveedor->razonSocial . '"';
		} elseif ($this->tipoOperacion == 'O') {
			$nombre = 'Pago autónomo: "' . $this->beneficiario . '"';
		}
		return $nombre;
	}

	public function contabilidadFecha() {
		return $this->fecha;
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

		/* AGREGO LA FILA DEL DEBE */
		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $fecha;
		$fila['imputacion'] = $this->getImputacionDebe();
		$fila['importeDebe'] = $importeTotal;
		$fila['importeHaber'] = 0;
		$fila['observaciones'] = $this->observaciones;

		//Agrego la fila DEBE antes de los detalles, para que aparezca primera
		$det = array_merge(array($fila), $det);

		return $det;
	}

	public function contabilidadIdAsientoContable() {
		return $this->idAsientoContable;
	}

	/************************************** ************ **************************************/

	public function getImputacionDebe() {
		$return = false;
		if ($this->tipoOperacion == 'P') {
			$return = $this->proveedor->idImputacionHaber;
		} elseif ($this->tipoOperacion == 'O') {
			$return = $this->imputacion->id;
		}
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
			$this->_documentoAplicacion = Factory::getInstance()->getDocumentoProveedorAplicacionHaber($this->empresa, $this->numero, 'OP');
		}
		return $this->_documentoAplicacion;
	}
	protected function setDocumentoAplicacion($documentoAplicacion) {
		$this->_documentoAplicacion = $documentoAplicacion;
		return $this;
	}
	protected function getHaciaDesdeTransferenciaBancariaOperacion() {
		return $this->proveedor->getIdNombre();
	}
	protected function getImputacion() {
		if (!isset($this->_imputacion)){
			$this->_imputacion = Factory::getInstance()->getImputacion($this->idImputacion);
		}
		return $this->_imputacion;
	}
	protected function setImputacion($imputacion) {
		$this->_imputacion = $imputacion;
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
	protected function getTipoOP() {
		if (!isset($this->_tipoOP)){
			$this->_tipoOP = (isset($this->idProveedor)) ? 'P' : 'O';
		}
		return $this->_tipoOP;
	}
}

?>