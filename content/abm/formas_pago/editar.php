<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/formas_pago/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	$formaPago = Factory::getInstance()->getFormaDePago($id);

	$formaPago->nombre = $nombre;

	Factory::getInstance()->persistir($formaPago);
	Html::jsonSuccess('La forma de pago fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La forma de pago que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la forma de pago');
}
?>
<?php } ?>