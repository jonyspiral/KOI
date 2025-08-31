<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/patrones/gestion/editar/')) { ?>
<?php

$idArticulo = Funciones::post('idArticulo');
$idColor = Funciones::post('idColor');
$idVersion = Funciones::post('idVersion');
$confirmado = (Funciones::post('confirmado') == 'S' ? 'S' : 'N');

try {
	Factory::getInstance()->beginTransaction();

	$patron = Factory::getInstance()->getPatron($idArticulo, $idColor, $idVersion);
	$patron->detalle;

	if ($patron->esVersionActual() && $versionActual == 'N') {
		throw new FactoryExceptionCustomException('No se puede desconfirmar un patrón que es versión actual');
	}

	$patron->confirmado = $confirmado;

	$patron->guardar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El patrón fue editado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El patrón que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el patrón');
}
?>
<?php } ?>