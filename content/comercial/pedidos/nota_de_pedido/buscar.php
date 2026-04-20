<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido/buscar/')) { ?>
<?php

function ampliar($ndp) {
	$ndp->cliente;
	$ndp->sucursal;
	$ndp->almacen;
	$ndp->estado;
	$ndp->formaDePago;
	$ndp->usuario->nombre;
	$ndp->usuario->apellido;
	$ndp->vendedor->nombreApellido;
	$ndp->autorizaciones->expand();
	foreach ($ndp->autorizaciones->autorizaciones as $key => $val) {
		$ndp->autorizaciones->autorizaciones[$key]->usuario->nombre;
		$ndp->autorizaciones->autorizaciones[$key]->usuario->apellido;
	}
	$ndp->autorizaciones->personasAutorizacionesPendientes;
	foreach ($ndp->detalle as $key => $val) {
		$ndp->detalle[$key]->articulo;
		$ndp->detalle[$key]->colorPorArticulo;
	}
	//return $ndp;
	return $ndp;//->expand();
}
	
function echoDetalle($notaDePedido){
	if ($notaDePedido->anulado == 'S') {
		Html::jsonInfo('La nota de pedido que busca fue anulada');
	} elseif ($notaDePedido->aprobado == 'S') {
		Html::jsonInfo('La nota de pedido ya fue procesada. Si aún no le llegó el pedido por favor revise el panel de pendientes');
	} else {
		Html::jsonEncode('', ampliar($notaDePedido));
	}
}

$idNotaDePedido = Funciones::get('idNotaDePedido');
try {
	$notaDePedido = Factory::getInstance()->getPedido($idNotaDePedido);
	if ($notaDePedido->esEcommerce()) {
		throw new FactoryException('No se pueden modificar pedidos de Ecommerce');
	} elseif (Usuario::logueado()->tipoPersona == TiposPersonal::vendedor && Usuario::logueado()->codigoPersonal == $notaDePedido->cliente->vendedor->id) {
		echoDetalle($notaDePedido);
	} elseif (Usuario::logueado()->tipoPersona == TiposContacto::cliente && Usuario::logueado()->contacto->cliente->id == $notaDePedido->cliente->id) {
		echoDetalle($notaDePedido);
	} elseif (Usuario::logueado()->tipoPersona == TiposPersonal::personal) {
		echoDetalle($notaDePedido);
	} else {
		throw new FactoryException('No tiene permiso para ver la nota de pedido');
	}
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
?>
<?php } ?>