<?php

class Ecommerce_OrderStatus_FacturadoCae extends Ecommerce_OrderStatus {
	const	STATUS_ID = 7;
	const	DOCUMENT_RELATIVE_PATH = 'comercial/facturas/reimpresion/';

	public function procesarEsteStatus(Ecommerce_Order &$order) {
		if ($order->factura->empresa != 2) {
			$order->factura->obtenerCae();
		}
	}

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

	//GETS y SETS
}

?>