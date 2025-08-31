<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/formas_pago/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$formaPago = Factory::getInstance()->getFormaDePago($id);
	Factory::getInstance()->marcarParaBorrar($formaPago);
	Factory::getInstance()->persistir($formaPago);
	Html::jsonSuccess('La forma de pago fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La forma de pago que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la forma de pago');
}
?>
<?php } ?>