<?php

/**
 * @property Cliente					$cliente
 * @property Imputacion					$imputacion
 * @property DocumentoAplicacionDebe	$documentoAplicacion
 * @property AsientoContable			$asientoContable
 * @property Ecommerce_Order			$ecommerceOrder
 * @property FormularioRecibo			$formulario
 */

class Recibo extends TransferenciaBase implements DocumentoContable {
	protected	$_entradaSalida = 'E';

	public		$idCliente;
	protected	$_cliente;
	public		$tipoOperacion;	//CD u OI
	public		$idImputacion;
	protected	$_imputacion;
	public		$recibidoDe;
	public		$mailEnviado;
	public		$fecha;
	public		$importePendiente;
	public		$formulario;
	protected	$_documentoAplicacion;
	protected	$_letra;
	public		$idAsientoContable;
	protected	$_asientoContable;
	public		$idEcommerceOrder;
	protected	$_ecommerceOrder;
	public		$fechaPonderadaPago;
	public		$numeroReciboProvisorio;


	public function expand() {
		$this->imputacion;
		return parent::expand();
	}

	public function esEcommerce() {
		return !is_null($this->idEcommerceOrder);
	}

	public function esOtrosIngresos() {
		return empty($this->idCliente);
	}

	protected function validarNuevo() {
		//validaciones propias del recibo
		$tipoRec = $this->datosSinValidar['tipoRecibo'];
		$idCli = $this->datosSinValidar['idCliente'];
		$recDe = $this->datosSinValidar['recibidoDe'];
		$fechaDocumento = Funciones::hoy();
		$idImputacion = $this->datosSinValidar['idImputacion'];
		$caja = $this->datosSinValidar['idCaja_E'];
		$numeroReciboProvisorio = $this->datosSinValidar['numeroReciboProvisorio'];

		if ($tipoRec == 'OI') {
			if (empty($recDe)) {
				throw new FactoryExceptionCustomException('Debe completar el campo "Recibido de"');
			} else {
				$this->recibidoDe = $recDe;
			}
		} elseif ($tipoRec == 'CD') {
			try {
				$this->cliente = Factory::getInstance()->getCliente($idCli);
				$this->recibidoDe = $this->cliente->razonSocial;
			} catch (Exception $ex) {
				throw new FactoryExceptionCustomException('Debe completar el campo cliente');
			}
		} else {
			throw new FactoryExceptionCustomException('Ocurrió un error de inconsistencia de datos');
		}
		if(empty($fechaDocumento) || empty($idImputacion) || empty($caja)/* || empty($numeroReciboProvisorio)*/) //Ari, antes de romperme absolutamente to_do, avisame =D (rompe Ecommerce esto, haceme acordar y lo charlamos)
			throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');

		$this->mailEnviado = 'N';
		$this->tipoOperacion = $tipoRec;
		$this->imputacion = Factory::getInstance()->getImputacion($idImputacion);
		$this->fecha = $fechaDocumento;
		$this->numeroReciboProvisorio = $numeroReciboProvisorio;

		$i = 0;
		foreach($this->importesSinValidar[$this->entradaSalida]['C'] as $cheque){
			$this->importesSinValidar[$this->entradaSalida]['C'][$i]['id'] = null;
			$i++;
		}

		parent::validarNuevo();

		$this->validarSiHayChequesConcluidos();
	}

	protected function beforeSave() {
		$fechaRec = Funciones::hoy();
		$arrFechas = array();
		$t = 0;
		foreach($this->importesSinValidar[$this->getEntradaSalida()] as $imps) {
			foreach($imps as $imp) {
				/* @var $imp Importe */
				$t += $imp->importe;
				if ($imp->getTipoImporte() == TiposImporte::cheque) {
					/* @var $imp Cheque */
					$imp->cliente = $this->cliente;
					$arrFechas[$imp->fechaVencimiento] = isset($arrFechas[$imp->fechaVencimiento]) ? ($arrFechas[$imp->fechaVencimiento] + $imp->importe) : $imp->importe;
				} else {
					$arrFechas[$fechaRec] = isset($arrFechas[$fechaRec]) ? ($arrFechas[$fechaRec] + $imp->importe) : $imp->importe;
				}
			}
		}
		$this->importeTotal = $t;
		$this->importePendiente = $t;

		/* Tratamiento para obtener fecha_ponderada_pago. Lo hago acá porque necesito el ->importeTotal */
		$acumuladoDias = 0;
		foreach ($arrFechas as $fecha => $importe) {
			$porcentaje = $importe / $this->importeTotal;
			$dias = Funciones::diferenciaFechas($fechaRec, $fecha);
			$acumuladoDias += ($porcentaje * $dias);
		}
		$acumuladoDias = round($acumuladoDias);
		$this->fechaPonderadaPago = Funciones::sumarTiempo($fechaRec, $acumuladoDias);
	}

