<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/panel_de_control/borrar/')) { ?>
<?php

$idCheque = Funciones::post('idCheque');

try {
	$cheque = Factory::getInstance()->getCheque($idCheque);
	$cheque->borrar();
	Html::jsonSuccess('El cheque fue anulado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El cheque que intentó anular no existe');
} catch (Exception $ex){
	Html::jsonError($ex->getMessage());
}
?>
<?php } ?>