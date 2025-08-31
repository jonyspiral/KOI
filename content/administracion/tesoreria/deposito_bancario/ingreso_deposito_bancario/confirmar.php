<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/agregar/')) { ?>
<?php

$idDepositoBancario = Funciones::post('idDepositoBancario');
$numeroTransaccion = Funciones::post('numeroTransaccion');
$observaciones = Funciones::post('observaciones');

try {
	Factory::getInstance()->beginTransaction();

	$depositoBancarioTemporal = Factory::getInstance()->getDepositoBancarioTemporal($idDepositoBancario);

	$datos = array();
	$datos['fecha'] = $depositoBancarioTemporal->fecha;
	$datos['observaciones'] = $observaciones;
	$datos['numeroTransaccion'] = $numeroTransaccion;
	$datos['idCuentaBancaria'] = $depositoBancarioTemporal->cuentaBancaria->id;
	$datos['esVentaDeCheque'] = $depositoBancarioTemporal->esVentaCheque();
	$datos['usuario'] = Usuario::logueado();
	$datos['idCaja_S'] = $depositoBancarioTemporal->caja->id;
	$datos['idCaja_E'] = $depositoBancarioTemporal->cuentaBancaria->caja->id;

	$importesSinValidar = Factory::getInstance()->getCheque()->simularArrayImportes();
	foreach($depositoBancarioTemporal->cheques as $cheque){
		/** @var Cheque $cheque */
		$importesSinValidar['C'][] = $cheque->simularComoImporte();
	}

	if(!$depositoBancarioTemporal->esVentaCheque() && $depositoBancarioTemporal->efectivo > 0){
		$efectivo = Factory::getInstance()->getEfectivo();
		$efectivo->importe = $depositoBancarioTemporal->efectivo;
		$importesSinValidar['E'][] = $efectivo->simularComoImporte();
	}

	$depositoBancario = Factory::getInstance()->getDepositoBancario();
	$depositoBancario->importesSinValidar['S'] = $importesSinValidar;
	$depositoBancario->importesSinValidar['E'] = $importesSinValidar;
	$depositoBancario->datosSinValidar = $datos;
	$depositoBancario->empresa = Funciones::session('empresa');
	$depositoBancario->guardar();

	if($depositoBancarioTemporal->esVentaCheque()){
		$cheques = $depositoBancarioTemporal->cheques;
		$importesSinValidarSalida = Factory::getInstance()->getCheque()->simularArrayImportes();

		$importeEfectivo = 0;
		foreach($cheques as $cheque){
			$importesSinValidarSalida['C'][] = $cheque->simularComoImporte();
			$importeEfectivo += $cheque->importe;
		}

		$datos = array();
		$datos['observaciones'] = 'Por venta de cheques';
		$datos['fecha'] = Funciones::hoy();
		$datos['usuario'] = Usuario::logueado();
		$datos['idCaja_S'] = $depositoBancarioTemporal->cuentaBancaria->caja->id;
		$datos['idCaja_E'] = $depositoBancarioTemporal->cuentaBancaria->caja->id;
		$datos['fecha_credito'] = Funciones::hoy();

		$efectivo = Factory::getInstance()->getEfectivo();
		$efectivo->importe = $importeEfectivo;
		$importesSinValidarEntrada = Factory::getInstance()->getEfectivo()->simularArrayImportes();
		$importesSinValidarEntrada['E'][] = $efectivo->simularComoImporte();

		$acreditarCheque = Factory::getInstance()->getAcreditarCheque();
		$acreditarCheque->importesSinValidar['S'] = $importesSinValidarSalida;
		$acreditarCheque->importesSinValidar['E'] = $importesSinValidarEntrada;
		$acreditarCheque->datosSinValidar = $datos;
		$acreditarCheque->empresa = Funciones::session('empresa');
		$acreditarCheque->guardar();
	}

	$depositoBancarioTemporal->confirmar('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/confirmar/');

	Factory::getInstance()->commitTransaction();

	$nombreOperación = ($depositoBancarioTemporal->esVentaCheque() ? 'la venta de cheques' : 'el depósito bancario');

	Html::jsonSuccess('Se confirmó correctamente ' . $nombreOperación);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar confirmar ' . $nombreOperación);
}

?>
<?php } ?>