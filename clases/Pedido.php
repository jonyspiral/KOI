<?php

/**
 * @property Cliente			$cliente
 * @property Sucursal			$sucursal
 * @property Usuario			$usuario
 * @property Vendedor			$vendedor
 * @property Almacen			$almacen
 * @property PedidoItem[]		$detalle
 * @property Autorizaciones		$autorizaciones
 * @property string				$estado
 * @property FormaDePago		$formaDePago
 * @property Temporada			$temporada
 * @property int				$paresPendientes
 * @property int				$paresPredespachados
 * @property Ecommerce_Order	$ecommerceOrder
 * @property Despacho			$despacho
 */

class Pedido extends Base {
	const		_primaryKey = '["numero"]';

	public		$empresa;
	public		$numero;
	public		$anulado;
	public		$aprobado;
	public		$idCliente;
	protected	$_cliente;
	public		$idSucursal;
	protected	$_sucursal;
	public		$idUsuario;
	protected	$_usuario;
	public		$idVendedor;
	protected	$_vendedor;
	public		$idAlmacen;
	protected	$_almacen;
	protected	$_detalle;
	protected	$_autorizaciones;
	public		$descuento;
	public		$recargo;
	protected	$_estado;
	public		$precioAlFacturar;	// S/N
	public		$idFormaDePago;
	protected	$_formaDePago;
	public		$idTemporada;
	protected	$_temporada;
	public		$idEcommerceOrder;
	protected	$_ecommerceOrder;
	protected	$_despacho;
	public		$importeTotal;
	protected	$_paresPendientes;
	protected	$_paresPredespachados;
	protected	$_cantidadDePares;
	public		$observaciones;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	public		$formulario;

	public function borrar() {
		foreach ($this->detalle as $item) {
			Factory::getInstance()->marcarParaBorrar($item);
		}

		return parent::borrar();
	}

	public function generarPredespacho() {
		//Creo un registro de predespacho por cada artículo del pedido
		foreach ($this->detalle as $item) {
			try {
				$item->predespacho;
				continue;
			} catch (FactoryExceptionRegistroNoExistente $ex) {
				$predespacho = Factory::getInstance()->getPredespacho();
				$predespacho->empresa = $item->empresa;
				$predespacho->pedido = $item->pedido;
				$predespacho->pedidoItem = $item;
				$predespacho->almacen = $item->almacen;
				$predespacho->articulo = $item->articulo;
				$predespacho->colorPorArticulo = $item->colorPorArticulo;
				for ($i = 1; $i <= 10; $i++) {
					$predespacho->predespachados[$i] = 0;
					$predespacho->tickeados[$i] = 0;
				}
				$predespacho->guardar();
			}
		}
	}

