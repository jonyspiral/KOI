<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/rubros_iva/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$rubroIva = Factory::getInstance()->getRubroIva($id);
	Factory::getInstance()->marcarParaBorrar($rubroIva);
	Factory::getInstance()->persistir($rubroIva);
	Html::jsonSuccess('El rubro fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El rubro que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el rubro');
}
?>
<?php } ?>