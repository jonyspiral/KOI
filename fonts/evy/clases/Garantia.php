<?php



/**
 * @property Cliente				$cliente
 * @property Ecommerce_Order		$order
 * @property Motivo					$motivo
 * @property int					$cantidadPares
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 * @property GarantiaItem[]			$detalle
 * @property FormularioGarantia		$formulario
 */

class Garantia extends Base {
	const		_primaryKey = '["id"]';
	const		ALMACEN_INGRESO_GARANTIA = '19';
	const		ALMACEN_CALIDAD = '05'; // Ac? deber? ir el ID de un almac?n sobre el que SEGURO va a tener acceso el usuario de calidad (el que crea las garant?as). Si no tiene acceso, va a romper

	public		$id;
	public		$clasificada;
	public		$solucionNcr;
	public		$totalNcr;
	public		$idCliente;
	protected	$_cliente;
	public		$idOrder;
	protected	$_order;
	protected	$_detalle;
	protected	$_cantidadPares;
	public		$movimientos; // Es un JSON con el detalle de todos los movimientos que hay que hacer despu?s de aprobada
	public		$derivada; // Es nulo o el ID de otra garant?a (de la cual deriva)
	public		$devuelta; // "N" o "S"
	public		$idMotivo;
	protected	$_motivo;
	public		$observaciones;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	public		$formulario;

	public function addItem(GarantiaItem $item) {
		$this->getDetalle(); //En caso de nuevo, esto me va a traer un array vac?o
		$this->_detalle[] = $item;
	}

