<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/fajas_horarias/buscar/')) { ?>
<?php
$id = Funciones::get('id');

try {
	$horaria = Factory::getInstance()->getFajaHoraria($id);
	Html::jsonEncode('', $horaria->expand());//expand abre el primer nivel del objeto json

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La faja horaria "' . $id . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
} 
?>
<?php } ?>