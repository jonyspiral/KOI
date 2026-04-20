<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/personal/buscar/')) { ?>
<?php
$id = Funciones::get('id');

try {
	$personal = Factory::getInstance()->getPersonal($id);
	Html::jsonEncode('', $personal->expand());//expand abre el primer nivel del objeto json

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El personal "' . $id . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
} 
?>
<?php } ?>