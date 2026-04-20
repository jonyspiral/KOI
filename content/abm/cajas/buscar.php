<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/cajas/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$caja = Factory::getInstance()->getCaja($id);
	foreach ($caja->cajasPosiblesTransferenciaInterna as $cajaPosible) {
		$cajaPosible->cajaEntrada;
	}
	Html::jsonEncode('', $caja->expand());
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La caja que intentó buscar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar la caja');
}

?>
<?php } ?>