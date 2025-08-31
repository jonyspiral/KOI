<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/impuestos/agregar/')) { ?>
<?php

$tipo = Funciones::post('tipo');
$nombre = Funciones::post('nombre');
$idImputacion = Funciones::post('idImputacion');
$porcentaje = Funciones::formatearDecimales(Funciones::post('porcentaje'), 2, '.');
$esGravado = (Funciones::post('esGravado') == 'S' ? 'S' : 'N');
$descripcion = Funciones::post('descripcion');

try {
	$impuesto = Factory::getInstance()->getImpuesto();

	$impuesto->tipo = $tipo;
	$impuesto->nombre = $nombre;
	$impuesto->imputacion = Factory::getInstance()->getImputacion($idImputacion);
	$impuesto->porcentaje = $porcentaje;
	$impuesto->esGravado = $esGravado;
	$impuesto->descripcion = $descripcion;

	$impuesto->guardar()->notificar('abm/impuestos/agregar/');
	Html::jsonSuccess('El impuesto fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el impuesto');
}

?>
<?php } ?>