<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/ingreso_gastos/agregar/')) { ?>
<?php

//Corresponde a la rendición de gastos

$idsGastitos = Funciones::post('gastitos');
$idCaja = Funciones::post('idCaja');
$fecha = Funciones::post('fecha');
$observaciones = Funciones::post('observaciones');

try {
	Factory::getInstance()->getPermisoPorUsuarioPorCaja($idCaja, Usuario::logueado()->id, PermisosUsuarioPorCaja::verCaja); //Esto puede tirar un FactoryExceptionRegistroNoExistente
	$caja = Factory::getInstance()->getCaja($idCaja);

	$gastitos = array();
	$totalEfectivo = 0;
	foreach($idsGastitos as $idGastito){
		$gastito = Factory::getInstance()->getGastito($idGastito);
		$gastitos[] = $gastito;
		if ($gastito->idCaja != $caja->id) {
			throw new FactoryExceptionCustomException('Alguno de los gastos seleccionados corresponde a una caja diferente a la seleccionada. Por favor recargue la lista de gastos e inténtelo nuevamente');
		}
		if ($gastito->consolidado()) {
			throw new FactoryExceptionCustomException('Alguno de los gastos seleccionados ya ha sido rendido previamente. Por favor recargue la lista de gastos e inténtelo nuevamente');
		}
		$totalEfectivo += $gastito->importe;
	}

	$datos = array();
	$datos['usuario'] = Usuario::logueado();
	$datos['idCaja_S'] = $caja->id;
	$datos['gastitos'] = $gastitos;
	$datos['fechaDocumento'] = $fecha;
	$datos['observaciones'] = $observaciones;

	$efectivo = Factory::getInstance()->getEfectivo();
	$efectivo->importe = $totalEfectivo;

	$rendicionDeGastos = Factory::getInstance()->getRendicionGastos();
	$rendicionDeGastos->importesSinValidar['S'] = array(TiposImporte::efectivo => array($efectivo->simularComoImporte()));
	$rendicionDeGastos->datosSinValidar = $datos;
	$rendicionDeGastos->empresa = Funciones::session('empresa');
	$rendicionDeGastos->guardar();

	Html::jsonSuccess('Los gastos se rindieron correctamente', $jsonSuccess);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('No tiene permiso para hacer rendiciones sobre la caja indicada');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar rendir los gastos');
}

?>
<?php } ?>