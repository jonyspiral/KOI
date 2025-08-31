<?php

class Ecommerce_OrderStatus_PendienteDeDevolucion extends Ecommerce_OrderStatus {
	const	STATUS_ID = 11;
	const	DOCUMENT_RELATIVE_PATH = 'comercial/ecommerce/panel_de_control/';
	const	DOCUMENT_FILE_NAME = 'getXls.php';

	public function procesarEsteStatus(Ecommerce_Order &$order) {
		$order->servicioAndreani = Factory::getInstance()->getEcommerce_ServicioAndreani(Ecommerce_ServicioAndreani::RETIRO_EN_CLIENTE);
	}

	//No hago "DESPROCESAR" (no vuelvo atrás lo del servicio Andreani) para poder diferenciar (en la lista de finalizados) aquellos que pasaron ya por una devolución

	public function getDocumentLinkObject(Ecommerce_Order $order) {
		if ($order->tieneDependenciaCumplida(self::STATUS_ID)) {
			$path = $this->getDocumentBaseUrl() . self::DOCUMENT_RELATIVE_PATH . self::DOCUMENT_FILE_NAME . '?';
			$path .= 'orderId=' . $order->id;
			$documentLinkObject = new Ecommerce_Document(array(
				'docid' => $order->id,
				'doctype' => 'CSV Andreani',
				'docnum' => $order->idEcommerce,
				'url' => $path
			));
			return $documentLinkObject;
		}
		return false;

	}

	//GETS y SETS
}

?>