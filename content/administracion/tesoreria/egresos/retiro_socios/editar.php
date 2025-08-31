<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/retiro_socios/editar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	$aporte = Factory::getInstance()->getAporteSocio($datos['idRetiro'], $empresa);
	$datos['idCaja_S'] = $aporte->importePorOperacion->idCaja;
	$aporte->datosSinValidar = $datos;
	$aporte->importesSinValidar['S'] = $importes;
	$aporte->guardar();

	Html::jsonSuccess('Se editó correctamente el retiro de socio');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el retiro de socio');
}

?>
<?php } ?>