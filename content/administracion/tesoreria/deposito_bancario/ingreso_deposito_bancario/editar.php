<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/editar/')) { ?>
<?php

$idDepositoBancario = Funciones::post('idDepositoBancarioTemporal');
$idCuentaBancaria = Funciones::post('idCuentaBancaria');
$fecha = Funciones::post('fecha');
$ventaDeCheque = Funciones::post('ventaCheque');
$numeroBoleta = Funciones::post('numeroBoleta');
$efectivo = Funciones::post('efectivo');
$efectivo = (empty($efectivo) ? 0 : $efectivo);
$arrayCheques = Funciones::post('cheques');

try {
	Factory::getInstance()->beginTransaction();

	$depositoBancarioTemporal = Factory::getInstance()->getDepositoBancarioTemporal($idDepositoBancario);

	$cheques = array();
	foreach($arrayCheques as $chequeItem){
		$cheque = Factory::getInstance()->getCheque($chequeItem['id']);
		$cheques[] = $cheque;
	}

	$depositoBancarioTemporal->chequesNuevos = $cheques;

	$depositoBancarioTemporal->cuentaBancaria = Factory::getInstance()->getCuentaBancaria($idCuentaBancaria);
	$depositoBancarioTemporal->ventaCheque = $ventaDeCheque;
	$depositoBancarioTemporal->efectivo = $efectivo;
	if ($ventaDeCheque == 'N') {
		$depositoBancarioTemporal->numeroBoleta = $numeroBoleta;
	}
	$depositoBancarioTemporal->fecha = $fecha;

	$depositoBancarioTemporal->guardar();

	Factory::getInstance()->commitTransaction();

	$nombreOperación = ($depositoBancarioTemporal->esVentaCheque() ? 'la venta de cheques' : 'el depósito bancario');

	Html::jsonSuccess('Se editó correctamente ' . $nombreOperación . ' y se encuentra disponible para confirmar.');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar ' . $nombreOperación);
}

?>
<?php } ?>