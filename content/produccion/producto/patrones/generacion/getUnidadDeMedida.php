<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/patrones/generacion/buscar/')) { ?>
<?php

$idMaterial = Funciones::get('idMaterial');

try {
	Html::jsonEncode('', array('unidadDeMedida' => Factory::getInstance()->getMaterial($idMaterial)->unidadDeMedida->nombre));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>