	public function getCodigoPermiso(){
		return PermisosUsuarioPorCaja::recibo;
	}

	public function getTipoTransferenciaBase(){
		return TiposTransferenciaBase::recibo;
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
		return true;
	}

	public function validarCantidadPermitidaTransferenciaBancaria($cantidad){
		return true;
	}

	public function validarCantidadPermitidaRetencionEfectuada($cantidad) {
		return false;
	}

	public function validarCantidadPermitidaRetencionSufrida($cantidad) {
		return true;
	}

	public function getTextoDe($conTipo = false) {
		return $this->esOtrosIngresos() ? (($conTipo ? 'Recibido de: ' : '') . $this->recibidoDe) : (($conTipo ? 'Cliente: ' : '') . $this->cliente->getIdNombre());
	}

	//formulario
	public function abrir() {
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
		$this->formulario->pdf->deleteFiles();
	}

	public function crear() {
		$this->crearFormulario();
		$this->llenarFormulario();
		return $this->formulario->crear();
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioRecibo();
	}

	protected function llenarFormulario() {
		$efectivos = $this->importePorOperacion->getEfectivo();
		$cheques = $this->importePorOperacion->getCheques();
		$transferencias = $this->importePorOperacion->getTransferencias();
		$retencionesSufridas = $this->importePorOperacion->getRetencionesSufridas();
		$totalEfectivo = 0;
		$totalCheques = 0;
		$totalTransferencias = 0;

		foreach($efectivos as $item)
			$totalEfectivo += $item->importe;

		foreach($cheques as $cheque)
			$totalCheques += $cheque->importe;

		foreach($transferencias as $transferencia)
			$totalTransferencias += $transferencia->importe;

		$this->formulario->id = $this->numero;
		$this->formulario->fecha = $this->fecha;
		$this->formulario->cliente = $this->cliente;
		$this->formulario->recibidoDe = $this->recibidoDe;
		$this->formulario->cheques = $cheques;
		$this->formulario->retenciones = $retencionesSufridas;
		$this->formulario->montoEfectivo = $totalEfectivo;
		$this->formulario->montoCheques = $totalCheques;
		$this->formulario->montoTransferencias = $totalTransferencias;
		$this->formulario->montoTotal = $this->importeTotal;
		$this->formulario->empresa = $this->empresa;
		$this->formulario->aplicaciones = $this->documentoAplicacion->hijas;
		$this->formulario->observaciones = $this->observaciones;
	}

	protected function borrarCheque(Cheque $cheque){
		if (!$cheque->esperandoEnBanco && !$cheque->concluido() && $this->importePorOperacion->caja->id == $cheque->cajaActual->id){
			Factory::getInstance()->marcarParaBorrar($cheque);
			$this->transaction()->persistir($cheque);
		} else {
			throw new FactoryExceptionCustomException('El recibo no puede borrarse ya que el cheque Nº ' . $cheque->numero . ' ya fue utilizado en alguna operación');
		}
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
		$nombre = 'Recibo de tipo irreconocible (probable error)';
		if ($this->tipoOperacion == 'CD') {
			$nombre = 'Recibo de cobranzas: "' . $this->cliente->razonSocial . '"';
		} elseif ($this->tipoOperacion == 'OI') {
			$nombre = 'Recibo de otros ingresos: "' . $this->recibidoDe . '"';
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
		$return = false;
		if ($this->tipoOperacion == 'CD') {
			$parametro = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::deudoresPorVentas);
			$return = $parametro->idImputacion;
		} elseif ($this->tipoOperacion == 'OI') {
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
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getClienteTodos($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
	protected function getDocumentoAplicacion() {
		if (!isset($this->_documentoAplicacion)){
			$this->_documentoAplicacion = Factory::getInstance()->getDocumentoAplicacionHaber($this->empresa, 1, 'REC', $this->numero, 'R');
		}
		return $this->_documentoAplicacion;
	}
	protected function setDocumentoAplicacion($documentoAplicacion) {
		$this->_documentoAplicacion = $documentoAplicacion;
		return $this;
	}
	protected function getEcommerceOrder() {
		if (!isset($this->_ecommerceOrder)){
			$this->_ecommerceOrder = Factory::getInstance()->getEcommerce_Order($this->idEcommerceOrder);
		}
		return $this->_ecommerceOrder;
	}
	protected function setEcommerceOrder($ecommerceOrder) {
		$this->_ecommerceOrder = $ecommerceOrder;
		return $this;
	}
	protected function getHaciaDesdeTransferenciaBancariaOperacion() {
		return $this->cliente->getIdNombre();
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
	protected function getLetra() {
		if (!isset($this->_letra)){
			$this->_letra = 'R';
		}
		return $this->_letra;
	}
}

?>