<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/ingreso_gastos/borrar/')) { ?>
<?php

//Corresponde al borrar de gastitos
$id = Funciones::post('id');

try {
	$gastito = Factory::getInstance()->getGastito($id);
	if ($gastito->consolidado()) {
		throw new FactoryExceptionCustomException('No puede borrarse un gasto ya rendido. Por favor recargue la lista de gastos e inténtelo nuevamente');
	} else {
		Factory::getInstance()->getPermisoPorUsuarioPorCaja($gastito->caja->id, Usuario::logueado()->id, PermisosUsuarioPorCaja::verCaja); //Esto puede tirar un FactoryExceptionRegistroNoExistente
		$gastito->borrar();
	}
	Html::jsonSuccess('El gasto fue borrado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El gasto que intentó borrar no existe o no tiene permiso para borrar gastos de la caja indicada');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el gasto');
}
?>
<?php } ?>