<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/bancos/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$banco = Factory::getInstance()->getBanco($id);
	Html::jsonEncode('', $banco->expand());
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El banco que intentó buscar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar el banco');
}

?>
<?php } ?>