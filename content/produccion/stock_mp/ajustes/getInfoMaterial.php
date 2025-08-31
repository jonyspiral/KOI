<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/ajustes/buscar/')) { ?>
<?php

$idAlmacen = Funciones::get('idAlmacen');
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');

try {
	if (!$idMaterial || !$idColor) {
		throw new FactoryExceptionCustomException('Debe indicarse el material y el color');
	}

	$color = Factory::getInstance()->getColorMateriaPrima($idMaterial, $idColor);

	Html::jsonEncode('', array(
        'rango' => $color->material->usaRango() ? $color->material->rango->posicion : array('1' => 'U'),
		'stock' => $color->getStockAlmacen($idAlmacen)
	));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>