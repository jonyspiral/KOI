<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/conceptos/buscar/')) { ?>
<?php
$idConcepto = Funciones::get('idConcepto');

try {
	$concepto = Factory::getInstance()->getConcepto($idConcepto);
	Html::jsonEncode('', $concepto->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El concepto "' . $idConcepto . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>