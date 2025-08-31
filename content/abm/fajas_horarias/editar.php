<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/fajas_horarias/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$horarioEntrada = Funciones::post('horarioEntrada');
$horarioSalida = Funciones::post('horarioSalida');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();

	$horaria = Factory::getInstance()->getFajaHoraria($id);
	
	$horaria->nombre = $nombre;
	$horaria->horarioEntrada= $horarioEntrada;
	$horaria->horarioSalida= $horarioSalida;

	Factory::getInstance()->persistir($horaria);
	Html::jsonSuccess('La faja horaria fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La faja horaria que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la faja horaria');
}
?>
<?php } ?>