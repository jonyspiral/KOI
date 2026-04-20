<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/chequera/agregar/')) { ?>
<?php


$idCuentaBancaria = Funciones::post('idCuentaBancaria');
$numeroInicio = Funciones::post('numeroInicio');
$numeroFin = Funciones::post('numeroFin');
$fecha = Funciones::post('fecha');

function generarArrayChequera($numeroInicio, $numeroFin, $serie = ''){
	$array = array();
	$serie = empty($serie) ? $serie : '-' . $serie;
	$cantidadEntradas = $numeroFin - $numeroInicio;

	for($i = 0; $i <= $cantidadEntradas; $i++){
		$chequera = Factory::getInstance()->getChequeraItem();
		$numero = Funciones::padLeft($numeroInicio + $i, 8, 0);
		$chequera->numero = $numero . $serie;
		$array[] = $chequera;
	}

	return $array;
}

try {
	if(is_null($idCuentaBancaria) || is_null($numeroInicio) || is_null($numeroFin) || is_null($fecha)){
		throw new FactoryExceptionCustomException('Todos los campos son obligatorios.');
	}

	$chequera = Factory::getInstance()->getChequera();
	$chequera->cuentaBancaria = Factory::getInstance()->getCuentaBancaria($idCuentaBancaria);
	$chequera->fecha = $fecha;

	$arrayNumeroInicio = explode('-', $numeroInicio);
	$arrayNumeroFin = explode('-', $numeroFin);
	$sumaTamañoArrays = count($arrayNumeroInicio) + count($arrayNumeroFin);

	if($arrayNumeroInicio[0] == 0){
		throw new FactoryExceptionCustomException('El número de inicio no puede ser cero.');
	}
	if(!is_numeric($arrayNumeroInicio[0]) || !is_numeric($arrayNumeroFin[0])){
		throw new FactoryExceptionCustomException('No se reconoce el formato ingresado para el número de cheque.');
	}
	if($arrayNumeroInicio[0] >= $arrayNumeroFin[0]){
		throw new FactoryExceptionCustomException('El número de fin no puede ser menor o igual al número de inicio.');
	}

	switch ($sumaTamañoArrays) {
		case 2:
			$arrayChequeraItem = generarArrayChequera($arrayNumeroInicio[0], $arrayNumeroFin[0]);
			break;
		case 4:
			$igualdadDeSerie = strtoupper($arrayNumeroInicio[1]) != strtoupper($arrayNumeroFin[1]);
			$longitudDeSerie = strlen($arrayNumeroInicio[1]) > 2 && strlen($arrayNumeroFin[1]) > 2;
			$esCaracterSerie = !ctype_alpha($arrayNumeroInicio[1]) && !ctype_alpha($arrayNumeroFin[1]);
			if($igualdadDeSerie || $longitudDeSerie || $esCaracterSerie){
				throw new FactoryExceptionCustomException('La serie ingresada es inconsistente.');
			}
			$arrayChequeraItem = generarArrayChequera($arrayNumeroInicio[0], $arrayNumeroFin[0], strtoupper($arrayNumeroInicio[1]));
			break;
		default:
			throw new FactoryExceptionCustomException('No se reconoce el formato ingresado para el número de cheque.');
	}

	$chequera->numeroInicio = $numeroInicio;
	$chequera->numeroFin = $numeroFin;
	$chequera->detalle = $arrayChequeraItem;
	$chequera->guardar();

	Html::jsonSuccess('Se generó correctamente la chequera Nº ' . $chequera->id . '.');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError('Alguno o todos los números del rango ya existen para la cuenta bancaria especificada.');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar la chequera.');
}

?>
<?php } ?>