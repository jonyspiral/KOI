<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/remitos/generacion/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$remitos = Funciones::post('remitos');

try {
	$i = 0;
	$j = 0;
	$k = 0;
	$l = 0;
	$motivo = '';
	foreach ($remitos as $rem) {
		$datos = array(
			'empresa' => $empresa,
			'idCliente' => $rem['cliente'],
			'idSucursal' => $rem['sucursal'],
			'observaciones' => $rem['observaciones'],
			'bultos' => $rem['bultos'],
			'detalles' => $rem['despachos']
		);

		try {
			$remito = Remito::remitir($datos, 'comercial/remitos/generacion/agregar/');
			$i++;
		} catch (ExceptionRemitoExcedeArticulos $ex) {
			$j++;
		} catch (ExceptionRemitoObservacionObligatoria $ex) {
			$k++;
		} catch (FactoryExceptionCustomException $ex) {
			$l++;
			$motivo = $ex->getMessage();
		}
	}
	$texto = 'Se generaron correctamente ' . $i . ' remito(s). ';
	if ($j > 0 || $k > 0 || $l > 0) {
		$texto .= 'Ocurrieron ' . ($j + $k + $l) . ' error(es). Motivo(s): ';
		if ($j > 0) $texto .= $j . ' remito(s) tienen más de ' . Remito::CANT_MAX_DETALLE . ' artículos. ';
		if ($k > 0) $texto .= $k . ' remito(s) no tienen observación obligatoria. ';
		if ($l > 0) $texto .= $l . ' remito(s) produjeron otro error (' . $motivo . ').';
		($i) ? Html::jsonInfo($texto) : Html::jsonError($texto);
	} else {
		Html::jsonSuccess($texto);
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el/los remito(s)');
}

?>
<?php } ?>