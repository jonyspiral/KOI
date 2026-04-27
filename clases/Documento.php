<?php

/**
 * @property FacturaElectronica	$fe
 *
 * @property int	 			$numeroComprobante
 * @property Cliente 			$cliente
 * @property Sucursal 			$sucursal
 * @property AsientoContable	asientoContable
 * @property Usuario			$usuario
 * @property int				$importeAplicado
 * @property float				$subtotal
 * @property float				$subtotal2
 * @property float				$importeTotal
 * @property FormaDePago		$formaDePago
 * @property Usuario			$caeObtencionUsuario
 * @property int				$cantidadArticulos
 * @property array				$detalle
 * @property array				$detalleItems
 */

class Documento extends Base implements DocumentoContable {
	const		_primaryKey = '["empresa", "puntoDeVenta", "tipoDocumento", "numero", "letra"]';
	const		CANT_MAX_DETALLE = 0;

	public		$empresa;
	public		$puntoDeVenta;
	public		$tipoDocumento;				//Enum TiposDocumento
	public		$tipoDocumento2;			//Enum TiposDocumento2
	public		$numero;
	public		$letra;
	protected	$_numeroComprobante;		//Si tiene número de comprobante, lo devuelve. Sino devuelve el número de documento 
	public		$idNumeroComprobante;		//Es el número 
	public		$anulado;
	public		$idCliente;
	protected	$_cliente;
	public		$idSucursal;
	protected	$_sucursal;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$tieneDetalle;	//'S' cuando no tiene artículos (el detalle va en documentos_d) y 'N' cuando es con un único detalle
	protected	$_detalle;
	protected	$_detalleItems;
	protected	$_cantidadArticulos;
	public		$formulario;
	public		$importeNeto;
	protected	$_subtotal;
	protected	$_subtotal2;
	public		$importeNoGravado;
	public		$importePendiente;
	protected	$_importeAplicado;			//Total - Pendiente (generarlo SIEMPRE, no usar ISSET)
	public		$descuentoComercialImporte;
	public		$descuentoComercialPorc;
	public		$descuentoDespachoImporte;
	public		$ivaPorcentaje1;
	public		$ivaImporte1;
	public		$ivaPorcentaje2;
	public		$ivaImporte2;
	public		$ivaPorcentaje3;
	public		$ivaImporte3;
	public		$idFormaDePago;
	protected	$_formaDePago;
	public		$diasPromedioPago;
	public		$cae;
	public		$caeFechaVencimiento;
	public		$caeObtencionFecha;
	public		$caeObtencionObservaciones;
	public		$idCaeObtencionUsuario;
	protected	$_caeObtencionUsuario;
	public		$mailEnviado;				//Es un S/N para saber si alguna vez ya se mandó la factura por mail
	public		$idAsientoContable;
	protected	$_asientoContable;
	public		$observaciones;
	public		$fecha;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	//Estos campos e importes son para el formulario y FE
	protected	$_fe;
	protected	$_importeDescuento;
	protected	$_importeIva;
	protected	$_importeNetoGravado;
	protected	$_importeNoGravado;
	protected	$_importeSubtotal;
	protected	$_importeSubtotal2;
	protected	$_importeTotal;			//Se diferencia del otro porque este se vuelve a calcular del detalle

	private function validarSiEstaAplicado(){
		if($this->importePendiente != $this->importeTotal){
			throw new FactoryExceptionCustomException('No puede borrar un documento que ya fue aplicado.');
		}
	}

