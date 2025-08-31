<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/reimpresion_rendicion/editar/')) { ?>
<?php

$numero = Funciones::post('numero');
$empresa = Funciones::session('empresa');
$observaciones = Funciones::post('observaciones');

try {
	if (!isset($numero)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	
	$rendicionDeGastos = Factory::getInstance()->getRendicionGastos($numero, $empresa);
	$rendicionDeGastos->observaciones = $observaciones;
	$rendicionDeGastos->update();

	Html::jsonSuccess('La rendición de gastos fue editada correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La rendición de gastos que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar la rendición de gastos');
}
?>
<?php } ?>