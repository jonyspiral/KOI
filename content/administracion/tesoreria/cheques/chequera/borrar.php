<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/chequera/borrar/')) { ?>
<?php

$cheques = Funciones::post('cheques');

try {
	Factory::getInstance()->beginTransaction();

	foreach($cheques as $cheque){
		$chequeraItem = Factory::getInstance()->getChequeraItem($cheque['idChequeraItem']);
		$chequeraItem->borrar();
	}

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Los cheques se borraron correctamente');
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El cheque que intentó borrar no existe en la chequera');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el cheque: ' . $ex->getMessage());
}

?>
<?php } ?>