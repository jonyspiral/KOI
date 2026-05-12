<?php

/**
 * @property Cliente			$cliente
 * @property Sucursal			$sucursal
 * @property Usuario			$usuario
 * @property DespachoItem[]		$detalle
 * @property int				$cantidadArticulos
 * @property Ecommerce_Order	$ecommerceOrder
 * @property Remito				$remito
 */

class Despacho extends Base {
	const		_primaryKey = '["numero"]';
	const		CANT_MAX_DETALLE = 99; //Está al pedo este número

	public		$numero;
	public		$empresa;
	public		$anulado;
	public		$idCliente;
	protected	$_cliente;
	public		$idSucursal;
	protected	$_sucursal;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	protected	$_detalle;
	public		$cantidad;				//Número total de items
	protected	$_cantidadArticulos;
	public		$idEcommerceOrder;
	protected	$_ecommerceOrder;
	protected	$_remito;
	public		$pendiente;
	public		$observaciones;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function getCantidadPares() {
		$pares = 0;
		foreach($this->getDetalle() as $item)
			$pares += $item->cantidadTotal;
		return $pares;
	}

	public function addItem(DespachoItem $item) {
		$this->getDetalle(); //En caso de nuevo, esto me va a traer un array vacío
		$this->_detalle[] = $item;
	}

	public static function despachar($datos, $funcionalidad = false) {
		/*
			Esta función se encargará de generar un despacho de uno o más predespachos.
			$datos será un array de distintos predespachos con las cantidades a despachar. Ejemplo:
				array(
					'empresa' => 1,
					'idCliente' => 33,
					'idSucursal' => 1,
					'observaciones' => 'caca culo',
					'predespachos' => array(
						array(
							'pedidoNumero' => 33,
							'pedidoNumeroDeItem' => 1,
							'cant' => array(
								'1' => 3,
								'2' => 0,
								'...' => ...,
								'10' => 4,
							)
						)
					),
				)
		*/

		$despacho = Factory::getInstance()->getDespacho();
		$despacho->empresa = $datos['empresa'];
		$despacho->sucursal = Factory::getInstance()->getSucursal($datos['idCliente'], $datos['idSucursal']);
		$despacho->cliente = $despacho->sucursal->cliente;
		$despacho->ecommerceOrder = Factory::getInstance()->getEcommerce_Order($datos['idEcommerceOrder']);
		$despacho->observaciones = $datos['observaciones'];

		$nroItem = 1;
		$arrPredespachos = array();
		foreach ($datos['predespachos'] as $predesp) {
			$predespacho = Factory::getInstance()->getPredespacho($predesp['pedidoNumero'], $predesp['pedidoNumeroDeItem']);
			//Acá en realidad hay q llenar un array de DespachoItem
			$despachoItem = Factory::getInstance()->getDespachoItem();
			$despachoItem->numeroDeItem = $nroItem;
			$despachoItem->empresa = $despacho->empresa;
			$despachoItem->almacen = $predespacho->almacen;
			$despachoItem->articulo = $predespacho->articulo;
			$despachoItem->colorPorArticulo = $predespacho->colorPorArticulo;
			$despachoItem->pedido = $predespacho->pedido;
			$despachoItem->pedidoItem = $predespacho->pedidoItem;
			$despachoItem->precioAlFacturar = $predespacho->pedido->precioAlFacturar;
			$despachoItem->descuentoPedido = $predespacho->pedido->descuento;
			$despachoItem->recargoPedido = $predespacho->pedido->recargo;
			$despachoItem->cliente = $despacho->cliente; //Hago esto para poder pedir el porcentaje de IVA
			$despachoItem->sucursal = $despacho->sucursal;
			$despachoItem->ivaPorcentaje = $despachoItem->getPorcentajeIva();
			$despachoItem->precioUnitario = $predespacho->pedidoItem->precioUnitario;
			$despachoItem->precioUnitarioFinal = $despachoItem->calcularPrecioUnitarioFinal();
			for ($z = 1; $z <= 10; $z++) {
				//Calculo cuánto puedo despachar según lo predespachado
				$propuesto = Funciones::toInt($predesp['cant'][$z]);
				$predespachado = $predespacho->predespachados[$z];
				$despachoItem->cantidad[$z] = (($propuesto <= $predespachado && $propuesto >= 0) ? $propuesto : ($propuesto >= $predespachado ? $predespachado : 0));
				$predespacho->predespachados[$z] -= $despachoItem->cantidad[$z];
			}
			$despacho->addItem($despachoItem);
			$arrPredespachos[] = $predespacho;
			$nroItem++;
		}
		$despacho->cantidad = $despacho->getCantidadPares();
		$despacho->pendiente = $despacho->cantidad;
		if ($despacho->cantidad <= 0) {
			throw new FactoryExceptionCustomException('No se puede generar un despacho con CERO pares');
		}
		if ($despacho->cantidadArticulos > Despacho::CANT_MAX_DETALLE) {
			throw new FactoryExceptionCustomException('No se puede generar el despacho ya que tiene más de ' . Despacho::CANT_MAX_DETALLE . ' artículos');
		}

		try {
			Factory::getInstance()->beginTransaction();
			$despacho->guardar()->notificar($funcionalidad);
			foreach($arrPredespachos as $predesp) {
				/** @var Predespacho $predesp */
				$predesp->guardar();
			}
			Factory::getInstance()->commitTransaction();

			return $despacho;
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}
	}

	public function remitir() {
		$datos = array(
			'empresa' => $this->empresa,
			'idCliente' => $this->cliente->id,
			'idSucursal' => $this->sucursal->id,
			'idEcommerceOrder' => $this->ecommerceOrder->id,
			'observaciones' => $this->observaciones,
			'detalles' => array()
		);
		foreach ($this->detalle as $di) {
			$datos['detalles'][] = array(
				'despachoNumero' => $di->despachoNumero,
				'numeroDeItem' => $di->numeroDeItem
			);
		}

		return Remito::remitir($datos);
	}

	public function facturar() {
		$remito = $this->remitir();
		return $remito->facturar();
	}

	protected function validarGuardar() {
		$this->cliente->comprobarHabilitadoDespachar();
	}

	//GETS y SETS
	protected function getCantidadArticulos() {
		return count($this->getDetalle());
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
		if (!isset($this->_detalle) && isset($this->numero)){
			$this->_detalle = Factory::getInstance()->getListObject('DespachoItem', 'nro_despacho = ' . Datos::objectToDB($this->numero) . ' AND anulado = \'N\' ');
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
	protected function getRemito() {
		if (!isset($this->_remito)) {
			$anterior = 0;
			foreach ($this->detalle as $despachoItem) {
				if ($despachoItem->remitoNumero != $anterior) {
					throw new FactoryExceptionCustomException('El despacho tiene múltiples remitos');
				}
			}
			$this->_remito = $this->detalle[0]->remito;
		}
		return $this->_remito;
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