<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/ejercicios_contables/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$fechaDesde = Funciones::post('fechaDesde');
$fechaHasta = Funciones::post('fechaHasta');

try {
	$ejercicioContable = Factory::getInstance()->getEjercicioContable();
	$ejercicioContable->nombre = $nombre;
	$ejercicioContable->fechaDesde = $fechaDesde;
	$ejercicioContable->fechaHasta = $fechaHasta;

	$ejercicioContable->guardar()->notificar('abm/ejercicios_contables/agregar/');
	Html::jsonSuccess('El ejercicio contable fue guardado correctamente');
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar guardar el ejercicio contable');
}

?>
<?php } ?>
