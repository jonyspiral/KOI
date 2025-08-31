<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/facturas/reimpresion/editar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$letra = Funciones::get('letra');

try {
	$factura = Factory::getInstance()->getFactura($empresa, $puntoDeVenta, 'FAC', $numero, $letra);
	$error = $factura->obtenerCae(); //No hace falta persistir

	$arr['puntoDeVenta'] = $factura->puntoDeVenta;
	$arr['nro'] = $factura->numero;
	$arr['letra'] = $factura->letra;

	if ($error !== true)
		Html::jsonAlert($error, $arr);
	else
		Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La factura que intentó obtener el CAE no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar obtener el CAE: ' . $ex->getMessage());
}

?>
<?php } ?>