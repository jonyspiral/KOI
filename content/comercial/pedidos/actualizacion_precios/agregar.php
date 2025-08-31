<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/actualizacion_precios/agregar/')) { ?>
<?php

$detalle = Funciones::post('detalle');

try {
	Factory::getInstance()->beginTransaction();

	foreach ($detalle as $idPedido) {
		$pedido = Factory::getInstance()->getPedido($idPedido);
		$nuevoDetallePedido = array();
		$detallesAnulados = array();

		foreach ($pedido->detalle as $pedidoItem) {
			if ($pedidoItem->anulado()) {
				$detallesAnulados[] = $pedidoItem;
			} else {
				if ($pedidoItem->pendiente > 0) {
					$pedidoItem->precioUnitario = $pedidoItem->colorPorArticulo->getPrecioSegunCliente($pedido->cliente);
				}
			}

			Factory::getInstance()->marcarParaInsertar($pedidoItem);
			$nuevoDetallePedido[] = $pedidoItem;
		}

		$pedido->detalle = $nuevoDetallePedido;
		$pedido->calcularTotal();
		$pedido->update();

		foreach ($detallesAnulados as $detalleAnulado) {
			Factory::getInstance()->getPedidoItem($detalleAnulado->pedido->numero, $detalleAnulado->nroItem)->borrar();
		}
	}

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('Se actualizaron correctamente los precios de los pedidos seleccionados');
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar actualizar el precio de los pedidos');
}
?>
<?php } ?>