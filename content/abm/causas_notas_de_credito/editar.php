<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/causas_notas_de_credito/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	$causaDeNotaDeCredito = Factory::getInstance()->getCausaNotaDeCredito($id);
	$causaDeNotaDeCredito->nombre = $nombre;
	Factory::getInstance()->persistir($causaDeNotaDeCredito);
	Html::jsonSuccess('La causa de nota crédito fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La causa de nota crédito que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la causa de nota crédito');
}
?>
<?php } ?>