	/**
	 * @return $this
	 * @throws Exception
	 */
	public function guardar() {
		try {
			Factory::getInstance()->beginTransaction();

			$this->guardarSinCae();

			if ($this->hayQuePedirCae()) {
				$this->obtenerCae();
			}

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	public function guardarSinCae() {
		try {
			Factory::getInstance()->beginTransaction();

			$asiento = $this->contabilidad();
			$this->asientoContable = $asiento;
			parent::guardar();
			Factory::getInstance()->marcarParaModificar($asiento);
			$asiento->nombre .=  Funciones::padLeft($this->numeroComprobante, 8);
			$asiento->guardar();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}
	}

	public function borrar() {
		try {
			Factory::getInstance()->beginTransaction();

			$this->validarSiEstaAplicado();
			parent::borrar();
			$this->contabilidad();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	//Métodos para la impresión de formularios
	public function abrir() {
		//$this->comprobaciones();
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
	}

	public function crear() {
		//$this->comprobaciones();
		$this->crearFormulario();
		$this->llenarFormulario();
		return $this->formulario->crear();
	}

	public function obtenerCae() {
		$success = true;
		if (!isset($this->cae)) {
			$this->comprobacionesCae();

			$this->fe = new FacturaElectronica();
			$this->fe->llenar($this); //Mando a llenar la factura electrónica con los datos del documento

			//throw new Exception('Funcionalidad Deshabilitada por 20 min');

			try {
				//Pido el CAE. Puede tirar Exception (para errores) o FactoryExceptionCustomException (para warnings)
				$this->cae = $this->fe->getCae();
				if ($this->fe->errorCae)
					$success = $this->fe->errorCae;
			} catch (Exception $ex) {
				throw $ex;
			}

			$this->caeObtencionFecha = Funciones::hoy('d/m/Y H:i');
			$this->caeObtencionObservaciones = ($success !== true ? $success : '');
			$this->caeObtencionUsuario = Usuario::logueado();
			$this->caeFechaVencimiento = $this->fe->getVencimientoAutorizacion();
			$this->numeroComprobante = $this->fe->getNumeroComprobante();

			try {
				//Guardo la factura con el nuevo cae, el vencimiento, y el número de comprobante
				//Esto no PUEDE tirar error, sino pierdo el CAE y to_do lo anterior.
				Factory::getInstance()->persistir($this);
			} catch (Exception $ex) {
				$this->sendMailErrorCae($ex->getMessage() . ($success !== true ? ' - ' . $success : ''));
				$success = '¡Atención! ¡No continue! ¡Error GRAVE! Se generó el CAE pero no se pudo guardar. Se envió un email con el detalle. ' . $success;
			}
		}
		return $success;
	}

	public function getLetra() {
		if (!isset($this->_cliente))
			throw new FactoryExceptionCustomException('Antes de calcular la letra debe asignarse el cliente');
		return $this->cliente->condicionIva->letraFactura;
	}

	public function tieneDetalle() {
		return $this->tieneDetalle == 'S';
	}

	protected function llenarFormulario() {
		if (!isset($this->formulario))
			$this->crearFormulario();
		//Lleno todas las variables comunes del formulario
		$this->formulario->empresa = $this->empresa;
		$this->formulario->letra = $this->letra;
		$this->formulario->numero = $this->getNumeroComprobante();
		$this->formulario->tipoDocumento = $this->tipoDocumento;
		$this->formulario->fecha = explode('/', $this->fecha);
		$this->formulario->nombreCliente = $this->cliente->razonSocial;
		$this->formulario->direccion = $this->armoDireccionCliente();
		$this->formulario->nombreCondicionIva = $this->cliente->condicionIva->nombre;
		$this->formulario->cuit = $this->cliente->cuit;
		//$this->formulario->condicionDeVenta = $this->;
		$this->formulario->descuentos = Funciones::toFloat($this->calcularImporteDescuento(), 2);
		$this->formulario->subtotal = Funciones::toFloat($this->calcularImporteSubtotal(), 2);
		$this->formulario->subtotal2 = Funciones::toFloat($this->calcularImporteSubtotal2(), 2);
		$this->formulario->ivaPorc1 = $this->ivaPorcentaje1;
		$this->formulario->ivaImporte1 = Funciones::toFloat($this->ivaImporte1, 2);
		$this->formulario->ivaPorc2 = $this->ivaPorcentaje2;
		$this->formulario->ivaImporte2 = Funciones::toFloat($this->ivaImporte2, 2);
		$this->formulario->ivaPorc3 = $this->ivaPorcentaje3;
		$this->formulario->ivaImporte3 = Funciones::toFloat($this->ivaImporte3, 2);
		$this->formulario->importeTotal = Funciones::toFloat($this->importeTotal, 2);
		$this->formulario->cae = $this->cae;
		$this->formulario->caeVencimiento = $this->caeFechaVencimiento;
		$this->formulario->observaciones = $this->observaciones;
		$this->formulario->detalle = $this->armoDetalleParaFormulario();
	}

	private function armoDireccionCliente() {
		$sf = $this->cliente->sucursalFiscal;
		$dir = $sf->direccionCalle . ' ' . $sf->direccionNumero . ' ';
		if ($sf->direccionPiso) $dir .= 'Piso ' . $sf->direccionPiso . ' ';
		if ($sf->direccionDepartamento) $dir .= 'Dpto ' . $sf->direccionDepartamento . ' ';
		$dir .= '</br>' . $sf->direccionCodigoPostal . ' ' . $sf->direccionLocalidad->nombre . ' - ';
		$dir .= '</br>' . $sf->direccionProvincia->nombre . ' - ' . $sf->direccionPais->nombre;
		return $dir;
	}

	private function armoDetalleParaFormulario() {
		//Array de objetos [{codArt: 350, nombreArt: 'Avril Woman', codColor: 'V', 'nombreColor': 'verde',
		// cantidad: 2, precioUnitario: 310.50, precioTotal: 721.00}, {.}]
		$arr = array();
		$d = $this->tieneDetalle(); //Si tiene, es una FAC/NCR sin artículos, con sólo una linea de detalle. Sino, es normal, con artículos
		foreach ($this->getDetalleItems() as $item) {
			$codAlm = ($d ? $item->numeroDeItem : $item->idAlmacen);
			$codArt = ($d ? 0 : $item->idArticulo);
			$codColor = ($d ? 0 : $item->idColorPorArticulo);
			$precio = ($d ? 0 : Funciones::toString($item->precioFactura));
			if (isset($arr[$codAlm][$codArt][$codColor][$precio])) {
				$arr[$codAlm][$codArt][$codColor][$precio]['cantidad'] += $item->cantidadTotal;
				$arr[$codAlm][$codArt][$codColor][$precio]['precioTotal'] = ($arr[$codAlm][$codArt][$codColor][$precio]['cantidad']) * ($arr[$codAlm][$codArt][$codColor][$precio]['precioUnitario']);
			} else {
				$o = array(
					'codAlm' => $codAlm,
					'codArt' => $codArt,
					'nombreArt' => ($d ? $item->descripcionItem : $item->articulo->nombre),
					'codColor' => $codColor,
					'nombreColor' => ($d ? '' : $item->colorPorArticulo->nombre),
					'cantidad' => $item->cantidadTotal,
					'precioUnitario' => $item->precioFactura,
					'precioTotal' =>  ($item->cantidadTotal) * ($item->precioFactura),
				);
				$arr[$codAlm][$codArt][$codColor][$precio] = $o;
			}
		}
		return $arr;
	}

	protected function llevaIvaDiscriminado() {
		return ($this->empresa == 2 || $this->cliente->condicionIva->tratamiento == 'D');
	}

	protected function calcularImporteDescuento() {
		if (!isset($this->_importeDescuento)) {
			$descuentoDespachos = 0;
			foreach ($this->getDetalleItems() as $item)
				$descuentoDespachos += ($item->cantidadTotal * $item->precioFactura) * (Funciones::toFloat(($item->descuentoPedido - $item->recargoPedido) / 100));
			$multiplicador = ($this->llevaIvaDiscriminado() ? 1 : (1 + Funciones::toFloat($this->cliente->condicionIva->porcentajes[1] / 100)));
			$descuentoComercial = Funciones::toFloat($this->descuentoComercialImporte * $multiplicador);
			$this->_importeDescuento = $descuentoDespachos + $descuentoComercial;
		}
		return $this->_importeDescuento;
	}

	protected function calcularImporteIva() {
		if (!isset($this->_importeIva)) {
			$this->_importeIva = Funciones::toFloat($this->ivaImporte1) + Funciones::toFloat($this->ivaImporte2) + Funciones::toFloat($this->ivaImporte3);
		}
		return $this->_importeIva;
	}

	protected function calcularImporteNetoGravado() {
		//Importe total de conceptos GRAVADOS por el IVA
		if (!isset($this->_importeNetoGravado)) {
			$this->_importeNetoGravado = $this->importeNeto - $this->importeNoGravado;
		}
		return $this->_importeNetoGravado;
	}

	protected function calcularImporteNoGravado() {
		//Importe total de conceptos NO GRAVADOS por el IVA
		if (!isset($this->_importeNoGravado)) {
			$this->_importeNoGravado = $this->importeNoGravado;
		}
		return $this->_importeNoGravado;
	}

	private function calcularImporteSubtotal() {
		if (!isset($this->_importeSubtotal)) {
			$this->_importeSubtotal = 0;
			foreach ($this->getDetalleItems() as $item)
				$this->_importeSubtotal += Funciones::toFloat(($item->precioFactura * $item->cantidadTotal));
		}
		return $this->_importeSubtotal;
	}

	private function calcularImporteSubtotal2() {
		if (!isset($this->_importeSubtotal2)) {
			$this->_importeSubtotal2 = $this->calcularImporteSubtotal() - $this->calcularImporteDescuento();
		}
		return $this->_importeSubtotal2;
	}

	protected function calcularImporteTotal() {
		if (!isset($this->_importeTotal)) {
			$this->_importeTotal = $this->calcularImporteNetoGravado() + $this->calcularImporteNoGravado() + $this->calcularImporteIva() - $this->calcularImporteDescuento();
		}
		return $this->_importeTotal;
	}

	public function comprobacionConfirmacionCae() {
		foreach ($this->detalleItems as $item) {
			if ($item->importeTotal <= 0) {
				return 'El documento tiene detalles con importes menores o iguales a 0. ¿Desea obtener el CAE de todos modos?';
			}
		}

		return false;
	}

	protected function hayQuePedirCae() {
		if ($this->letra == 'E' || $this->empresa != 1) {
			return false;
		}
		return true;
	}

	private function comprobacionesCae() {
		$errorBase = 'No se puede obtener el CAE ';
		if ($this->letra == 'E')
			throw new FactoryExceptionCustomException($errorBase . 'porque las facturas "E" son manuales');
		if ($this->empresa != 1)
			throw new FactoryExceptionCustomException($errorBase . 'en la empresa 2');
		if ($this->cliente->habilitadoCae != 'S')
			throw new FactoryExceptionCustomException($errorBase . 'porque el cliente no está habilitado para obtener CAE');
		if (isset($this->cliente->cuit) && !Funciones::validarCuit($this->cliente->cuit))
			throw new FactoryExceptionCustomException($errorBase . 'porque al cliente le falta el CUIT o no es válido');

		/* Por ahora todos los clientes deben tener CUIT
		if (!$this->llevaIvaDiscriminado() && !Funciones::validarDni($this->cliente->dni))
			throw new FactoryExceptionCustomException($errorBase . 'porque al cliente le falta el DNI o no es válido');
		*/
	}

	private function sendMailErrorCae($exMessage) {
		//Mando mail informando el error. ES IMPORTANTE NO PERDER EL CAE!!!
		$asunto = 'Error al persistir documento con CAE';
		$cuerpo = 'Ocurrió un error al intentar persistir un documento una vez obtenido el CAE. ';
		$cuerpo .= 'Se envió el documento "' . $this->tipoDocumento . '" Nº ' . $this->numero . ', letra "' . $this->letra . '", ';
		$cuerpo .= 'del cliente ' . $this->cliente->razonSocial . ' (' . $this->idCliente . '). ';
		$cuerpo .= 'El CAE obtenido fue ' . $this->cae . '.';
		$cuerpo .= 'La fecha de vencimiento obtenida fue ' . $this->caeFechaVencimiento . '.';
		$cuerpo .= 'El número de comprobante obtenido fue ' . $this->numeroComprobante . '.';
		$cuerpo .= 'El error al persistir fue: ' . $exMessage . '.';
		$para = array('alejandro@spiralshoes.com', 'gg@spiralshoes.com');
		Email::enviar(
			 array(
				 'para' => $para,
				 'asunto' => $asunto,
				 'contenido' => $cuerpo
			)
		);
	}

	/************************************** CONTABILIDAD **************************************/

	public function contabilidad() {
		return Contabilidad::contabilizarDocumento($this);
	}

	public function contabilidadEmpresa() {
		return $this->empresa;
	}

	public function contabilidadNombre() {
		$return = '[' . $this->tipoDocumento . '] Cliente: ' . $this->cliente->razonSocial . ' | ' . Funciones::padLeft($this->puntoDeVenta, 4) . '-';
		return $return;
	}

	public function contabilidadFecha() {
		$fecha = ($this->fecha ? $this->fecha : Funciones::hoy());
		return $fecha;
	}

	public function contabilidadDetalle() {
		$det = array();

		/* AGREGO LAS FILAS DE LOS IMPORTES */
		$fecha = ($this->fecha ? $this->fecha : Funciones::hoy());

		$i = 1;

		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $fecha;
		$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::deudoresPorVentas)->idImputacion;
		$fila['importeDebe'] = Funciones::toFloat($this->importeTotal, 2);
		$fila['importeHaber'] = 0;
		$fila['observaciones'] = '';
		$det[] = $fila;
		$i++;

		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $fecha;
		$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ivaDebitoFiscal)->idImputacion;
		$fila['importeDebe'] = 0;
		$fila['importeHaber'] = Funciones::toFloat($this->ivaImporte1, 2) + Funciones::toFloat($this->ivaImporte2, 2) + Funciones::toFloat($this->ivaImporte3, 2);
		$fila['observaciones'] = '';
		$det[] = $fila;
		$i++;

		if ($this->tieneDetalle()) {
			foreach ($this->detalleItems as $item) {
				/** @var DocumentoItem $item */
				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = $item->imputacion->id;
				$fila['importeDebe'] = 0;
				$fila['importeHaber'] = Funciones::toFloat($item->importeTotal, 2);
				$fila['observaciones'] = '';
				$det[] = $fila;
				$i++;
			}
		} else {
			$fila = array();
			$fila['numeroFila'] = $i;
			$fila['fechaVencimiento'] = $fecha;
			$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->idImputacion;
			$fila['importeDebe'] = 0;
			$fila['importeHaber'] = Funciones::toFloat($this->importeNeto, 2);
			$fila['observaciones'] = '';
			$det[] = $fila;
			$i++;
		}

		if ($this->descuentoComercialImporte || $this->descuentoDespachoImporte) {
			$dosDescuentos = (Funciones::toFloat($this->descuentoComercialImporte) >= 0 && Funciones::toFloat($this->descuentoDespachoImporte) >= 0);
			$dosRecargos = (Funciones::toFloat($this->descuentoComercialImporte) <= 0 && Funciones::toFloat($this->descuentoDespachoImporte) <= 0);
			if (!$dosDescuentos && !$dosRecargos) {
				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::descuentosComerciales)->idImputacion;
				$fila['importeDebe'] = Funciones::toFloat((Funciones::toFloat($this->descuentoComercialImporte) > 0) ? $this->descuentoComercialImporte : $this->descuentoDespachoImporte, 2);
				$fila['importeHaber'] = 0;
				$fila['observaciones'] = '';
				$det[] = $fila;
				$i++;

				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::recargosComerciales)->idImputacion;
				$fila['importeDebe'] = 0;
				$fila['importeHaber'] = Funciones::toFloat((Funciones::toFloat($this->descuentoComercialImporte) < 0) ? $this->descuentoComercialImporte : $this->descuentoDespachoImporte, 2);
				$fila['observaciones'] = '';
				$det[] = $fila;
			} else {
				$imp = Funciones::toFloat(abs($this->descuentoComercialImporte) + abs($this->descuentoDespachoImporte), 2);
				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(($dosDescuentos ? ParametrosContabilidad::descuentosComerciales : ParametrosContabilidad::recargosComerciales))->idImputacion;
				$fila['importeDebe'] = $dosDescuentos ? $imp : 0;
				$fila['importeHaber'] = $dosRecargos ? $imp : 0;
				$fila['observaciones'] = '';
				$det[] = $fila;
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
	protected function getCaeObtencionUsuario() {
		if (!isset($this->_caeObtencionUsuario)){
			$this->_caeObtencionUsuario = Factory::getInstance()->getUsuario($this->idCaeObtencionUsuario);
		}
		return $this->_caeObtencionUsuario;
	}
	protected function setCaeObtencionUsuario($caeObtencionUsuario) {
		$this->_caeObtencionUsuario = $caeObtencionUsuario;
		return $this;
	}
	protected function getCantidadArticulos() {
		if (!isset($this->_cantidadArticulos)){
			if ($this->tieneDetalle()) {
				$this->_cantidadArticulos = 1;
			} else {
				$auxArr = array();
				foreach ($this->getDetalleItems() as $item) {
					$auxArr[$item->almacen->id . '_' . $item->articulo->id . '_' . $item->colorPorArticulo->id] = 1;
				}
				$this->_cantidadArticulos = count($auxArr);
			}
		}
		return $this->_cantidadArticulos;
	}
	protected function setCantidadArticulos($cantidadArticulos) {
		$this->_cantidadArticulos = $cantidadArticulos;
		return $this;
	}
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getCliente($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
	protected function getDetalle() {
		//El detalle lo obtiene en la mayoría de los casos de DOCUMENTOS_D (salvo las FAC comunes)
		if (!isset($this->_detalle)){
			$where = 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND punto_venta = ' . Datos::objectToDB($this->puntoDeVenta) . ' AND tipo_docum = ' . Datos::objectToDB($this->tipoDocumento) . ' AND nro_documento = ' . Datos::objectToDB($this->numero) . ' AND letra = ' . Datos::objectToDB($this->letra);
			$this->_detalle = Factory::getInstance()->getListObject('DocumentoItem', $where);
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getDetalleItems() {
		if (!isset($this->_detalleItems)){
			$this->_detalleItems = $this->getDetalle();
		}
		return $this->_detalleItems;
	}
	protected function setDetalleItems($detalleItems) {
		$this->_detalleItems = $detalleItems;
		return $this;
	}
	protected function getFe() {
		if (!isset($this->_fe)){
			$this->_fe = new FacturaElectronica();
		}
		return $this->_fe;
	}
	protected function setFe($fe) {
		$this->_fe = $fe;
		return $this;
	}
	protected function getFormaDePago() {
		if (!isset($this->_formaDePago)){
			$this->_formaDePago = Factory::getInstance()->getFormaDePago($this->idFormaDePago);
		}
		return $this->_formaDePago;
	}
	protected function setFormaDePago($formaDePago) {
		$this->_formaDePago = $formaDePago;
		return $this;
	}
	protected function getImporteAplicado() {
		return Funciones::toFloat($this->importeTotal - $this->importePendiente);
	}
	protected function getImporteTotal() {
		if (!isset($this->_importeTotal)){
			$this->_importeTotal = $this->calcularImporteTotal();
		}
		return $this->_importeTotal;
	}
	protected function setImporteTotal($importeTotal) {
		$this->_importeTotal = $importeTotal;
		return $this;
	}
	protected function getNumeroComprobante() {
		if (!isset($this->_numeroComprobante)){
			$this->_numeroComprobante = (isset($this->idNumeroComprobante) ? $this->idNumeroComprobante : $this->numero);
		}
		return $this->_numeroComprobante;
	}
	protected function setNumeroComprobante($numeroComprobante) {
		$this->_numeroComprobante = $numeroComprobante;
		return $this;
	}
	protected function getSubtotal() {
		if (!isset($this->_subtotal)){
			$this->_subtotal = $this->calcularImporteSubtotal();
		}
		return $this->_subtotal;
	}
	protected function setSubtotal($subtotal) {
		$this->_subtotal = $subtotal;
		return $this;
	}
	protected function getSubtotal2() {
		if (!isset($this->_subtotal2)){
			$this->_subtotal2 = $this->calcularImporteSubtotal2();
		}
		return $this->_subtotal2;
	}
	protected function setSubtotal2($subtotal2) {
		$this->_subtotal2 = $subtotal2;
		return $this;
	}
	protected function getSucursal() {
		if (!isset($this->_sucursal)){
			$this->_sucursal = Factory::getInstance()->getSucursal($this->idCliente, $this->idSucursal);
		}
		return $this->_sucursal;
	}
	protected function setSucursal($sucursal) {
		$this->_sucursal = $sucursal;
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