	public function guardar() {
		try {
			Factory::getInstance()->beginTransaction();

			if ($this->modo == Modos::insert && is_null($this->derivada)) {
				parent::guardar(); //Tengo que hacer el IF antes de guardar porque el guardar cambia el modo a update

				foreach ($this->detalle as $item) {
					$item->stock();
				}
			} else {
				parent::guardar();
			}

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	public function borrar() {
		try {
			Factory::getInstance()->beginTransaction();

			foreach ($this->detalle as $item) {
				Factory::getInstance()->marcarParaBorrar($item);
				$item->stock();
			}
			parent::borrar();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	public function esEcommerce() {
		return isset($this->idOrder);
	}

	protected function validarGuardar() {
		if ($this->cantidadPares <= 0) {
			throw new FactoryExceptionCustomException('No puede hacer una garantía sin pares');
		}

		foreach ($this->detalle as $item) {
			for ($i = 1; $i <= 10; $i++) {
				$item->cantidad[$i] = Funciones::toInt($item->cantidad[$i]);
			}

			if ($item->cantidadTotal <= 0 && $this->modo == Modos::insert) {
				throw new FactoryExceptionCustomException('No puede hacer una garantía con algún item en 0 (cero) pares (todas las columnas de cantidad están en cero)');
			}
		}
	}

	function finalizar($funcionalidad = false, $llevaNcr = false, $cantidadesCuenta1 = array()) {
		try {
			Factory::getInstance()->beginTransaction();

			if ($llevaNcr) {
				$this->generarNcr($cantidadesCuenta1, $ncr1, $ncr2);
				if (!$ncr1 && !$ncr2) {
					throw new FactoryExceptionCustomException('No se pudieron generar las notas de crédito correspondientes. Por favor, recargue la página');
				}

				// Pongo que la garant?a fue aprobada (con NCR) y guardo
				$this->solucionNcr = 'S';
				$this->guardar()->notificar($funcionalidad);

				// Bajo de stock los productos para que luego se den de alta con la NCR en el mismo almac?n
				foreach ($this->detalle as $item) {
					Factory::getInstance()->marcarParaBorrar($item);
					$item->stock();
				}

				// Genero la NCR y los movimientos de stock al almac?n de calidad
				$ncr2 && $ncr2->guardar();
				// Es importante que esto sea LO ?LTIMO que hago en la transacci?n, porque esto involucra AFIP. Si algo DESPU?S de esto fallara, estar?amos subiendo cosas a la AFIP inv?lidas
				// Despu?s de esto se deben hacer cosas (se deben generar los movimientos de stock), pero eso se har? fuera de la transacci?n y si fallan se alertar? al usuario y nada m?s
				$ncr1 && $ncr1->guardar();
			} else {
				if ($this->esEcommerce()) {
					// Si es de Ecommerce la pongo en 'N' porque los movimientos se van a lanzar igual a los almacenes correspondientes
					$this->solucionNcr = 'N';
				} else {
					// Vuelvo la garant?a a su estado anterior para que la puedan clasificar de nuevo
					$this->clasificada = 'N';
					$this->movimientos = null;
				}

				$this->guardar()->notificar($funcionalidad);
			}

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		if ($llevaNcr || $this->esEcommerce()) {
			// Genero los movimientos de stock a los almacenes correspondientes
			$this->lanzarMovimientosDeAlmacen($llevaNcr);
		}
	}

	private function lanzarMovimientosDeAlmacen($llevaNcr) {
		try {
			Factory::getInstance()->beginTransaction();

			foreach ($this->movimientos as $detalle) {
				$movimiento = Factory::getInstance()->getMovimientoAlmacenConfirmacion();
				$movimiento->almacenOrigen = Factory::getInstance()->getAlmacen(self::ALMACEN_CALIDAD); //Factory::getInstance()->getAlmacen($detalle['idAlmacenOrigen']);
				$movimiento->almacenDestino = Factory::getInstance()->getAlmacen($detalle['idAlmacenDestino']);
				$movimiento->articulo = Factory::getInstance()->getArticulo($detalle['idArticulo']);
				$movimiento->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($detalle['idArticulo'], $detalle['idColorPorArticulo']);
				$movimiento->motivo = ($this->idOrder ? 'Por ecommerce Nº ' . $this->order->idEcommerce : 'Del cliente ' . $this->cliente->getIdNombre());
				$movimiento->cantidad = $detalle['cantidad'];
				$movimiento->usuario = $this->usuario;

				$movimiento->guardar();
			}

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex){
			$nombreCliente = ($this->esEcommerce() ? 'de Ecommerce "' . $this->order->customer->fullname() . '"' : '"' . $this->cliente->razonSocial . '"');
			//Mando mail informando el error. Es importante notificar que los pares no pudieron moverse de almac?n y que deber? hacer a mano desde el almac?n de calidad
			$asunto = 'Error al intentar mover pares luego de garantía';
			$cuerpo = 'Ocurrió un error al intentar mover los pares a sus almacenes correspondientes (según clasificó el sector de "Calidad").<br>';
			$cuerpo .= 'El error se produjo luego de aprobarse la garantía Nº ' . $this->id . ' del cliente ' . $nombreCliente . '.<br>';
			$cuerpo .= 'La nota de crédito se generó correctamente.<br>';
			$cuerpo .= 'Deberá revisarse cuál fue el motivo del error y realizar los movimientos de manera manual desde el almacén de "Calidad".<br><br>';
			$cuerpo .= 'El error obtenido fue "' . $ex->getMessage() . '".';
			$para = array('sistemas@spiralshoes.com', 'calidad@spiralshoes.com');
			Email::enviar(
				 array(
					 'para' => $para,
					 'asunto' => $asunto,
					 'contenido' => $cuerpo
				 )
			);
			$msg = 'La garantía ' . ($llevaNcr ? 'y la nota de crédito se generaron' : 'se generó') . ' correctamente, pero ocurrió un error al mover los pares de almacén. ';
			$msg .= 'Se envió un mail a sistemas con más información. Por favor, vuelva a cargar la página para continuar. Info: ' . $ex->getMessage();
			throw new FactoryExceptionCustomException($msg);
		}
	}

	private function generarNcr($cantidadesCuenta1, &$ncr1, &$ncr2) {
		//Si alguna de las dos NCRs no debe se realizada, debe tener el valor FALSE
		$ncr1 = false;
		$ncr2 = false;

		//Cosas que necesito antes de crear las NCRs
		$idCliente = ($this->esEcommerce() ? Ecommerce_Configuration::ECOMMERCE_ID_CLIENTE : $this->idCliente);
		$cliente = Factory::getInstance()->getCliente($idCliente);

		$todoMismaCuenta = ($this->esEcommerce() ? $this->order->customer->usergroup->empresa : false);

		$nroItem1 = 1;
		$nroItem2 = 1;
		$totalCant1 = 0;
		$totalCant2 = 0;
		$totalNcr = array('1' => 0, '2' => 0);
		$items = array('1' => false, '2' => false);
		foreach ($this->detalle as $item) {
			if (count($cantidadesCuenta1) && !isset($cantidadesCuenta1[$item->id])) {
				throw new FactoryExceptionCustomException('Deben distribuirse todos los pares de la garantía');
			}

			$cantidades1 = array();
			$cantidades2 = array();
			for ($j = 1; $j <= 10; $j++) {
				$cantidades1[$j] = 0;
				$cantidades2[$j] = 0;
			}
			$dif = false;
			$cantCuenta1 = $todoMismaCuenta ? ($todoMismaCuenta == 2 ? 0 : $item->cantidadTotal) : $cantidadesCuenta1[$item->id];

			$i = 1;
			while ($i <= 10 && $dif === false) {
				if ($item->cantidad[$i] >= $cantCuenta1) {
					$dif = $item->cantidad[$i] - $cantCuenta1;
					$cantidades1[$i] = $cantCuenta1;
				} else {
					$cantidades1[$i] = $item->cantidad[$i];
					$cantCuenta1 -= $cantidades1[$i];
					$i++;
				}
			}
			while ($i <= 10) {
				if ($dif !== false) {
					$cantidades2[$i] = $dif;
					$dif = false;
				} else {
					$cantidades2[$i] = $item->cantidad[$i];
				}
				$i++;
			}

			$cant1 = Funciones::sumaArray($cantidades1);
			$cant2 = Funciones::sumaArray($cantidades2);
			$pUnit = $item->importeNcr / $item->cantidadTotal;
			$totalNcr['1'] += ($cant1 * $pUnit);
			$totalNcr['2'] += ($cant2 * $pUnit);
			$totalCant1 += $cant1;
			$totalCant2 += $cant2;

			if ($cant1) {
				$documentoItem = Factory::getInstance()->getDocumentoItem();
				$documentoItem->cliente = $cliente; //Sirve para calcular el IVA
				$documentoItem->almacen = Factory::getInstance()->getAlmacen(self::ALMACEN_CALIDAD); //$item->almacen;
				$documentoItem->articulo = $item->articulo;
				$documentoItem->colorPorArticulo = $item->colorPorArticulo;
				$documentoItem->cantidad = $cantidades1;
				$documentoItem->empresa = $this->esEcommerce() ? $this->order->customer->usergroup->empresa : 1;
				$documentoItem->numeroDeItem = $nroItem1;
				$documentoItem->ivaPorcentaje = $documentoItem->getPorcentajeIva();
				$documentoItem->precioUnitario = Funciones::toFloat($item->importeNcr / $item->cantidadTotal);
				$documentoItem->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->imputacion;
				if (!$items['1']) {
					$items['1'] = array();
				}
				$items['1'][] = $documentoItem;
				$nroItem1++;
			}
			if ($cant2) {
				$documentoItem = Factory::getInstance()->getDocumentoItem();
				$documentoItem->cliente = $cliente; //Sirve para calcular el IVA
				$documentoItem->almacen = Factory::getInstance()->getAlmacen(self::ALMACEN_CALIDAD); //$item->almacen;
				$documentoItem->articulo = $item->articulo;
				$documentoItem->colorPorArticulo = $item->colorPorArticulo;
				$documentoItem->cantidad = $cantidades2;
				$documentoItem->empresa = $this->esEcommerce() ? $this->order->customer->usergroup->empresa : 2;
				$documentoItem->numeroDeItem = $nroItem2;
				$documentoItem->ivaPorcentaje = $documentoItem->getPorcentajeIva();
				$documentoItem->precioUnitario = Funciones::toFloat($item->importeNcr / $item->cantidadTotal);
				$documentoItem->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->imputacion;
				if (!$items['2']) {
					$items['2'] = array();
				}
				$items['2'][] = $documentoItem;
				$nroItem2++;
			}
		}

		if ($totalCant1) {
			$ncr1 = $this->armarNcr(1, $items, $cliente, $totalNcr);
		}

		if ($totalCant2) {
			$ncr2 = $this->armarNcr(2, $items, $cliente, $totalNcr);
		}
	}

	private function armarNcr($empresa, $items, $cliente, $totalNcr) {
		$ncr = Factory::getInstance()->getNotaDeCredito();
		$ncr->empresa = $empresa;
		$ncr->cliente = $cliente;
		$ncr->tipoDocumento2 = TiposDocumento2::ncrDevolucion;
		//$ncr->causa = Factory::getInstance()->getCausaNotaDeCredito($idCausa); // Si el d?a de ma?ana se requiere, se puede agregar tirando un popup cuando el de cobranzas aprueba
		$ncr->observaciones = $this->observaciones;
		$ncr->tieneDetalle = 'N';

		$ncr->detalle =  $items[$empresa];

		// Importes y descuentos
		$ncr->importeNoGravado = 0;
		$ncr->importeNeto = Funciones::toFloat($totalNcr[$empresa], 2);;
		$ncr->descuentoComercialPorc = $ncr->cliente->creditoDescuentoEspecial;
		$ncr->descuentoComercialImporte = Funciones::toFloat($ncr->importeNeto * $ncr->descuentoComercialPorc / 100, 2);

		// IVA
		if ($ncr->empresa == 2) {
			$ncr->ivaPorcentaje1 = 0;
			$ncr->ivaPorcentaje2 = 0;
			$ncr->ivaPorcentaje3 = 0;
			$ncr->ivaImporte1 = 0;
			$ncr->ivaImporte2 = 0;
			$ncr->ivaImporte3 = 0;
		} else {
			$iva = array();
			foreach ($ncr->detalle as $item) {
				/** @var DocumentoItem $item */
				if (!isset($iva[Funciones::toString($item->ivaPorcentaje)]))
					$iva[Funciones::toString($item->ivaPorcentaje)] = 0;
				$iva[Funciones::toString($item->ivaPorcentaje)] += Funciones::toFloat($item->precioUnitario * $item->cantidadTotal);
			}
			if (isset($iva['21'])) //Hardcodeo. Es el porcentaje en el cual se aplica el descuento comercial
				$iva['21'] = Funciones::toFloat($iva['21'] - $ncr->descuentoComercialImporte);
			$j = 1;
			foreach ($iva as $porc => $valor) {
				$attr1 = 'ivaPorcentaje' . $j;
				$attr2 = 'ivaImporte' . $j;
				$ncr->$attr1 = Funciones::toFloat($porc);
				$ncr->$attr2 = Funciones::toFloat($valor * (Funciones::toFloat($porc) / 100));
				$j++;
			}
		}

		// A cuenta 2 le pongo IVA 0
		//$ncr->ivaPorcentaje1 = ($ncr->empresa == 2 ? 0 : $ncr->cliente->condicionIva->porcentajes[1]);
		//$ncr->ivaImporte1 = ($ncr->empresa == 2 ? 0 : Funciones::toFloat($ncr->importeNeto * $ncr->ivaPorcentaje1 / 100, 2)); //Lo tengo que hacer despu?s del item porque $ncr->subtotal necesita los items para poder calcularse

		return $ncr;
	}

	public function devolver($sucursal, $observaciones) {

		try {
			Factory::getInstance()->beginTransaction();

			$devolucion = Factory::getInstance()->getDevolucionACliente();
			$devolucion->cliente = $this->cliente;
			$devolucion->sucursal = $sucursal;
			$devolucion->observaciones = $observaciones;
			$detalle = array();
			foreach ($this->detalle as $item) {
				$di = Factory::getInstance()->getDevolucionAClienteItem();
				$di->almacen = $item->almacen;
				$di->articulo = $item->articulo;
				$di->colorPorArticulo = $item->colorPorArticulo;
				$di->cantidad = $item->cantidad;

				$detalle[] = $di;
			}
			$devolucion->detalle = $detalle;

			if ($devolucion->cantidadPares <= 0) {
				throw new FactoryExceptionCustomException('No se puede generar una devolución con CERO pares');
			}

			$devolucion->guardar();

			$this->devuelta = 'S';
			$this->guardar();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $devolucion;
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
		$this->formulario = new FormularioGarantia();
	}

	protected function llenarFormulario() {
		$this->formulario->id = $this->id;
		$this->formulario->cliente = $this->cliente;
		$this->formulario->order = $this->order;
		$this->formulario->fecha = $this->fechaAlta;
		$this->formulario->detalle = $this->movimientos;
		$this->formulario->motivo = $this->motivo->nombre;
		$this->formulario->observaciones = $this->observaciones;
	}

	//GETS y SETS
	public function getCantidadPares() {
		if (!isset($this->_cantidadPares)){
			$this->_cantidadPares = 0;
			foreach ($this->getDetalle() as $item) {
				$this->_cantidadPares += Funciones::sumaArray($item->cantidad);
			}
		}
		return $this->_cantidadPares;
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
		if (!isset($this->_detalle) && isset($this->id)){
			$this->_detalle = Factory::getInstance()->getListObject('GarantiaItem', 'cod_garantia = ' . Datos::objectToDB($this->id) . ' AND cantidad > 0');
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getOrder() {
		if (!isset($this->_order)){
			$this->_order = Factory::getInstance()->getEcommerce_Order($this->idOrder);
		}
		return $this->_order;
	}
	protected function setOrder($order) {
		$this->_order = $order;
		return $this;
	}
	protected function getMotivo() {
		if (!isset($this->_motivo)){
			$this->_motivo = Factory::getInstance()->getMotivo($this->idMotivo);
		}
		return $this->_motivo;
	}
	protected function setMotivo($motivo) {
		$this->_motivo = $motivo;
		return $this;
	}
}

?>