<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/patrones/gestion/editar/')) { ?>
<?php

$idArticulo = Funciones::post('idArticulo');
$idColor = Funciones::post('idColor');
$idVersion = Funciones::post('idVersion');
$versionActual = (Funciones::post('versionActual') == 'S' ? 'S' : 'N');

try {
	Factory::getInstance()->beginTransaction();

	if ($versionActual == 'N') {
		throw new FactoryExceptionCustomException('No se puede dejar un patrón sin versión actual');
	}

	$patrones = Factory::getInstance()->getListObject('Patron', 'cod_articulo = ' . Datos::objectToDB($idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($idColor));

	foreach ($patrones as $patron) {
		$patron->detalle;
		$patron->versionActual = 'N';
		$patron->guardar();
	}

	$patron = Factory::getInstance()->getPatron($idArticulo, $idColor, $idVersion);
	$patron->detalle;
	$patron->confirmado = 'S';
	$patron->versionActual = 'S';
	$patron->guardar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El patrón fue editado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El patrón que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el patrón');
}
?>
<?php } ?>