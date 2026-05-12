<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/alicuotas_retenciones/borrar/')) { ?>
<?php

$idImpuesto = Funciones::post('idImpuesto');

try {
	$impuesto = Factory::getInstance()->getImpuesto($idImpuesto);
	$impuesto->borrar()->notificar('abm/impuestos/borrar/');
	Html::jsonSuccess('El impuesto fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El impuesto que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el impuesto');
}
?>
<?php } ?>