<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/curvas/buscar/')) { ?>
<?php
$id = Funciones::get('id');

try {
	$horaria = Factory::getInstance()->getCurva($id);
	Html::jsonEncode('', $horaria->expand());//expand abre el primer nivel del objeto json

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La curva "' . $id . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
} 
?>
<?php } ?>