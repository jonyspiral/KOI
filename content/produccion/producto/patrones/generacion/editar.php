<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/patrones/generacion/editar/')) { ?>
<?php

$idArticulo = Funciones::post('idArticulo');
$idColor = Funciones::post('idColor');
$idVersion = Funciones::post('idVersion');
$detalle = Funciones::post('detalle');

try {
	if (empty($idArticulo) || empty($idColor) || empty($idVersion)) {
		throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');
	}

	if (count($detalle) == 0) {
		throw new FactoryExceptionCustomException('El patrón debe tener al menos un detalle');
	}

	$articuloColor = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);

	if (!$articuloColor->idArticulo) {
		throw new FactoryExceptionCustomException('El Artículo no existe o no posee el color seleccionado');
	}

	Factory::getInstance()->beginTransaction();

	$patron = Factory::getInstance()->getPatron($idArticulo, $idColor, $idVersion);
	$patron->detalle = null;
	foreach ($detalle as $item) {
		if (empty($item['idSeccion']) || empty($item['idConjunto']) || empty($item['idMaterial']) || empty($item['idColor']) || empty($item['consumoPar'])) {
			throw new FactoryExceptionCustomException('Debe completar todos los campos de los detalles');
		}

		$patronItem = Factory::getInstance()->getPatronItem();
		$patronItem->patron = $patron;
		$patronItem->seccion = Factory::getInstance()->getSeccionProduccion($item['idSeccion']);
		$patronItem->conjunto = Factory::getInstance()->getConjunto(trim($item['idConjunto']));
		$patronItem->colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($item['idMaterial'], $item['idColor']);
		$patronItem->material = $patronItem->colorMateriaPrima->material;
		$patronItem->consumoPar = Funciones::toFloat($item['consumoPar']);

		$patron->addDetalle($patronItem);
	}

	$patron->guardar();

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('El patron se editó correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar editar el patron');
}

?>
<?php } ?>