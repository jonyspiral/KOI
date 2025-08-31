<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/periodos_fiscales/tipos/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');

try {
	if (!isset($id)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$tipoPeriodoFiscal = Factory::getInstance()->getTipoPeriodoFiscal($id);
	$tipoPeriodoFiscal->nombre = $nombre;

	$tipoPeriodoFiscal->guardar()->notificar('administracion/contabilidad/periodos_fiscales/tipos/editar/');
	Html::jsonSuccess('El tipo de período fiscal fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $ex){
	Html::jsonError('El tipo de período fiscal que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el tipo de período fiscal');
}
?>
<?php } ?>