<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/zonas/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$zona = Factory::getInstance()->getZona($id);
	Html::jsonEncode('', $zona->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La zona "' . $id . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>