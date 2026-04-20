<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/ingreso_cheque_propio/agregar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	$ingresoChequePropio = Factory::getInstance()->getIngresoChequePropio();
	$ingresoChequePropio->empresa = $empresa;
	$ingresoChequePropio->datosSinValidar = $datos;
	$ingresoChequePropio->importesSinValidar['E'] = $importes;
	$ingresoChequePropio->guardar();

	Html::jsonSuccess('Se ingresaron correctamente los cheques en el sistema');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar los cheques');
}

?>
<?php } ?>