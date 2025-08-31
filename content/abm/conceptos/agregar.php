<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/conceptos/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');

try {
	$concepto = Factory::getInstance()->getConcepto();

	$concepto->nombre = $nombre;
	$concepto->descripcion = $descripcion;

	$concepto->guardar()->notificar('abm/concepto/agregar/');
	Html::jsonSuccess('El concepto fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el concepto');
}

?>
<?php } ?>