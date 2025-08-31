<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/condiciones_iva/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$condicionIva = Factory::getInstance()->getCondicionIva($id);
	Factory::getInstance()->marcarParaBorrar($condicionIva);
	Factory::getInstance()->persistir($condicionIva);
	Html::jsonSuccess('La condición de IVA fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La condición de IVA que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la condición de IVA');
}
?>
<?php } ?>