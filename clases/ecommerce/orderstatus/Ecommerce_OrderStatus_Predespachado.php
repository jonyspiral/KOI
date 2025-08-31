<?php

class Ecommerce_OrderStatus_Predespachado extends Ecommerce_OrderStatus {
	const	STATUS_ID = 3;
	const	DOCUMENT_RELATIVE_PATH = 'comercial/predespachos/reimpresion/';

	public function procesarEsteStatus(Ecommerce_Order &$order) {
		$order->pedido->predespachar();
	}

	public function desprocesarEsteStatus(Ecommerce_Order &$order) {
		$order->pedido->despredespachar();
	}

	public function getDocumentLinkObject(Ecommerce_Order $order) {
		if ($order->tieneDependenciaCumplida(self::STATUS_ID)) {
			$path = $this->getDocumentBaseUrl() . self::DOCUMENT_RELATIVE_PATH . self::DOCUMENT_FILE_NAME . '?';
			$path .= 'idPedido=' . $order->pedido->numero . '&tipo=P';
			$documentLinkObject = new Ecommerce_Document(array(
				'docid' => $order->pedido->numero,
				'doctype' => 'Predespacho',
				'docnum' => $order->pedido->numero,
				'url' => $path
			));
			return $documentLinkObject;
		}
		return false;
	}

	//GETS y SETS
}

?>