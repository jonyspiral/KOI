<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/ejercicios_contables/editar/')) { ?>
<?php

$idEjercicioContable = Funciones::post('idEjercicioContable');
$nombre = Funciones::post('nombre');
$fechaDesde = Funciones::post('fechaDesde');
$fechaHasta = Funciones::post('fechaHasta');

try {
	if (!isset($idEjercicioContable)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$ejercicioContable = Factory::getInstance()->getEjercicioContable($idEjercicioContable);
	if ($ejercicioContable->anulado()) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$ejercicioContable->nombre = $nombre;
	$ejercicioContable->fechaDesde = $fechaDesde;
	$ejercicioContable->fechaHasta = $fechaHasta;

	$ejercicioContable->guardar()->notificar('abm/ejercicios_contables/editar/');
	Html::jsonSuccess('El ejercicio contable fue guardado correctamente');
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El ejercicio contable que intentó editar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar guardar el ejercicio contable');
}

?>
<?php } ?>