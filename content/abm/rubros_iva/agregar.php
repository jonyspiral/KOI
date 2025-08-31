<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/rubros_iva/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$columnaIva = Funciones::post('columnaIva');

try {
	$rubroIva = Factory::getInstance()->getRubroIva();
	$rubroIva->nombre = $nombre;
	$rubroIva->columnaIva = $columnaIva;
	Factory::getInstance()->persistir($rubroIva);
	Html::jsonSuccess('El Rubro fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el rubro');
}

?>
<?php } ?>