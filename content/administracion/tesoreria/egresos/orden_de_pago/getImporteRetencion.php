<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/orden_de_pago/buscar/')) { ?>
<?php

$ayuda = Funciones::get('ayuda') == '1';
$netoOriginal = Funciones::toFloat(Funciones::get('neto'));
$idProveedor = Funciones::get('idProveedor');

try {
	$proveedor = Factory::getInstance()->getProveedor($idProveedor);
	$neto = $netoOriginal;
	$bruto = $neto;
	$retencion = $proveedor->calcularRetencion($netoOriginal, $ayuda);
	if ($retencion) {
		($ayuda) && $neto = ($neto - $retencion);
		(!$ayuda) && $bruto = ($neto + $retencion);
	}

	$neto = Funciones::toFloat($neto, 2);
	$retencion = Funciones::toFloat($retencion, 2);
	$bruto = Funciones::toFloat($bruto, 2);

	Html::jsonEncode('', array(
							  'neto'		=> $neto,
							  'retencion'	=> $retencion,
							  'bruto'		=> $bruto
						 )
	);
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>