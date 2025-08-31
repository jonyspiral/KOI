<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/ejercicios_contables/borrar/')) { ?>
<?php

$idEjercicioContable = Funciones::post('idEjercicioContable');

try {
	$ejercicioContable = Factory::getInstance()->getEjercicioContable($idEjercicioContable);
	$ejercicioContable->borrar()->notificar('abm/ejercicios_contables/borrar/');
	Html::jsonSuccess('El ejercicio contable fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El ejercicio contable que intentó borrar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar borrar el ejercicio contable');
}

?>
<?php } ?>