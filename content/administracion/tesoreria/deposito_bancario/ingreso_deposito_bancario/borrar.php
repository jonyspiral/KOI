<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/borrar/')) { ?>
<?php

$idDepositoBancario = Funciones::post('idDepositoBancario');

try {
	Factory::getInstance()->beginTransaction();

	$depositoBancarioTemporal = Factory::getInstance()->getDepositoBancarioTemporal($idDepositoBancario);
	$depositoBancarioTemporal->revertirEstadoCheques();
	$depositoBancarioTemporal->borrar()->notificar('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/borrar/');

	Factory::getInstance()->commitTransaction();

	$nombreOperación = ($depositoBancarioTemporal->esVentaCheque() ? 'La venta de cheques' : 'El depósito bancario');

	Html::jsonSuccess($nombreOperación . ' fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError($nombreOperación . ' que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar ' . lcfirst($nombreOperación));
}
?>
<?php } ?>