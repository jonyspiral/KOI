<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/reimpresion/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$ordenDeCompra = Factory::getInstance()->getOrdenDeCompra($id);
	$ordenDeCompra->borrar();

	Html::jsonSuccess('La órden de compra se borró correctamente', array('nro' => $ordenDeCompra->id));
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La órden de compra que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la órden de compra Nº "' . $id . '"');
}

?>
<?php } ?>