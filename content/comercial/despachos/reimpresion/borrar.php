<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/despachos/reimpresion/borrar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$numeroDespacho = Funciones::post('numeroDespacho');
$numeroItem = Funciones::post('numeroItem');

try {
	$item = Factory::getInstance()->getDespachoItem($numeroDespacho, $numeroItem);
	$item->borrar()->notificar('comercial/despachos/reimpresion/borrar/');

	$arr['numeroDespacho'] = $item->despachoNumero;
	$arr['numeroItem'] = $item->numeroDeItem;
	Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El despacho que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el despacho: ' . $ex->getMessage());
}

?>
<?php } ?>