<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/transportes/buscar/')) { ?>
<?php
$idTransporte = Funciones::get('idTransporte');//pido los parametros del de cliente y sucursal

try {
	$transporte = Factory::getInstance()->getTransporte($idTransporte);//llamada a getSucursal paso los parametros traidos por get
	Html::jsonEncode('', $transporte->expand());//expand abre el primer nivel del objeto json

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El transporte "' . $idTransporte . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>