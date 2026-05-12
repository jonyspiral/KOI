<?php

class ExceptionRemitoExcedeArticulos extends Exception {
	public function __construct($msg = 'La cantidad de artículo del remito excede el límite permitido'){
		parent::__construct($msg);
	}
}

class ExceptionRemitoObservacionObligatoria extends Exception {
	public function __construct($msg = 'Deberá ingresar una observación para el remito'){
		parent::__construct($msg);
	}
}

/**
 * @property array				$detalle
 * @property Cliente			$cliente
 * @property Sucursal			$sucursal
 * @property Usuario			$usuario
 * @property int				$cantidadArticulos
 * @property int				$cantidadPares
 * @property FormularioRemito	$formulario
 * @property Ecommerce_Order	$ecommerceOrder
 * @property Factura			$factura
 */

class Remito extends Base implements OperacionStock {
	const		_primaryKey = '["empresa", "numero", "letra"]';
	const		CANT_MAX_DETALLE = 14;

	public		$empresa;
	public		$numero;
	public		$letra;
	public		$anulado;
	protected	$_detalle;
	public		$idCliente;
	protected	$_cliente;
	public		$idSucursal;
	protected	$_sucursal;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$importe;
	protected	$_cantidadArticulos;
	public		$cantidadBultos;
	protected	$_cantidadPares;
	public		$facturaPuntoDeVenta;
	public		$facturaTipoDocumento;	//Enum TiposDocumento
	public		$facturaNumero;
	public		$facturaLetra;
	public		$idEcommerceOrder;
	protected	$_ecommerceOrder;
	protected	$_factura;
	public		$formulario;
	public		$observaciones;
	public		$fecha;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function guardar() {
		try {
			Factory::getInstance()->beginTransaction();

			parent::guardar(); //Lo hago primero por el ID (para el $keyObjeto)
			$this->stock();

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

			parent::borrar();
			$this->stock();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	public static function remitir($datos, $funcionalidad = false) {
		/*
			Esta función se encargará de generar un remito de uno o más items de despacho.
			$datos será un array de distintos DespachoItem a remitir (los DespachoItem se remiten ENTEROS). Ejemplo:
				array(
					'empresa' => 1,
					'idCliente' => 33,
					'idSucursal' => 1,
					'observaciones' => 'caca culo',
					'bultos' => 0,
					'detalles' => array(
						array(
							'despachoNumero' => 33,
							'numeroDeItem' => 1
						)
					),
				)
		*/

		$remito = Factory::getInstance()->getRemito();

		$remito->empresa = $datos['empresa'];
		$remito->sucursal = Factory::getInstance()->getSucursal($datos['idCliente'], $datos['idSucursal']);
		$remito->cliente = $remito->sucursal->cliente;
		$remito->ecommerceOrder = Factory::getInstance()->getEcommerce_Order($datos['idEcommerceOrder']);
		$remito->observaciones = $datos['observaciones'];
		$remito->cantidadBultos = $datos['bultos'] ? $datos['bultos'] : 0;
		$remito->letra = $remito->getLetra();

		$arrDetalle = array();
		foreach ($datos['detalles'] as $di) {
			$arrDetalle[] = Factory::getInstance()->getDespachoItem($di['despachoNumero'], $di['numeroDeItem']);
		}
		$remito->detalle = $arrDetalle;

		$remito->importe = $remito->getImporteSinDescuentoRecargo();

		if ($remito->getCantidadPares() <= 0) {
			throw new FactoryExceptionCustomException('No se puede generar un remito con CERO pares');
		}

		$remito->guardar()->notificar($funcionalidad);

		return $remito;
	}

	public function facturar() {
		$observaciones = (($this->cliente->id == 291 || $this->cliente->id == 589) ? $this->observaciones : ''); //Hardcodeo a clientes varios por la observación obligatoria
		$datos = array(
			'empresa' => $this->empresa,
			'idCliente' => $this->cliente->id,
			'idEcommerceOrder' => $this->ecommerceOrder->id,
			'observaciones' => $observaciones,
			'remitos' => array(
				array(
					'numero' => $this->numero,
					'letra' => $this->letra
				)
			)
		);

		return Factura::facturar($datos);
	}

	protected function validarGuardar() {
		$this->cliente->comprobarHabilitadoRemitir();

		foreach ($this->detalle as $item) {
			/** @var DespachoItem $item */

			if ($item->empresa != $this->empresa) {
				throw new FactoryExceptionCustomException('Alguno de los items seleccionados no corresponde a la empresa con la que está trabajando. Por favor recargue la página e inténtelo nuevamente');
			}

			if ($item->cliente->id != $this->cliente->id) {
				throw new FactoryExceptionCustomException('Alguno de los items seleccionados no corresponde al cliente del remito. Por favor recargue la página e inténtelo nuevamente');
			}

			if ($item->sucursal->id != $this->sucursal->id) {
				throw new FactoryExceptionCustomException('Alguno de los items seleccionados no corresponde a la sucursal del remito. Por favor recargue la página e inténtelo nuevamente');
			}

			if ($this->cantidadArticulos > self::CANT_MAX_DETALLE) {
				throw new ExceptionRemitoExcedeArticulos();
			}

			if (($this->cliente->id == 291 || $this->cliente->id == 589) && (!$this->observaciones)) {
				throw new ExceptionRemitoObservacionObligatoria();
			}
		}
	}

	private function comprobaciones() {
		if ($this->getCantidadArticulos() > self::CANT_MAX_DETALLE)
			throw new FactoryExceptionCustomException('No se puede generar el remito porque tiene un detalle de más de ' . self::CANT_MAX_DETALLE . ' artículos');
	}

	public function abrir() {
		$this->comprobaciones();
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
	}

	public function crear() {
		$this->comprobaciones();
		$this->crearFormulario();
		$this->llenarFormulario();
		return $this->formulario->crear();
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioRemito();
	}

	private function llenarFormulario() {
		if (!isset($this->formulario))
			$this->formulario = new FormularioRemito();
		//Lleno todas las variables del formulario
		$this->formulario->empresa = $this->empresa;
		$this->formulario->numero = $this->numero;
		$this->formulario->fecha = $this->fecha;
		$this->formulario->nombreCliente = $this->cliente->razonSocial;
		$this->formulario->idCliente = $this->cliente->id;
		$sucursal = $this->cliente->idSucursalEntrega ? $this->cliente->sucursalEntrega : $this->sucursal->sucursalEntrega;
		$this->formulario->direccion = $sucursal->direccionCalle . ' '. $sucursal->direccionNumero . ' - ' . $sucursal->direccionLocalidad->nombre . ' - ' . $sucursal->direccionProvincia->nombre;
		$this->formulario->cuit = $this->cliente->cuit;
		$this->formulario->idCondicionIva = $this->cliente->idCondicionIva;
		$this->formulario->valorDeclarado = $this->importe;
		$this->formulario->cantidadPares = $this->cantidadPares;
		$this->formulario->cantidadBultos = $this->cantidadBultos;
		$this->formulario->transportistaNombre = $this->sucursal->transporte->nombre;
		$this->formulario->transportistaDomicilio = $this->sucursal->transporte->armarDireccion();
		//Mando a armar el detalle
		$this->formulario->detalle = $this->armoDetalleParaFormulario();
		$this->formulario->horarioEntrega1 = $this->sucursal->horarioEntrega1;
		$this->formulario->horarioEntrega2 = $this->sucursal->horarioEntrega2;
	}

	private function armoDetalleParaFormulario() {
		//Array de objetos [{codArt: 350, nombreArt: 'Avril Woman', codColor: 'V', 'nombreColor': 'verde', cantidad: 2}, {.}]
		$arr = array();
		foreach ($this->getDetalle() as $despachoItem) {
			$codAlm = $despachoItem->idAlmacen;
			$codArt = $despachoItem->idArticulo;
			$codColor = $despachoItem->idColorPorArticulo;
			if (isset($arr[$codAlm][$codArt][$codColor])) {
				$arr[$codAlm][$codArt][$codColor]['cantidad'] += $despachoItem->cantidadTotal;
			} else {
				$o = array(
					'codAlm' => $despachoItem->almacen->id,
					'codArt' => $despachoItem->articulo->id,
					'nombreArt' => $despachoItem->articulo->nombre,
					'codColor' => $despachoItem->colorPorArticulo->id,
					'nombreColor' => $despachoItem->colorPorArticulo->nombre,
					'cantidad' => $despachoItem->cantidadTotal
				);
				$arr[$codAlm][$codArt][$codColor] = $o;
			}
		}
		return $arr;
	}

	public function getImporte() {
		$importe = 0.00;
		foreach($this->getDetalle() as $item)
			$importe += Funciones::toFloat($item->cantidadTotal * $item->precioUnitarioFinal);
		return $importe;
	}

	public function getImporteSinDescuentoRecargo() {
		$importe = 0.00;
		foreach($this->getDetalle() as $item)
			$importe += Funciones::toFloat($item->cantidadTotal * $item->precioUnitario);
		return $importe;
	}

	public function getLetra() {
		return ($this->empresa == 1 ? 'R' : 'X');
	}

	public function facturado() {
		return !is_null($this->facturaNumero);
	}

	/************************************** STOCK **************************************/

	public function stock() {
		return Stock::registrarOperacion($this);
	}

	public function stockTipoMovimiento() {
		return ($this->modo == Modos::delete) ? TiposMovimientoStock::positivo : TiposMovimientoStock::negativo;
	}

	public function stockTipoOperacion() {
		return TiposOperacionStock::remito;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'REM Nº ' . $this->empresa . '-' . $this->numero . '-' . $this->letra;
	}

	public function stockDetalle() {
		$ret = array();
		foreach ($this->getDetalle() as $item) {
			/** @var DespachoItem $item */
			if (!isset($ret[$item->almacen->id])) {
				$ret[$item->almacen->id] = array();
			}
			if (!isset($ret[$item->almacen->id][$item->articulo->id])) {
				$ret[$item->almacen->id][$item->articulo->id] = array();
			}
			if (!isset($ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id])) {
				$ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id] = $item->cantidad;
			} else {
				for ($i = 1; $i <= 10; $i++) {
					$ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id][$i] += $item->cantidad[$i];
				}
			}
		}
		return $ret;
	}

	/************************************** ***** **************************************/

	//GETS y SETS
	protected function getCantidadArticulos() {
		if (!isset($this->_cantidadArticulos)){
			$auxArr = array();
			foreach ($this->getDetalle() as $item) {
				$auxArr[$item->idAlmacen . '_' . $item->idArticulo . '_' . $item->idColorPorArticulo] = 1;
			}
			$this->_cantidadArticulos = count($auxArr);
		}
		return $this->_cantidadArticulos;
	}
	protected function setCantidadArticulos($cantidadArticulos) {
		$this->_cantidadArticulos = $cantidadArticulos;
		return $this;
	}
	protected function getCantidadPares() {
		$pares = 0;
		foreach($this->getDetalle() as $item) {
			$pares += $item->cantidadTotal;
		}
		return $pares;
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
		if (!isset($this->_detalle)){
			$where = 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND nro_remito = ' . Datos::objectToDB($this->numero) . ' AND letra_remito = ' . Datos::objectToDB($this->letra);
			$this->_detalle = Factory::getInstance()->getListObject('DespachoItem', $where);
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
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
	protected function getFactura() {
		if (!isset($this->_factura)){
			$this->_factura = Factory::getInstance()->getFactura($this->empresa, $this->facturaPuntoDeVenta, $this->facturaTipoDocumento, $this->facturaNumero, $this->facturaLetra);
		}
		return $this->_factura;
	}
	protected function setFactura($factura) {
		$this->_factura = $factura;
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