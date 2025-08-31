<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/conceptos/editar/')) { ?>
<?php

$idConcepto = Funciones::post('idConcepto');
$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');

try {
	if (!isset($idConcepto))
		throw new FactoryExceptionRegistroNoExistente();
	
	$concepto = Factory::getInstance()->getConcepto($idConcepto);

	$concepto->nombre = $nombre;
	$concepto->descripcion = $descripcion;

	$concepto->guardar()->notificar('abm/conceptos/editar/');
	Html::jsonSuccess('El concepto fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El concepto que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el concepto');
}
?>
<?php } ?>