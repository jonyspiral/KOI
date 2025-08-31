<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/articulos/editar/comercial/')) { ?>
<?php

$id = Funciones::post('id');
$fechaDeLanzamiento = Funciones::post('fechaDeLanzamiento');

$colores = Funciones::post('colores');

try {
	if (!isset($id)) {
		throw new FactoryExceptionCustomException();
	}
	$articulo = Factory::getInstance()->getArticulo($id);

	$articulo->fechaDeLanzamiento = $fechaDeLanzamiento;

	foreach ($articulo->colores as $color) {
		if (isset($colores[$color->id])) {
			$color->categoriaCalzadoUsuario = Factory::getInstance()->getCategoriaCalzadoUsuario($colores[$color->id]['idCategoriaCalzadoUsuario']);
			$color->tipoProductoStock = Factory::getInstance()->getTipoProductoStock($colores[$color->id]['idTipoProductoStock']);
			$color->formaDeComercializacion = $colores[$color->id]['formaDeComercializacion'];
			$color->precioMayoristaDolar = $colores[$color->id]['precioMayoristaDolar'];
			$color->precioDistribuidor = $colores[$color->id]['precioDistribuidor'];
			$color->precioMinoristaDolar = $colores[$color->id]['precioMinoristaDolar'];
			$color->precioDistribuidorMinorista = $colores[$color->id]['precioDistribuidorMinorista'];

			$curvas = array();
			foreach ($colores[$color->id]['curvas'] as $curva) {
				$cur = Factory::getInstance()->getCurvaPorArticulo();
				$cur->curva = Factory::getInstance()->getCurva($curva['id']);
				$curvas[] = $cur;
			}
			$color->curvas = $curvas;
		}
	}

	$articulo->guardar()->notificar('abm/articulos/editar/comercial/');

	Html::jsonSuccess('El articulo fue guardado correctamente');
} catch (FactoryExceptionCustomException $e) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El artículo que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el articulo');
}
?>
<?php } ?>