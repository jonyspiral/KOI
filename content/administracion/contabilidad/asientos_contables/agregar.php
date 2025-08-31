<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/asientos_contables/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$nombre = Funciones::post('nombre');
$fecha = Funciones::post('fecha');
$detalleJson = Funciones::post('detalleJson');

try {
	$asientoContable = Contabilidad::contabilizar($empresa, $nombre, $fecha, $detalleJson);
	Html::jsonSuccess('Se generó correctamente el asiento contable', $asientoContable->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el asiento contable');
}

?>
<?php } ?>