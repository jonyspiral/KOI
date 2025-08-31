<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/zonas_transporte/buscar/')) { ?>
<?php
$id = Funciones::get('id');//pido los parametros del de cliente y sucursal

try {
	$zonaTransporte = Factory::getInstance()->getZonaTransporte($id);
	Html::jsonEncode('', $zonaTransporte->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La zona de transporte "' . $id . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>