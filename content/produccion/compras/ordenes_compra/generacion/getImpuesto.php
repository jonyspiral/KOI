<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/generacion/buscar/')) { ?>
<?php

$idImpuesto = Funciones::get('idImpuesto');

try {
	if (empty($idImpuesto))
		throw new FactoryExceptionCustomException('El impuesto no existe');

	$impuesto = Factory::getInstance()->getImpuesto($idImpuesto);

	Html::jsonEncode('', array('id' => $impuesto->id, 'porcentaje' => $impuesto->porcentaje));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>