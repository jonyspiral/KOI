<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/formas_pago/agregar/')) { ?>
<?php

$cantDias = Funciones::post('cantDias');
$nombre = Funciones::post('nombre');

try {
	$formaPago = Factory::getInstance()->getFormaDePago();

	$formaPago->id = $cantDias;
	$formaPago->nombre = $nombre;

	Factory::getInstance()->persistir($formaPago);
	Html::jsonSuccess('La forma de pago fue guardada correctamente');
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError('La forma de pago que intentó guardar ya existe.');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la forma de pago');
}
?>
<?php } ?>