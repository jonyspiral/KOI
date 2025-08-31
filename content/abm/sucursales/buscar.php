<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/sucursales/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$idSucursal = Funciones::get('idSucursal');

try {
	$sucursal = Factory::getInstance()->getSucursal($idCliente, $idSucursal);//llamada a getSucursal paso los parametros traidos por get
	Html::jsonEncode('', $sucursal->expand());//expand abre el primer nivel del objeto json

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La sucursal "' . $idSucursal . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>