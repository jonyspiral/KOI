<?php

/**
 * @property Proveedor									$proveedor
 * @property TipoFactura								$tipo
 * @property AsientoContable							$asientoContable
 * @property Usuario									$usuario
 * @property DocumentoProveedorItem[]					$detalle
 * @property ImpuestoPorDocumentoProveedor[]			$impuestos
 * @property DocumentoGastoDatos						$documentoGastoDatos
 * @property bool										$esProveedor
 * @property String										$nroDocumentoCompleto
 */

class DocumentoProveedor extends Base implements DocumentoContable, DocumentoConCierreFiscal {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$empresa;
	public		$puntoVenta;
	public		$idTipo;
	protected	$_tipo;
	public		$tipoDocum;
	public		$nroDocumento;
	protected	$_nroDocumentoCompleto;
	public		$letra;
	public		$idProveedor;
	protected	$_proveedor;
	public		$operacionTipo;
	public		$fecha;
	public		$netoGravado;
	public		$netoNoGravado;
	public		$importeTotal;
	public		$importePendiente;
	public		$condicionPlazoPago;
	public		$fechaVencimiento;
	public		$fechaPeriodoFiscal;
	public		$observaciones;
	public		$documentoEnConflicto;
	public		$idAsientoContable;
	protected	$_asientoContable;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	protected	$_detalle;
	protected	$_impuestos;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	public		$idViejo;
	public		$idDocumentoGastoDatos;
	protected	$_documentoGastoDatos;
	public		$facturaGastos;
	protected 	$_esProveedor;

	public function getLetra() {
		if (!isset($this->_proveedor))
			throw new Exception('Antes de calcular la letra debe asignarse el proveedor');
		return $this->proveedor->condicionIva->letraFactura;
	}

	public function tieneDetalle() {
		return (count($this->detalle) > 0 ? true : false);
	}

	public function tieneImpuestos() {
		return (count($this->impuestos) > 0 ? true : false);
	}

	public function esFacturaGastos() {
		return $this->facturaGastos == 'S';
	}

	public function guardar() {
		$asiento = $this->contabilidad();
		$this->asientoContable = $asiento;
		return parent::guardar();
	}

	public function borrar() {
		foreach($this->detalle as $item) {
			$item->borrar();
		}
		parent::borrar();
		$this->contabilidad();

		return $this;
	}

	protected function validarGuardar() {
		if(!(Funciones::esFechaMenorOIgual($this->fecha, Funciones::hoy()))){
			throw new FactoryExceptionCustomException('La fecha del documento no puede ser posterior a la fecha de hoy');
		}

		if(!(Funciones::esFechaMenorOIgual($this->fecha, $this->fechaVencimiento))){
			throw new FactoryExceptionCustomException('La fecha de vencimiento no puede ser anterior a la fecha del documento');
		}

		$this->cierreFiscalComprobarFechas();
	}

	/************************************** CONTABILIDAD **************************************/

	public function contabilidad() {
		return Contabilidad::contabilizarDocumento($this);
	}

	public function contabilidadEmpresa() {
		return $this->empresa;
	}

	public function contabilidadNombre() {
		$return = '[' . $this->tipoDocum . '] ';
		$return .= ($this->esFacturaGastos()) ? 'Gastos: ' . $this->documentoGastoDatos->razonSocial : 'Proveedor: ' . $this->proveedor->razonSocial;
		$return .= ' | ' . Funciones::padLeft($this->puntoVenta, 4) . '-' . Funciones::padLeft($this->nroDocumento, 8);
		return $return;
	}

	public function contabilidadFecha() {
		return $this->fechaPeriodoFiscal;
	}

