<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/impuestos/buscar/')) { ?>
<?php
$idImpuesto = Funciones::get('idImpuesto');

try {
	$impuesto = Factory::getInstance()->getImpuesto($idImpuesto);
	Html::jsonEncode('', $impuesto->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El impuesto "' . $idImpuesto . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>