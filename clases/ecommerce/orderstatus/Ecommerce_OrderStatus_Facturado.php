<?php

class Ecommerce_OrderStatus_Facturado extends Ecommerce_OrderStatus {
	const	STATUS_ID = 6;
	const	DOCUMENT_RELATIVE_PATH = 'comercial/facturas/reimpresion/';

	public function procesarEsteStatus(Ecommerce_Order &$order) {
		$order->remito->facturar();
		//$order->pedido->despacho->remito->facturar();
	}

	public function desprocesarEsteStatus(Ecommerce_Order &$order) {
		$order->factura->borrar();
		//$order->pedido->despacho->remito->factura->borrar();
	}

	/*
	public function getDocumentLinkObject(Ecommerce_Order $order) {
		if ($order->tieneDependenciaCumplida(self::STATUS_ID)) {
			$path = $this->getDocumentBaseUrl() . self::DOCUMENT_RELATIVE_PATH . self::DOCUMENT_FILE_NAME . '?';
			$path .= 'numero=' . $order->factura->numero . '&empresa=' . $order->factura->empresa . '&puntoDeVenta=' . $order->factura->puntoDeVenta . '&letra=' . $order->factura->letra;
			$documentLinkObject = new Ecommerce_Document(array(
				'docid' => $order->factura->numeroComprobante,
				'doctype' => 'Factura',
				'docnum' => $order->factura->numeroComprobante,
				'url' => $path
			));
			return $documentLinkObject;
		}
		return false;

	}
	*/

	//GETS y SETS
}

?>