	public function contabilidadDetalle() {
		$det = array();

		/* AGREGO LAS FILAS DE LOS IMPORTES */
		$importeTotal = 0;
		$i = 1;
		$agrupados = array();
		foreach ($this->detalle as $item) {
			/** @var DocumentoProveedorItem $item */
			if (!isset($agrupados[$item->imputacion->id])) {
				$agrupados[$item->imputacion->id] = 0;
			}
			$agrupados[$item->imputacion->id] += Funciones::toFloat($item->importe, 2);
		}
		foreach ($agrupados as $imputacion => $importe) {
			$fila = array();
			$fila['numeroFila'] = $i;
			$fila['fechaVencimiento'] = $this->fecha;
			$fila['imputacion'] = $imputacion;
			$fila['importeDebe'] = Funciones::toFloat($importe, 2);
			$fila['importeHaber'] = 0;
			$fila['observaciones'] = '';
			$det[] = $fila;
			$importeTotal += $fila['importeDebe'];
			$i++;
		}
		foreach ($this->impuestos as $impuesto) {
			/** @var ImpuestoPorDocumentoProveedor $impuesto */
			$fila = array();
			$fila['numeroFila'] = $i;
			$fila['fechaVencimiento'] = $this->fecha;
			$fila['imputacion'] = $impuesto->impuesto->imputacion->id;
			$fila['importeDebe'] = Funciones::toFloat($impuesto->importe, 2);
			$fila['importeHaber'] = 0;
			$fila['observaciones'] = '';
			$det[] = $fila;
			$importeTotal += $fila['importeDebe'];
			$i++;
		}

		/* AGREGO LA FILA DEL HABER */
		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $this->fecha;
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

	/************************************ CIERRES FISCALES ************************************/

	public function cierreFiscalComprobarFechas() {
		foreach ($this->cierreFiscalFechasParaComprobar() as $fechaParaComprobar) {
			CierrePeriodoFiscal::comprobarFecha($fechaParaComprobar['fecha'], $fechaParaComprobar['tipo']);
		}
	}

	public function cierreFiscalFechasParaComprobar() {
		return array(
			array(
				'tipo' => Factory::getInstance()->getTipoPeriodoFiscal(TipoPeriodoFiscal::TIPO_EJERCICIO_CONTABLE),
				'fecha' => $this->fechaPeriodoFiscal
			),
			array(
				'tipo' => Factory::getInstance()->getTipoPeriodoFiscal(TipoPeriodoFiscal::TIPO_IVA),
				'fecha' => $this->fechaPeriodoFiscal
			)
		);
	}

	/************************************** ************ **************************************/

	public function getImputacionHaber() {
		$return = ($this->esFacturaGastos()) ? Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::gastosARendir)->idImputacion : $this->proveedor->idImputacionHaber;
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
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->id)){
			$this->_detalle = Factory::getInstance()->getListObject('DocumentoProveedorItem', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_documento_proveedor = ' . Datos::objectToDB($this->id));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getDocumentoGastoDatos() {
		if (!isset($this->_documentoGastoDatos)){
			$this->_documentoGastoDatos = Factory::getInstance()->getDocumentoGastoDatos($this->idDocumentoGastoDatos);
		}
		return $this->_documentoGastoDatos;
	}
	protected function setDocumentoGastoDatos($documentoGastoDatos) {
		$this->_documentoGastoDatos = $documentoGastoDatos;
		return $this;
	}
	protected function getEsProveedor() {
		if (!isset($this->_esProveedor)){
			return !!$this->proveedor->id;
		}
		return $this->_esProveedor;
	}
	protected function setEsProveedor($esProveedor) {
		$this->_esProveedor = $esProveedor;
		return $this;
	}
	protected function getImpuestos() {
		if (!isset($this->_impuestos) && isset($this->id)){
			$this->_impuestos = Factory::getInstance()->getListObject('ImpuestoPorDocumentoProveedor', 'cod_documento_proveedor = ' . Datos::objectToDB($this->id));
		}
		return $this->_impuestos;
	}
	protected function setImpuestos($impuestos) {
		$this->_impuestos = $impuestos;
		return $this;
	}
	protected function getNroDocumentoCompleto() {
		if (!isset($this->_nroDocumentoCompleto)){
			$this->_nroDocumentoCompleto = Funciones::padLeft((is_null($this->puntoVenta) ? '0' : $this->puntoVenta), 4, 0) . '-' . Funciones::padLeft((is_null($this->nroDocumento) ? '0' : $this->nroDocumento), 8, 0);
		}
		return $this->_nroDocumentoCompleto;
	}
	protected function setNroDocumentoCompleto($nroDocumentoCompleto) {
		$this->_nroDocumentoCompleto = $nroDocumentoCompleto;
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
	protected function getTipo() {
		if (!isset($this->_tipo)){
			$this->_tipo = Factory::getInstance()->getTipoFactura($this->idTipo);
		}
		return $this->_tipo;
	}
	protected function setTipo($tipo) {
		$this->_tipo = $tipo;
		return $this;
	}
	protected function getUsuario() {
		if (!isset($this->_usuario)){
			$this->_usuario = Factory::getInstance()->getUsuario($this->idUsuario);
		}
		return $this->_usuario;
	}
	protected function setUsuario($usuario) {
		$this->_usuario = $usuario;
		return $this;
	}
}

?>