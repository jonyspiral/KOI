<?php

class Ecommerce_OrderStatus_Pedido extends Ecommerce_OrderStatus {
	const	STATUS_ID = 2;

	public function procesarEsteStatus(Ecommerce_Order &$order) {
		//Genero el pedido y mando a guardar
		$pedido = $this->generarPedido($order);
		$pedido->guardar()->notificar('comercial/ecommerce/panel_de_control/');
		$pedido->generarPredespacho(); //Es necesario porque normalmente se hace en la autorización, pero acá no vamos a tener ese proceso

		$order->pedido = $pedido;
	}

	public function desprocesarEsteStatus(Ecommerce_Order &$order) {
		Logger::addError('Borrar pedido');
		$order->pedido->borrar();
		Logger::addError('Pedido borrado');
		if ($order->correspondeRecibo()) {
			Logger::addError('Ya corresponde recibo');
			$order->recibo->borrar();
			Logger::addError('Recibo borrado');
		}
		Logger::addError('Borrando orden');
		$order->borrar();
		Logger::addError('Orden borrada');
	}

	private function generarPedido(Ecommerce_Order $order) {
		//Lleno el pedido
		$pedido = Factory::getInstance()->getPedido();

		$pedido->empresa = $order->customer->usergroup->empresa;
		$pedido->aprobado = 'S'; //Genero el pedido como aprobado directamente, sin autorización de nadie, y el predespacho lo creo manualmente
		$pedido->cliente = Factory::getInstance()->getCliente(Ecommerce_Configuration::ECOMMERCE_ID_CLIENTE);
		$pedido->sucursal = Factory::getInstance()->getSucursal(Ecommerce_Configuration::ECOMMERCE_ID_CLIENTE, Ecommerce_Configuration::ECOMMERCE_ID_SUCURSAL);
		$pedido->vendedor = $pedido->cliente->vendedor;
		$pedido->almacen = Factory::getInstance()->getAlmacen(Ecommerce_Configuration::ECOMMERCE_ID_ALMACEN);
		$pedido->descuento = $order->calcularDescuentoEnPorcentaje();
		$pedido->precioAlFacturar = 'N';
		//$pedido->importeTotal = $order->grandTotal; Se calcula el total abajo
		$pedido->ecommerceOrder = $order;
		$pedido->observaciones = 'Pedido por ECOMMERCE - Nº ORDER: ' . $order->idEcommerce . ' - De: ' . $order->customer->fullname();

		$nroItem = 1;
		$detalles = array();
		foreach ($order->details as $detail) {
			$detalle = Factory::getInstance()->getPedidoItem();

			$posColor = 3;
			if (is_numeric(substr($detail->reference, 0, $posColor + 1))) {
				$posColor = 4;
			}
			$idArticulo = substr($detail->reference, 0, $posColor);
			$idColor = substr($detail->reference, $posColor);
			$detalle->empresa = $pedido->empresa;
			$detalle->almacen = $pedido->almacen;
			$detalle->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);
			$detalle->articulo = $detalle->colorPorArticulo->articulo;

			$porcentajeIva = ($detalle->empresa == 1 ? $pedido->cliente->condicionIva->porcentajes[$detalle->articulo->rubroIva->columnaIva] : 0);
			$detalle->precioUnitario = Funciones::toFloat($detail->price) / (1 + ($porcentajeIva / 100));

			$cantidad = array();
			foreach ($detalle->articulo->rangoTalle->posicion as $i => $talle) {
				$cantidad[$i] = ($talle == $detail->size ? $detail->quantity : 0);
			}
			$detalle->cantidad = $cantidad;
			$detalle->pendiente = $cantidad;
			if ($detalle->getTotalCantidad() == 0) {
				throw new FactoryExceptionCustomException('No se encontró ninguna coincidencia entre los talles pedidos y los correspondientes al artículo');
			}

			$detalle->numeroDeItem = $nroItem;
			$detalles[] = $detalle;
			$nroItem++;
		}
		$pedido->detalle = $detalles;
		$pedido->calcularTotal();

		return $pedido;
	}

	//GETS y SETS
}

?>