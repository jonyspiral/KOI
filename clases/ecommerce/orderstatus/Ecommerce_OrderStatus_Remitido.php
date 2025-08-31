<?php

class Ecommerce_OrderStatus_Remitido extends Ecommerce_OrderStatus {
	const	STATUS_ID = 5;
	const	DOCUMENT_RELATIVE_PATH = 'comercial/remitos/reimpresion/';

	public function procesarEsteStatus(Ecommerce_Order &$order) {
		$order->despacho->remitir();
		//$order->pedido->despacho->remitir();
	}

	public function desprocesarEsteStatus(Ecommerce_Order &$order) {
		$order->remito->borrar();
		//$order->pedido->despacho->remito->borrar();
	}

	public function getDocumentLinkObject(Ecommerce_Order $order) {
		if ($order->tieneDependenciaCumplida(self::STATUS_ID)) {
			$path = $this->getDocumentBaseUrl() . self::DOCUMENT_RELATIVE_PATH . self::DOCUMENT_FILE_NAME . '?';
			$path .= 'numero=' . $order->remito->numero . '&empresa=' . $order->remito->empresa;
			$documentLinkObject = new Ecommerce_Document(array(
				'docid' => $order->remito->numero,
				'doctype' => 'Remito',
				'docnum' => $order->remito->numero,
				'url' => $path
			));
			return $documentLinkObject;
		}
		return false;
	}

	//GETS y SETS
}

?>