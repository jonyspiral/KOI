<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/causas_notas_de_credito/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$causaNotaDeCredito = Factory::getInstance()->getCausaNotaDeCredito($id);
	Html::jsonEncode('', $causaNotaDeCredito->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La causa de nota de crédito "' . $id . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>