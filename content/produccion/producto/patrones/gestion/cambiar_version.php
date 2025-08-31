<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/patrones/gestion/editar/')) { ?>
<?php

$idArticulo = Funciones::post('idArticulo');
$idColor = Funciones::post('idColor');
$idVersion = Funciones::post('idVersion');
$tipoPatron = Funciones::post('tipoPatron');

try {
	Factory::getInstance()->beginTransaction();

	if (!($tipoPatron == 'P' || $tipoPatron == 'D')) {
		throw new FactoryExceptionCustomException('El tipo de patrón solo puede ser "D" o "P"');
	}

	$patron = Factory::getInstance()->getPatron($idArticulo, $idColor, $idVersion);
	$patron->detalle;
	$patron->tipoPatron = $tipoPatron;
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