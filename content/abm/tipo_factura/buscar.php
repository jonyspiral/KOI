<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/tipo_factura/buscar/')) { ?>
<?php

$idTipoFactura = Funciones::get('idTipoFactura');

try {
	$tipoFactura = Factory::getInstance()->getTipoFactura($idTipoFactura);
	Html::jsonEncode('', $tipoFactura->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El tipo de factura "' . $idTipoFactura . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>