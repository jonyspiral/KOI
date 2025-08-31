<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/zonas_transporte/borrar/')) { ?>
<?php
$id = Funciones::post('id');

try {
	$zonaTransporte = Factory::getInstance()->getZonaTransporte($id);
	Factory::getInstance()->marcarParaBorrar($zonaTransporte);
	Factory::getInstance()->persistir($zonaTransporte);
	Html::jsonSuccess('La zona de transporte fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La zona de transporte que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la zona de transporte');
}
?>
<?php } ?>