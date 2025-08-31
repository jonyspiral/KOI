<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/colores_por_articulo/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');
$idColorPorArticulo = Funciones::get('id');

try {
	if (!isset($idArticulo) || !isset($idColorPorArticulo)) {
		throw new FactoryExceptionRegistroNoExistente();
	}

	$colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColorPorArticulo);

	Html::jsonEncode('', $colorPorArticulo->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El artículo "' . $idArticulo . '" color "' . $idColorPorArticulo . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>