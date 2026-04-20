<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/seccion_produccion/agregar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$nombreCorto = Funciones::post('nombreCorto');
$imprimeStickers = Funciones::post('imprimeStickers') == 'S' ? 'S' : 'N';
$jerarquiaSeccion = Funciones::post('jerarquiaSeccion') == 'P' ? 'P' : 'S';
$idSeccionSuperior = Funciones::post('idSeccionSuperior');
$ingresaAlStock = Funciones::post('ingresaAlStock') == 'S' ? 'S' : 'N';
$interrumpible = Funciones::post('interrumpible') == 'S' ? 'S' : 'N';
$idUnidadDeMedida = Funciones::post('idUnidadDeMedida') == 'M' ? 'M' : 'P';
$idAlmacenDefault = Funciones::post('idAlmacenDefault');

$almacenes = Funciones::post('almacenes');

try {
	$seccion = Factory::getInstance()->getSeccionProduccion();
	$seccion->id = $id;
	$seccion->nombre = $nombre;
	$seccion->nombreCorto = $nombreCorto;
	$seccion->imprimeStickers = $imprimeStickers;
	$seccion->jerarquiaSeccion = $jerarquiaSeccion;
	$seccion->idSeccionSuperior = $idSeccionSuperior;
	$seccion->ingresaAlStock = $ingresaAlStock;
	$seccion->interrumpible = $interrumpible;
	$seccion->idUnidadDeMedida = $idUnidadDeMedida;
	$seccion->idAlmacenDefault = $idAlmacenDefault;

	foreach ($almacenes as $almacen) {
		$seccion->addAlmacen(Factory::getInstance()->getAlmacen($almacen));
	}

	$seccion->guardar()->notificar('abm/seccion_produccion/agregar/');

	Html::jsonSuccess('La sección fue guardada correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la sección');
}

?>
<?php } ?>