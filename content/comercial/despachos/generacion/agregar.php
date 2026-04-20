<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/despachos/generacion/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$despachos = Funciones::post('despachos');
$facturar = Funciones::post('facturar') == '1';
$remitir = Funciones::post('remitir') == '1';

function calcularCantidad($propuesto, $predespachado) {
	if ($propuesto <= $predespachado && $propuesto >= 0)
		return $propuesto;
	if ($propuesto >= $predespachado) //Esto puede pasar sólo si tocan JS, no va a pasar
		return $predespachado;
	return 0;
}

try {
	$i = 0;
	$j = 0;
	$k = 0;
	$l = 0;
	$m = 0;
	$motivo = '';
	$motivoFacturar = '';
	$errorFacturar = false;
	foreach ($despachos as $desp) {
		$datos = array(
			'empresa' => $empresa,
			'idCliente' => $desp['cliente'],
			'idSucursal' => $desp['sucursal'],
			'observaciones' => $desp['observaciones'],
			'predespachos' => $desp['predespachos']
		);

		try {
			Factory::getInstance()->beginTransaction();
			$despacho = Despacho::despachar($datos, 'comercial/despachos/generacion/agregar/');
			if ($facturar) {
				try {
					$despacho->facturar();
				} catch (Exception $ex) {
					$errorFacturar = true;
					throw $ex;
				}
				$m++;
			} elseif ($remitir) {
				try {
					$despacho->remitir();
				} catch (Exception $ex) {
					$errorFacturar = true;
					throw $ex;
				}
				$m++;
			}
			Factory::getInstance()->commitTransaction();
			$i++;
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			if ($errorFacturar) {
				$errorFacturar = false;
				$l++;
				$motivoFacturar .= $ex->getMessage() . '. ';
			} else {
				$k++;
				$motivo .= $ex->getMessage() . '. ';
			}
		}
	}

	$texto = 'Se generaron correctamente ' . $i . ' despachos.' . ($facturar ? ' Se crearon correctamente ' . $m . ' facturas.' : '') . '<br>';
	if ($j > 0 || $k > 0 || $l > 0) {
		$texto .= 'Ocurrieron ' . ($j + $k + $l) . ' error(es). Motivo(s):<br>';
		if ($j > 0) $texto .= $j . ' despacho(s) tienen más de ' . Despacho::CANT_MAX_DETALLE . ' artículos<br>';
		if ($k > 0) $texto .= $k . ' despacho(s) produjeron otro(s) error(es) (' . $motivo . ')<br>';
		if ($l > 0) $texto .= $l . ' despacho(s) no pudieron remitirse/facturarse (' . $motivoFacturar . ')<br>';
		($i) ? Html::jsonInfo($texto) : Html::jsonError($texto);
	} else {
		Html::jsonSuccess($texto);
	}
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el/los despacho(s)');
}

?>
<?php } ?>