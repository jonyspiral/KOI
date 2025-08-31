<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/colores_por_articulo/editar/')) { ?>
<?php

$idArticulo = Funciones::post('idArticulo');
$idColorPorArticulo = Funciones::post('id');
$ecommerceExiste = Funciones::post('ecommerceExiste');
$ecommerceNombre = Funciones::post('ecommerceNombre');
$ecommerceInfo = Funciones::post('ecommerceInfo');
$ecommerceForSale = Funciones::post('ecommerceForSale');
$ecommerceCondition = Funciones::post('ecommerceCondition');
$ecommerceCategory = Funciones::post('ecommerceCategory');
$ecommerceExclusive = Funciones::post('ecommerceExclusive');
$ecommerceFeatured = Funciones::post('ecommerceFeatured');
$ecommercePrice1 = Funciones::post('ecommercePrice1');
$ecommercePrice2 = Funciones::post('ecommercePrice2');
$ecommercePrice3 = Funciones::post('ecommercePrice3');
$ecommerceImage1 = Funciones::post('ecommerceImage1');

try {
	if (!isset($idArticulo) || !isset($idColorPorArticulo)) {
		throw new FactoryExceptionRegistroNoExistente();
	}

	$colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColorPorArticulo);

	$colorPorArticulo->ecommerceExiste = $ecommerceExiste;
	$colorPorArticulo->ecommerceNombre = trim($ecommerceNombre, ' ');
	$colorPorArticulo->ecommerceInfo = trim($ecommerceInfo, ' ');
	$colorPorArticulo->ecommerceForSale = $ecommerceForSale;
	$colorPorArticulo->ecommerceCondition = $ecommerceCondition;
	$colorPorArticulo->ecommerceCategory = Factory::getInstance()->getCategoriaCalzadoUsuario($ecommerceCategory);
	$colorPorArticulo->ecommerceExclusive = $ecommerceExclusive;
	$colorPorArticulo->ecommerceFeatured = $ecommerceFeatured;
	$colorPorArticulo->ecommercePrice1 = $ecommercePrice1;
	$colorPorArticulo->ecommercePrice2 = $ecommercePrice2;
	$colorPorArticulo->ecommercePrice3 = $ecommercePrice3;
	$colorPorArticulo->ecommerceImage1 = $ecommerceImage1;

	$colorPorArticulo->guardar()->notificar('abm/colores_por_articulo/editar/');
	Html::jsonSuccess('El color por artículo fue editado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El color por artículo que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el color por artículo');
}
?>
<?php } ?>