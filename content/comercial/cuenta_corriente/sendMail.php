<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('comercial/cuenta_corriente/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$empresa = Funciones::get('empresa');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');

try {
	if (!isset($idCliente)) {
		throw new Exception('Debe elegir un cliente');
	}
	$cliente = Factory::getInstance()->getCliente($idCliente);
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Cuenta_corriente_' . $cliente->id . '_' . $razonSocial . (isset($empresa) ? '_' . $empresa : '');
	$html2pdf->tituloReporte = 'Cuenta corriente';
	$html2pdf->datosCabecera = array('Cliente' => '[' . $cliente->id . '] ' . $cliente->razonSocial, 'E' => (isset($empresa) ? $empresa : '-'), 'F. desde' => (isset($desde) ? $desde : '-'), 'F. hasta' => (isset($hasta) ? $hasta : '-'));
	$html2pdf->create();

	if ($empresa == 1 && !PHPMailer::ValidateAddress($cliente->email))
		throw new FactoryExceptionCustomException('No se puede enviar la factura porque el cliente no tiene email o es incorrecto');
	if (!PHPMailer::ValidateAddress($cliente->vendedor->email))
		throw new FactoryExceptionCustomException('No se puede enviar la factura porque el vendedor no tiene email o es incorrecto');

	//Envío la cuenta corriente por mail al cliente y al vendedor
	$asunto = 'Spiral Shoes - Cuenta corriente' . ($desde ? ' del ' . $desde : '') . ($hasta ? ' al ' . $hasta : '');
	$para = array($cliente->vendedor->email);
	($empresa == 1) && $para[] = $cliente->email;
	//Le tengo que mandar el PDF adjunto!
	$pdfPath = $html2pdf->pdfPath;
	$cuerpo = 'Se adjunta el documento de cuenta corriente' . ($desde ? ' del ' . $desde : '') . ($hasta ? ' al ' . $hasta : '') . '.<br><br>';
	$cuerpo .= 'Aprovechamos para recordarles que la condición de pago es 15 días promedio.<br>Las facturas deberán ser canceladas a los 15 días emitida la factura.<br>';
	$cuerpo .= 'Los plazos de los cheques tienen que ser a 30-60-90 días.<br>De no haber recibido el pago a los 15 días de emitida la factura queda suspendida la cuenta.<br>';
	$cuerpo .= 'Si los plazos de pago exceden los 15 días promedio desde la fecha de factura se generaran débitos por intereses punitorios.<br>Muchas gracias.';
	Email::enviar(
		 array(
			 'para' => $para,
			 'asunto' => $asunto,
			 'contenido' => $cuerpo,
			 'adjuntos' => array($pdfPath)
		 )
	);

	Html::jsonSuccess('La cuenta corriente se envió por mail correctamente');
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La factura que intentó enviar por mail no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar enviar la factura por mail');
}

?>
<?php } ?>