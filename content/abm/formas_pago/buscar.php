<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/formas_pago/buscar/')) { ?>
<?php

$id = Funciones::get('dias');//obtengo parametro de busqueda por get

try {
	$formaPago = Factory::getInstance()->getFormaDePago($id);
	Html::jsonEncode('', $formaPago->expand());//expand abre el primer nivel del objeto json

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La forma de pago "' . $id . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>