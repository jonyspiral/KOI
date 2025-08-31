<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/asientos_modelo/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$asientoModelo = Factory::getInstance()->getAsientoContableModelo($id);
	$asientoModelo->borrar()->notificar('administracion/contabilidad/asientos_modelo/borrar/');
	Html::jsonSuccess('El asiento modelo fue borrado correctamente', array('id' => $id));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el asiento modelo');
}

?>
<?php } ?>