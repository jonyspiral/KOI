<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/aporte_socios/agregar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	$rec = Factory::getInstance()->getAporteSocio();
	$rec->empresa = $empresa;
	$rec->datosSinValidar = $datos;
	$rec->importesSinValidar['E'] = $importes;
	$rec->guardar();

	Html::jsonSuccess('Se generó correctamente el aporte de socio');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el aporte de socio');
}

?>
<?php } ?>