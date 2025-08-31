<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/reimpresion/borrar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::post('puntoDeVenta');
$numero = Funciones::post('numero');
$letra = Funciones::post('letra');

try {
	$ncr = Factory::getInstance()->getNotaDeCredito($empresa, $puntoDeVenta, 'NCR', $numero, $letra);
	Factory::getInstance()->beginTransaction();
	$ncr->borrar()->notificar('comercial/notas_de_credito/reimpresion/borrar/');
	Factory::getInstance()->commitTransaction();

	$arr['puntoDeVenta'] = $ncr->puntoDeVenta;
	$arr['nro'] = $ncr->numero;
	$arr['letra'] = $ncr->letra;
	Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La nota de crédito que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar borrar la nota de crédito: ' . $ex->getMessage());
}

?>
<?php } ?>