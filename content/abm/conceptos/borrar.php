<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/conceptos/borrar/')) { ?>
<?php

$idConcepto = Funciones::post('idConcepto');

try {
	$concepto = Factory::getInstance()->getConcepto($idConcepto);
	$concepto->borrar()->notificar('abm/conceptos/borrar/');
	Html::jsonSuccess('El concepto fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El concepto que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el concepto');
}
?>
<?php } ?>