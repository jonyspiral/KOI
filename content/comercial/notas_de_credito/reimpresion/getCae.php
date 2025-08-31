<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/reimpresion/editar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::post('puntoDeVenta');
$numero = Funciones::post('numero');
$letra = Funciones::post('letra');
$confirmar = (Funciones::get('confirmar') == '1');

try {
	$ncr = Factory::getInstance()->getNotaDeCredito($empresa, $puntoDeVenta, 'NCR', $numero, $letra);

	$mensajeConfirmacion = $ncr->comprobacionConfirmacionCae();

	if ($confirmar || !$mensajeConfirmacion) {
		$error = $ncr->obtenerCae(); //No hace falta persistir

		$arr['puntoDeVenta'] = $ncr->puntoDeVenta;
		$arr['nro'] = $ncr->numero;
		$arr['letra'] = $ncr->letra;

		if ($error !== true) {
			Html::jsonAlert($error, $arr);
		} else {
			Html::jsonSuccess('El CAE se ha obtenido correctamente', $arr);
		}
	} else {
		Html::jsonConfirm($mensajeConfirmacion, 'confirmar');
	}
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La nota de crédito que intentó obtener el CAE no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar obtener el CAE: ' . $ex->getMessage());
}

?>
<?php } ?>