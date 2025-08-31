<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/almacenes/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$zona = Factory::getInstance()->getAlmacen($id);
	Html::jsonEncode('', $zona->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El almacen "' . $id . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>