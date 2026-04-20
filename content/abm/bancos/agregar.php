<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/bancos/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$codigoBanco = Funciones::post('codigoBanco');

try {
	$banco = Factory::getInstance()->getBanco();
	$banco->nombre = $nombre;
	$banco->codigoBanco = $codigoBanco;

	$banco->guardar()->notificar('abm/bancos/agregar/');
	Html::jsonSuccess('El banco fue guardado correctamente');
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar guardar el banco');
}

?>
<?php } ?>