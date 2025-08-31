<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/ejercicios_contables/buscar/')) { ?>
<?php

$idEjercicioContable = Funciones::get('idEjercicioContable');

try {
	$ejercicioContable = Factory::getInstance()->getEjercicioContable($idEjercicioContable);
	Html::jsonEncode('', $ejercicioContable->expand());
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El ejercicio contable que intentó buscar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar el ejercicio contable');
}

?>
<?php } ?>