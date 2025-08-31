<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/aporte_socios/borrar/')) { ?>
<?php

$idAporte = Funciones::post('idAporte');
$empresa = Funciones::session('empresa');

try {
	$aporte = Factory::getInstance()->getAporteSocio($idAporte, $empresa);
	$aporte->borrar();

	Html::jsonSuccess('Se borró correctamente el aporte de socio');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el aporte de socio');
}

?>
<?php } ?>