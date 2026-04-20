<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/aporte_socios/editar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	$aporte = Factory::getInstance()->getAporteSocio($datos['idAporte'], $empresa);
	$datos['idCaja_E'] = $aporte->importePorOperacion->idCaja;
	$aporte->datosSinValidar = $datos;
	$aporte->importesSinValidar['E'] = $importes;
	$aporte->guardar();

	Html::jsonSuccess('Se editó correctamente el aporte de socio');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el aporte de socio');
}

?>
<?php } ?>