<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede()) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::post('puntoDeVenta');
$numero = Funciones::post('numero');
$letra = Funciones::post('letra');

try {
	$ndb = Factory::getInstance()->getNotaDeDebito($empresa, $puntoDeVenta, 'NDB', $numero, $letra);
	Factory::getInstance()->beginTransaction();
	$ndb->borrar()->notificar('comercial/notas_de_debito/reimpresion/borrar/');
	Factory::getInstance()->commitTransaction();

	$arr['puntoDeVenta'] = $ndb->puntoDeVenta;
	$arr['nro'] = $ndb->numero;
	$arr['letra'] = $ndb->letra;
	Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La nota de débito que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar borrar la nota de débito: ' . $ex->getMessage());
}

?>
<?php } ?>