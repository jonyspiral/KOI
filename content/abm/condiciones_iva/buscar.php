<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/condiciones_iva/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$condicionesIva = Factory::getInstance()->getCondicionIva($id);
	Html::jsonEncode('', $condicionesIva->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La condición de venta "' . $id . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>