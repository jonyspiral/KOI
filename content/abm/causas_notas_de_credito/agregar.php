<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/causas_notas_de_credito/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');

try {
	$causaNotaDeCredito = Factory::getInstance()->getCausaNotaDeCredito();
	$causaNotaDeCredito->nombre = $nombre;
	Factory::getInstance()->persistir($causaNotaDeCredito);
	Html::jsonSuccess('La causa de nota de crédito fue guardada correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la cauusa de nota de crédito');
}

?>
<?php } ?>