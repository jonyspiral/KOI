<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/articulos/agregar/')) { ?>
<?php

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
	$articulo = Factory::getInstance()->getArticulo();
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

	foreach ($colores as $col) {
		$color = Factory::getInstance()->getColorPorArticulo();
		$color->idColor = $col;
		$color->id = $color->color->id;
		$color->nombre = $color->color->nombre;

		$articulo->addItem($color);
	}

	$articulo->guardar()->notificar('abm/articulos/agregar/');

	Html::jsonSuccess('El articulo fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el articulo');
}
?>
<?php } ?>