	public function actualizarEstadoPedidoCliente() {
		// Si viene de un PedidoCliente, le actualizamos el estado
        $pedidosCliente = Base::getListObject('PedidoCliente', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_pedido = ' . $this->numero);
        if (count($pedidosCliente) == 1) {
            $pedidoCliente = $pedidosCliente[0];
            /** @var PedidoCliente $pedidoCliente */
            $pedidoCliente->estado = PedidoCliente::ESTADO_EN_CURSO;
            $pedidoCliente->guardar();
		}
	}

	public function predespachar() {
		$mutex = new Mutex('OperacionStock'); //Congelo todas las operaciones de stock para no tener problemas con la concurrencia
		try {
			$stockActual = Stock::getStockMenosAsignado($this->almacen->id);
			Factory::getInstance()->beginTransaction();

			foreach ($this->detalle as $item) {
				$idArticulo = $item->articulo->id;
				$idColor = $item->colorPorArticulo->id;
				foreach ($item->pendiente as $posicion => $cantidadParaAsignar) {
					$stockActual[$idArticulo][$idColor][$posicion] -= ($cantidadParaAsignar);
					if ($stockActual[$idArticulo][$idColor][$posicion] < 0) {
						$art = '[' . $this->almacen->id . '-' . $idArticulo . '-' . $idColor . '] ' . $item->articulo->nombre;
						throw new FactoryExceptionCustomException('No hay stock disponible suficiente del artículo "' . $art . '" para asignar el pedido');
					}
					$item->pendiente[$posicion] -= $cantidadParaAsignar;
					$item->predespacho->predespachados[$posicion] += $cantidadParaAsignar;
				}
				Factory::getInstance()->marcarParaModificar($item);
				$item->guardar();
				$item->predespacho->guardar();
			}

			Factory::getInstance()->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			$mutex->unlock();
			throw $ex;
		}
	}

	public function despredespachar() {
		$mutex = new Mutex('OperacionStock'); //Congelo todas las operaciones de stock para no tener problemas con la concurrencia
		try {
			Factory::getInstance()->beginTransaction();

			foreach ($this->detalle as $item) {
				foreach ($item->predespacho->predespachados as $posicion => $cantidadParaDesasignar) {
					$item->predespacho->predespachados[$posicion] -= $cantidadParaDesasignar;
					$item->pendiente[$posicion] += $cantidadParaDesasignar;
				}
				Factory::getInstance()->marcarParaModificar($item);
				$item->predespacho->guardar();
				$item->guardar();
			}

			Factory::getInstance()->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			$mutex->unlock();
			throw $ex;
		}
	}

	public function despachar() {
		$datos = array(
			'empresa' => $this->empresa,
			'idCliente' => $this->cliente->id,
			'idSucursal' => $this->sucursal->id,
			'idEcommerceOrder' => $this->ecommerceOrder->id,
			'observaciones' => $this->observaciones,
			'predespachos' => array()
		);
		foreach ($this->detalle as $pi) {
			$datos['predespachos'][] = array(
				'pedidoNumero' => $pi->numero,
				'pedidoNumeroDeItem' => $pi->numeroDeItem,
				'cant' => $pi->predespacho->predespachados
			);
		}

		return Despacho::despachar($datos);
	}

	public function esEcommerce() {
		return !is_null($this->idEcommerceOrder);
	}

	// Formulario predespacho
	public function abrirPredespachados() {
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
		$this->formulario = new FormularioPredespacho();
	}

	protected function llenarFormulario() {
		$where = 'nro_pedido = ' . Datos::objectToDB($this->numero);
		$orderBy = ' ORDER BY cod_articulo ASC, cod_color_articulo ASC';

		$predespachos = Factory::getInstance()->getListObject('Predespacho', $where . $orderBy);

		$this->formulario->detalle = $predespachos;
		$this->formulario->esPedido = true;
		$this->formulario->idPedido = $this->numero;
	}
 
	public function calcularTotal() {
		$aux = 0;
		foreach ($this->getDetalle() as $item){
			$aux += Funciones::toFloat($item->precioUnitario) * Funciones::sumaArray($item->cantidad);
		}
		$this->importeTotal = $aux;
		$this->importeTotal -= Funciones::toFloat($aux * ($this->descuento) / 100);
		$this->importeTotal += Funciones::toFloat($aux * ($this->recargo) / 100);
		return $this->importeTotal;
	}

	public function addItem(PedidoItem $item) {
		$this->getDetalle(); //En caso de pedido nuevo, esto me va a traer un array vacío
		$this->_detalle[] = $item;
	}

	//GETS y SETS
	protected function getAlmacen() {
		if (!isset($this->_almacen)){
			$this->_almacen = Factory::getInstance()->getAlmacen($this->idAlmacen);
		}
		return $this->_almacen;
	}
	protected function setAlmacen($almacen) {
		$this->_almacen = $almacen;
		return $this;
	}
	protected function getAutorizaciones() {
		if (!isset($this->_autorizaciones) && isset($this->numero)){
			$this->_autorizaciones = new Autorizaciones(TiposAutorizacion::notaDePedido, $this->numero);
		}
		return $this->_autorizaciones;
	}
	protected function setAutorizaciones($autorizaciones) {
		$this->_autorizaciones = $autorizaciones;
		return $this;
	}
	protected function getCantidadDePares() {
		if (!isset($this->_cantidadDePares)){
			$aux = 0;
			foreach ($this->getDetalle() as $item){
				$aux += Funciones::sumaArray($item->cantidad);
			}
			$this->_cantidadDePares = $aux;
		}
		return $this->_cantidadDePares;
	}
	protected function getCliente() {
		if (!isset($this->_cliente)){
			//Hago clienteTodos porque sino no funciona en el HtmlAutoSuggestBox
			$this->_cliente = Factory::getInstance()->getClienteTodos($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
	protected function getDespacho() {
		if (!isset($this->_despacho)) {
			$despachos = Factory::getInstance()->getListObject('DespachoItem', 'nro_pedido = ' . Datos::objectToDB($this->numero) . ' AND anulado = ' . Datos::objectToDB('N'));
			if (!count($despachos)) {
				throw new FactoryExceptionCustomException('El pedido no tiene ningún despacho');
			}
			$anterior = 0;
			foreach ($despachos as $despacho) {
				/** @var DespachoItem $despacho */
				if ($despacho->despachoNumero != $anterior) {
					throw new FactoryExceptionCustomException('El pedido tiene múltiples despachos');
				}
			}
			$this->_despacho = $despachos[0]->despacho;
		}
		return $this->_despacho;
	}
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->numero)){
			$this->_detalle = Factory::getInstance()->getListObject('PedidoItem', 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND nro_pedido = ' . Datos::objectToDB($this->numero) . ' ');
			//En realidad, en la tabla de detalles el campo ANULADO debería decir siempre 'N'
			//Pero a veces desde hexágono (en DESPACHOS) borran una curva entera de un artículo de un pedido
			//Eso hace que aparezca como ANULADO = 'S' en lugar de borrarlo físicamente
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
	protected function getEstado() {
		if (!isset($this->_estado)){
			$this->_estado = 'En proceso';
			if (count($this->getAutorizaciones()->autorizaciones) == 0)
				$this->_estado = 'Por procesar';
			foreach ($this->getAutorizaciones()->autorizaciones as $aut) {
				if ($aut->autorizado != 'S') {
					$this->_estado = 'No aprobado';
					break;
				}
			}
		}
		return $this->_estado;
	}
	protected function setEstado($estado) {
		$this->_estado = $estado;
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
	protected function getParesPredespachados() {
		if (!isset($this->_paresPredespachados)) {
			$aux = 0;
			foreach ($this->getDetalle() as $item)
				for ($i = 1; $i <= 8; $i++)
					$aux += $item->predespachados[$i];
			$this->_paresPredespachados = $aux;
		}
		return $this->_paresPredespachados;
	}
	protected function getParesPendientes() {
		if (!isset($this->_paresPendientes)) {
			$aux = 0;
			foreach ($this->getDetalle() as $item)
				for ($i = 1; $i <= 8; $i++)
					$aux += $item->pendiente[$i];
			$this->_paresPendientes = $aux;
		}
		return $this->_paresPendientes;
	}
	protected function setParesPendientes($paresPendientes) {
		$this->_paresPendientes = $paresPendientes;
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
	protected function getTemporada() {
		if (!isset($this->_temporada)){
			$this->_temporada = Factory::getInstance()->getTemporada($this->idTemporada);
		}
		return $this->_temporada;
	}
	protected function setTemporada($temporada) {
		$this->_temporada = $temporada;
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
	protected function getVendedor() {
		if (!isset($this->_vendedor)){
			$this->_vendedor = Factory::getInstance()->getVendedor($this->idVendedor);
		}
		return $this->_vendedor;
	}
	protected function setVendedor($vendedor) {
		$this->_vendedor = $vendedor;
		return $this;
	}
}

?>