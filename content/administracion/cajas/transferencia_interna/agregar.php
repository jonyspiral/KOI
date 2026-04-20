<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/transferencia_interna/agregar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	$transferenciaInterna = Factory::getInstance()->getTransferenciaInterna();
	$transferenciaInterna->empresa = $empresa;
	$transferenciaInterna->datosSinValidar = $datos;
	$transferenciaInterna->importesSinValidar['E'] = $importes;
	$transferenciaInterna->importesSinValidar['S'] = $importes;

	$transferenciaInterna->guardar();

	Html::jsonSuccess('Se generó correctamente la transferencia interna de la caja ' . $datos['idCaja_S'] . ' a la caja ' . $datos['idCaja_E'], $rec);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar la transferencia interna');
}

?>
<?php } ?>