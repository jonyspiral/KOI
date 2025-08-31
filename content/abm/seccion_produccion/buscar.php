<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/seccion_produccion/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$seccion = Factory::getInstance()->getSeccionProduccion($id);
	foreach ($seccion->almacenes as $almacen) {
		$almacen->expand();
	}
	Html::jsonEncode('', $seccion->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La sección "' . $id . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>