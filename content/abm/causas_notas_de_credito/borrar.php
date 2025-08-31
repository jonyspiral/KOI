<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/causas_notas_de_credito/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$causaDeNotaDeCredito = Factory::getInstance()->getCausaNotaDeCredito($id);
	Factory::getInstance()->marcarParaBorrar($causaDeNotaDeCredito);
	Factory::getInstance()->persistir($causaDeNotaDeCredito);
	Html::jsonSuccess('La causa de nota de crédito fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La causa de nota de crédito que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la causa de nota de crédito');
}
?>
<?php } ?>