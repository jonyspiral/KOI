<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/bancos/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$codigoBanco = Funciones::post('codigoBanco');

try {
	if (!isset($id)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$banco = Factory::getInstance()->getBanco($id);
	if ($banco->anulado()) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$banco->nombre = $nombre;
	$banco->codigoBanco = $codigoBanco;

	$banco->guardar()->notificar('abm/bancos/editar/');
	Html::jsonSuccess('El banco fue guardado correctamente');
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El banco que intentó editar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar guardar el banco');
}

?>
<?php } ?>