<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/ingreso_gastos/editar/')) { ?>
<?php

//Corresponde al ingreso y edición de gastitos

$empresa = Funciones::session('empresa');
$id = Funciones::post('id');
$fecha = Funciones::post('fecha');
$idPersonaGasto = Funciones::post('idPersonaGasto');
$importe = Funciones::post('importe');
$comprobante = Funciones::post('comprobante');
$observaciones = Funciones::post('observaciones');
$idCaja = Funciones::post('idCaja');

try {
	Factory::getInstance()->getPermisoPorUsuarioPorCaja($idCaja, Usuario::logueado()->id, PermisosUsuarioPorCaja::verCaja); //Esto puede tirar un FactoryExceptionRegistroNoExistente
	if ($importe < 0) {
		throw new FactoryExceptionCustomException('El importe debe ser mayor a cero');
	}
	$gastito = Factory::getInstance()->getGastito();
	if (empty($id)) {
		if(empty($fecha) || empty($idPersonaGasto) || empty($importe) || empty($idCaja)) {
			throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');
		}
		if ($comprobante != 'S') {
			$comprobante = 'N';
		}
		$gastito->caja = Factory::getInstance()->getCaja($idCaja);
		if (($gastito->caja->importeEfectivoFinal + abs($gastito->caja->importeDescubierto)) < $importe) {
			throw new FactoryExceptionCustomException('La caja no tiene el efectivo suficiente para ingresar el gasto');
		}
		$gastito->empresa = $empresa;
	} else {
		$gastito = Factory::getInstance()->getGastito($id);
		if ($gastito->consolidado()) {
			throw new FactoryExceptionCustomException('No puede editarse un gasto ya rendido');
		}
		if (($importe > $gastito->importe) && (($importe - $gastito->importe) > ($gastito->caja->importeEfectivoFinal + abs($gastito->caja->importeDescubierto)))) {
			throw new FactoryExceptionCustomException('La caja no tiene el efectivo suficiente para realizar la modificación');
		}
	}
	$gastito->fecha = $fecha;
	$gastito->importe = $importe;
	$gastito->personaGasto = Factory::getInstance()->getPersonaGasto($idPersonaGasto);
	$gastito->comprobante = $comprobante;
	$gastito->observaciones = $observaciones;
	$gastito->guardar();

	Html::jsonSuccess('El gasto se ' . (empty($id) ? 'agregó' : 'editó') . ' correctamente', array('id' => $gastito->id, 'fechaAlta' => Funciones::hoy()));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('No tiene permiso para cargar gastos en la caja indicada');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar agregar el gasto "' . $nombre . '"');
}

?>
<?php } ?>