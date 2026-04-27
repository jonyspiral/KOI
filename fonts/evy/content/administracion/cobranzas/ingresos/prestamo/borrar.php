<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/prestamo/borrar/')) { ?>
<?php

$idPrestamo = Funciones::post('idPrestamo');
$empresa = Funciones::session('empresa');

try {
	$aporte = Factory::getInstance()->getPrestamo($idPrestamo, $empresa);
	$aporte->borrar();

	Html::jsonSuccess('Se borró correctamente el aporte de socio');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el aporte de socio');
}

?>
<?php } ?>