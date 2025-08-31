<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/descontar_pendiente/agregar/')) { ?>
<?php

$idOrdenDeCompra = Funciones::post('idOrdenDeCompra');
$numeroDeItem = Funciones::post('numeroDeItem');
$cantidad = Funciones::post('cantidad');

function calcularCantidad($propuesto, $predespachado) {
	if ($propuesto <= $predespachado && $propuesto >= 0)
		return $propuesto;
	if ($propuesto >= $predespachado) //Esto puede pasar sólo si tocan JS, no va a pasar
		return $predespachado;
	return 0;
}

try {
	$ordenDeCompraItem = Factory::getInstance()->getOrdenDeCompraItem($idOrdenDeCompra, $numeroDeItem);

	if($ordenDeCompraItem->material->usarango()){
		$cantidadTotal = 0;
		for($i = 1; $i <= 8; $i++){
			$ordenDeCompraItem->cantidadesPendientes[$i] -= $cantidad[$i];
			$cantidadTotal += $cantidad[$i];
			if($ordenDeCompraItem->cantidadesPendientes[$i] < 0){
				throw new FactoryExceptionCustomException('No pueden descontarse más unidades que las que se encuentran pendientes');
			}
		}

		$ordenDeCompraItem->cantidadPendiente -= $cantidadTotal;
	} else {
		$ordenDeCompraItem->cantidadPendiente -= $cantidad;

		if($ordenDeCompraItem->cantidadesPendientes[$i] < 0){
			throw new FactoryExceptionCustomException('No pueden descontarse más unidades que las que se encuentran pendientes');
		}
	}

	$ordenDeCompraItem->guardar();

	Html::jsonSuccess('Se descontó correctamente el stock');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar confirmar el stock');
}
?>
<?php } ?>