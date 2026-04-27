<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/retiro_socios/borrar/')) { ?>
<?php

$idRetiro = Funciones::post('idRetiro');
$empresa = Funciones::session('empresa');

try {
	$retiroSocio = Factory::getInstance()->getRetiroSocio($idRetiro, $empresa);
	$retiroSocio->borrar();

	Html::jsonSuccess('Se borró correctamente el aporte de socio');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el aporte de socio');
}

?>
<?php } ?>