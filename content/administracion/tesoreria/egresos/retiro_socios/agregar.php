<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/retiro_socios/agregar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	$retiroSocio = Factory::getInstance()->getRetiroSocio();
	$retiroSocio->empresa = $empresa;
	$retiroSocio->datosSinValidar = $datos;
	$retiroSocio->importesSinValidar['S'] = $importes;
	$retiroSocio->guardar();

	Html::jsonSuccess('Se generó correctamente el retiro de socio');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el retiro de socio');
}

?>
<?php } ?>