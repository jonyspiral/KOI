<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/transportes/borrar/')) { ?>
<?php

$idTransporte = Funciones::post('idTransporte');

try {
	$transporte = Factory::getInstance()->getTransporte($idTransporte);
	$transporte->borrar()->notificar('abm/transportes/borrar/');
	Html::jsonSuccess('El transporte fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El transporte que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el transporte');
}
?>
<?php } ?>