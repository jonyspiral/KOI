<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/fajas_horarias/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$horarioEntrada= Funciones::post('horarioEntrada');
$horarioSalida= Funciones::post('horarioSalida');

try {
	$horaria = Factory::getInstance()->getFajaHoraria();
	
	$horaria->nombre = $nombre;
	$horaria->horarioEntrada= $horarioEntrada;
	$horaria->horarioSalida= $horarioSalida;

	Factory::getInstance()->persistir($horaria);
	Html::jsonSuccess('La faja horaria fue guardada correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la faja horaria');
}
?>
<?php } ?>