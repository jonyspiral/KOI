<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/reimpresion/editar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$letra = Funciones::get('letra');

try {
	$ncr = Factory::getInstance()->getNotaDeCredito($empresa, $puntoDeVenta, 'NCR', $numero, $letra);
	if (!isset($ncr->cae))
		throw new FactoryExceptionCustomException('No se puede enviar por mail una nota de crédito que aún no tiene CAE');
	if (!PHPMailer::ValidateAddress($ncr->cliente->email))
		throw new FactoryExceptionCustomException('No se puede enviar la nota de crédito porque el cliente no tiene email o es incorrecto');

	//Envío la nota de crédito por mail al cliente!
	$asunto = 'Spiral Shoes - Nota de crédito Nº ' . $ncr->numeroComprobante;
	$para = array($ncr->cliente->vendedor->email);
	($ncr->empresa == 1) && $para[] = $ncr->cliente->email;
	//Le tengo que mandar el PDF adjunto!
	$pdfPath = $ncr->crear();
	$cuerpo = 'Se adjunta el comprobante electrónico correspondiente a la nota de crédito Nº ' . $ncr->numeroComprobante . '. Muchas gracias. ';
	Email::enviar(
		 array(
			 'para' => $para,
			 'asunto' => $asunto,
			 'contenido' => $cuerpo,
			 'adjuntos' => array($pdfPath)
		 )
	);

	//Guardo en la base que ya se envió el mail
	$ncr->mailEnviado = 'S';
	Factory::getInstance()->persistir($ncr);

	$arr['puntoDeVenta'] = $ncr->puntoDeVenta;
	$arr['nro'] = $ncr->numero;
	$arr['nroComprobante'] = $ncr->numeroComprobante;
	$arr['letra'] = $ncr->letra;
	Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La nota de crédito que intentó enviar por mail no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar enviar la nota de crédito por mail');
}

?>
<?php } ?>