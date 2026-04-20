<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/ajustes/buscar/')) { ?>
<?php

$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	if (!$idArticulo || !$idColor) {
		throw new FactoryExceptionCustomException('Debe indicarse el artículo y el color');
	}

	$color = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);

	Html::jsonEncode('', array(
		'rangoTalle' => $color->articulo->rangoTalle->posicion,
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