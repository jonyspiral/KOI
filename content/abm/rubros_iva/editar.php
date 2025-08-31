<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/rubros_iva/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$columnaIva = Funciones::post('columnaIva');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	$rubroIva = Factory::getInstance()->getRubroIva($id);
	$rubroIva->nombre = $nombre;
	$rubroIva->columnaIva = $columnaIva;
	Factory::getInstance()->persistir($rubroIva);
	Html::jsonSuccess('El rubro fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El rubro que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el rubro');
}
?>
<?php } ?>