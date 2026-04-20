<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/asientos_contables/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	Contabilidad::descontabilizar($id);
	Html::jsonSuccess('El asiento contable fue borrado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el asiento contable');
}
?>
<?php } ?>