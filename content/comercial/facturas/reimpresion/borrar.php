<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/facturas/reimpresion/borrar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::post('puntoDeVenta');
$numero = Funciones::post('numero');
$letra = Funciones::post('letra');

try {
	$factura = Factory::getInstance()->getFactura($empresa, $puntoDeVenta, 'FAC', $numero, $letra);
	$factura->borrar()->notificar('comercial/facturas/reimpresion/borrar/');

	$arr['puntoDeVenta'] = $factura->puntoDeVenta;
	$arr['nro'] = $factura->numero;
	$arr['letra'] = $factura->letra;
	Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La factura que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar borrar la factura: ' . $ex->getMessage());
}

?>
<?php } ?>