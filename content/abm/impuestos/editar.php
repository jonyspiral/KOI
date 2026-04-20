<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/impuestos/editar/')) { ?>
<?php

$idImpuesto = Funciones::post('idImpuesto');
$tipo = Funciones::post('tipo');
$nombre = Funciones::post('nombre');
$idImputacion = Funciones::post('idImputacion');
$porcentaje = Funciones::formatearDecimales(Funciones::post('porcentaje'), 2, '.');
$esGravado = (Funciones::post('esGravado') == 'S' ? 'S' : 'N');
$descripcion = Funciones::post('descripcion');

try {
	if (!isset($idImpuesto))
		throw new FactoryExceptionRegistroNoExistente();
	
	$impuesto = Factory::getInstance()->getImpuesto($idImpuesto);

	$impuesto->tipo = $tipo;
	$impuesto->nombre = $nombre;
	$impuesto->imputacion = Factory::getInstance()->getImputacion($idImputacion);
	$impuesto->porcentaje = $porcentaje;
	$impuesto->esGravado = $esGravado;
	$impuesto->descripcion = $descripcion;

	$impuesto->guardar()->notificar('abm/impuestos/editar/');
	Html::jsonSuccess('El impuesto fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El impuesto que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el impuesto');
}
?>
<?php } ?>