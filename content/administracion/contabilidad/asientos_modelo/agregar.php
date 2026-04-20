<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/asientos_modelo/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$detalleJson = Funciones::post('detalleJson');

try {
	$asientoModelo = Factory::getInstance()->getAsientoContableModelo();
	$asientoModelo->nombre = $nombre;
	$i = 1;
	$filas = array();
	foreach($detalleJson as $f){
		$fila = Factory::getInstance()->getAsientoContableModeloFila();
		$fila->numeroFila = $i;
		$fila->imputacion = Factory::getInstance()->getImputacion($f['imputacion']);
		$fila->observaciones = $f['observaciones'];
		$filas[] = $fila;
		$i++;
	}
	$asientoModelo->detalle = $filas;
	$asientoModelo->guardar()->notificar('administracion/contabilidad/asientos_modelo/agregar/');

	Html::jsonSuccess('Se generó correctamente el asiento modelo', $asientoModelo->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el asiento modelo');
}

?>
<?php } ?>