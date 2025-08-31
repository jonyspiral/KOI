<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/areas_empresa/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$areaEmpresa = Factory::getInstance()->getAreaEmpresa($id);
	Html::jsonEncode('', $areaEmpresa->expand());
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El área empresa que intentó buscar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar el área empresa');
}

?>
<?php } ?>