<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/panel_de_control/editar/')) { ?>
<?php

$idCheque = Funciones::post('idCheque');
$numero = Funciones::formatearNumeroCheque(Funciones::post('numero'));
$nombreLibrador = Funciones::post('nombreLibrador');
$cuitLibrador = Funciones::post('cuitLibrador');
$fechaEmision = Funciones::post('fechaEmision');
$fechaVencimiento = Funciones::post('fechaVencimiento');
$noALaOrden = Funciones::post('noALaOrden');
$cruzado = Funciones::post('cruzado');

function jsonCheque(Cheque $cheque) {
	$json = array();
	$json['idCheque'] = $cheque->id;
	$json['nombreBanco'] = $cheque->banco->nombre;
	$json['cliente'] = $cheque->esDeCliente() ? $cheque->cliente->razonSocial : '';
	$json['nombreCuenta'] = $cheque->cuentaBancaria->nombre;
	$json['numero'] = $cheque->numero;
	$json['nombreLibrador'] = $cheque->libradorNombre;
	$json['cuitLibrador'] = $cheque->libradorCuit;
	$json['importe'] = $cheque->importe;
	$json['noALaOrden'] = $cheque->noALaOrden;
	$json['cruzado'] = $cheque->cruzado;
	$json['fechaEmision'] = $cheque->fechaEmision;
	$json['fechaVencimiento'] = $cheque->fechaVencimiento;
	$json['propio'] = ($cheque->esPropio() ? '1' : '0');
	$json['entregado'] = ($cheque->concluido() ? '1' : '0');
	return $json;
}

try {
	if (!isset($idCheque))
		throw new FactoryExceptionRegistroNoExistente();
	
	$cheque = Factory::getInstance()->getCheque($idCheque);

	if($cheque->esPropio()){
		if(is_null($fechaEmision) || is_null($fechaVencimiento) || is_null($noALaOrden) || is_null($cruzado))
			throw new FactoryExceptionCustomException('Todos los campos son obligatorios.');
	} else {
		if(is_null($numero) || is_null($nombreLibrador) || is_null($cuitLibrador) || is_null($fechaEmision) ||
		   is_null($noALaOrden) || is_null($numero) || is_null($cruzado))
			throw new FactoryExceptionCustomException('Todos los campos son obligatorios.');
	}

	if(Funciones::esFechaMenor($fechaVencimiento, $fechaEmision))
		throw new FactoryExceptionCustomException('La fecha de vencimiento no puede ser menor a la fecha de emisión.');

	if (!$cheque->esPropio()) {
		$cheque->numero = $numero;
		$cheque->libradorNombre = $nombreLibrador;
		$cheque->libradorCuit = $cuitLibrador;
	}
	$cheque->fechaEmision = $fechaEmision;
	$cheque->fechaVencimiento = $fechaVencimiento;
	$cheque->noALaOrden = $noALaOrden;
	$cheque->cruzado = $cruzado;

	$cheque->guardar();
	Html::jsonSuccess('El cheque fue editado correctamente', jsonCheque($cheque));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El cheque que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el cheque');
}
?>
<?php } ?>