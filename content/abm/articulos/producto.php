<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/articulos/editar/producto/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$naturaleza = Funciones::post('naturaleza') == 'PT' ? 'PT' : 'SE';
$idProveedor = Funciones::post('idProveedor');
$idMarca = Funciones::post('idMarca');
$idLineaProducto = Funciones::post('idLineaProducto');
$idTemporada = Funciones::post('idTemporada');
$idCliente = Funciones::post('idCliente');
$idRangoTalle = Funciones::post('idRangoTalle');
$idRutaProduccion = Funciones::post('idRutaProduccion');
$origen = Funciones::post('origen');
$idHorma = Funciones::post('idHorma');

$colores = Funciones::post('colores');

try {
	if (!isset($id)) {
		throw new FactoryExceptionCustomException();
	}
	$articulo = Factory::getInstance()->getArticulo($id);

	$articulo->nombre = $nombre;
	$articulo->naturaleza = $naturaleza;
	$articulo->idCliente = $idCliente;
	$articulo->idProveedor = $idProveedor;
	$articulo->idMarca = $idMarca;
	$articulo->idLineaProducto = $idLineaProducto;
	$articulo->idTemporada = $idTemporada;
	$articulo->idRangoTalle = $idRangoTalle;
	$articulo->idRutaProduccion = $idRutaProduccion;
	$articulo->origen = $origen;
	$articulo->idHorma = $idHorma;

	$auxArray = array();
	foreach ($colores as $col) {
		$auxArray[$col] = array('idColor' => $col, 'existente' => false);
	}

	foreach ($articulo->colores as $color) {
		if (isset($auxArray[$color->id])) {
			$auxArray[$color->id]['existente'] = true;
		} elseif ($color->vigente == 'S') {
			Factory::getInstance()->marcarParaBorrar($color);
		}
	}

	foreach ($auxArray as $col) {
		if (!$col['existente']) {
			$color = Factory::getInstance()->getColorPorArticulo();
			$color->articulo = $articulo;
			$color->idColor = $col['idColor'];
			$color->id = $color->color->id;
			$color->nombre = $color->color->nombre;

			$articulo->addItem($color);
		}
	}

	$articulo->guardar()->notificar('abm/articulos/editar/producto/');

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