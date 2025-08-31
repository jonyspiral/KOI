<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/presupuesto/manual/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();

	Factory::getInstance()->beginTransaction();

	$presupuesto = Factory::getInstance()->getPresupuesto($id);
	$presupuesto->borrar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El pedido de cotización Nº "' . $id . '" fue borrado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError('No existe el pedido de cotización Nº ' . $id);
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el pedido de cotización');
}

?>
<?php } ?>