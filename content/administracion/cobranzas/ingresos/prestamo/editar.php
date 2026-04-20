<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/prestamo/editar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	$prestamo = Factory::getInstance()->getPrestamo($datos['idPrestamo'], $empresa);
	$datos['idCaja_E'] = $prestamo->importePorOperacion->idCaja;
	$prestamo->datosSinValidar = $datos;
	$prestamo->importesSinValidar['E'] = $importes;
	$prestamo->guardar();

	Html::jsonSuccess('Se editó correctamente el prestamo');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el prestamo');
}

?>
<?php } ?>