<?php

class Ecommerce_OrderStatus_Cobrado extends Ecommerce_OrderStatus {
	const	STATUS_ID = 1;
	const	DOCUMENT_RELATIVE_PATH = 'administracion/cobranzas/reimpresion_recibos/';

	public function procesarEsteStatus(Ecommerce_Order &$order) {
		if ($order->correspondeRecibo()) { //Si el total es 0, no se puede hacer recibo
			$datos = array(
				'tipoRecibo' => 'CD',
				'idCliente' => Ecommerce_Configuration::ECOMMERCE_ID_CLIENTE,
				'idImputacion' => Ecommerce_Configuration::ECOMMERCE_ID_IMPUTACION,
				'recibidoDe' => $order->customer->fullname(),
				'fechaDocumento' => $order->fechaPedido,
				'observaciones' => 'Recibo por ECOMMERCE - Nº ORDER: ' . $order->idEcommerce . ' - De: ' . $order->customer->fullname(),
				'idCaja_E' => Ecommerce_Configuration::ECOMMERCE_ID_CAJA_COBRANZA,
				'usuario' => Usuario::logueado()
			);
			$importes = array(
				'E' => array(
					array(
						'importe' => $order->grandTotal
					)
				)
			);

			$rec = Factory::getInstance()->getRecibo();
			$rec->empresa = !isset($order->customer->usergroup->empresa) ? 1 : $order->customer->usergroup->empresa;
			$rec->datosSinValidar = $datos;
			$rec->importesSinValidar['E'] = $importes;
			$rec->ecommerceOrder = $order;
			$rec->guardar();
		}
	}

	public function getDocumentLinkObject(Ecommerce_Order $order) {
		if ($order->correspondeRecibo() && $order->tieneDependenciaCumplida(self::STATUS_ID)) { //Si el total es 0, no hay recibo
			$path = $this->getDocumentBaseUrl() . self::DOCUMENT_RELATIVE_PATH . self::DOCUMENT_FILE_NAME . '?';
			$path .= 'numero=' . $order->recibo->numero . '&empresa=' . $order->recibo->empresa;
			$documentLinkObject = new Ecommerce_Document(array(
				'docid' => $order->recibo->numero,
				'doctype' => 'Recibo',
				'docnum' => $order->recibo->numero,
				'url' => $path
			));
			return $documentLinkObject;
		}
		return false;
	}

	//GETS y SETS
}

?>