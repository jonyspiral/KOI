<?php

class Ecommerce_OrderStatus_Despachado extends Ecommerce_OrderStatus {
	const	STATUS_ID = 4;

	public function procesarEsteStatus(Ecommerce_Order &$order) {
		$order->pedido->despachar();
	}

	public function desprocesarEsteStatus(Ecommerce_Order &$order) {
		$order->despacho->borrar();
		//$order->pedido->despacho->borrar();
	}

	//GETS y SETS
}

?>