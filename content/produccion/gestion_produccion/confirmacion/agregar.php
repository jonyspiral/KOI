<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/gestion_produccion/confirmacion/agregar/')) { ?>
<?php

$idOrdenDeFabricacion = Funciones::post('idOrdenDeFabricacion');
$numeroTarea = Funciones::post('numeroTarea');
$idSeccionProduccion = 60;
$cantidad = Funciones::post('cantidad');

function calcularCantidad($propuesto, $predespachado) {
	if ($propuesto <= $predespachado && $propuesto >= 0)
		return $propuesto;
	if ($propuesto >= $predespachado) //Esto puede pasar sólo si tocan JS, no va a pasar
		return $predespachado;
	return 0;
}

try {
	$conf = Factory::getInstance()->getConfirmacionStock();
	$conf->tareaProduccionItem = Factory::getInstance()->getTareaProduccionItem($idOrdenDeFabricacion, $numeroTarea, $idSeccionProduccion);
	for ($i = 1; $i <= 10; $i++) {
		$conf->cantidad[$i] = calcularCantidad(Funciones::keyIsSet($cantidad, $i, 0), Funciones::keyIsSet($conf->tareaProduccionItem->pendiente, $i, 0));
	}
	$conf->cantidadTotal = Funciones::sumaArray($conf->cantidad);
	$conf->guardar()->notificar('produccion/gestion_produccion/confirmacion/agregar/');

	Html::jsonSuccess('Se confirmó correctamente el stock');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar confirmar el stock');
}
?>
<?php } ?>