<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/rubros_iva/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$rubroIva = Factory::getInstance()->getRubroIva($id);
	Html::jsonEncode('', $rubroIva->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El rubro de IVA "' . $id . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>