<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/reimpresion_recibos/buscar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');

try {
	$recibo = Factory::getInstance()->getRecibo($numero, $empresa);
	if(empty($recibo->idCliente))
		throw new FactoryExceptionCustomException('No se puede enviar por mail un recibo de otros ingresos');

	if (!PHPMailer::ValidateAddress($recibo->cliente->email))
		throw new FactoryExceptionCustomException('No se puede enviar el recibo porque el cliente no tiene email o es incorrecto');

	//Envío la factura por mail al cliente!
	$asunto = 'Spiral Shoes - Recibo Nº ' . $recibo->numero . ' - ' . Funciones::formatearMoneda($recibo->importeTotal);
	$para = array();
	($recibo->empresa == 1) && $para[] = $recibo->cliente->email;
	//Le tengo que mandar el PDF adjunto!
	$pdfPath = $recibo->crear();
	$cuerpo = 'Se adjunta el recibo Nº ' . $recibo->numero . '. Muchas gracias. ';
	Email::enviar(
		 array(
			 'para' => $para,
			 'asunto' => $asunto,
			 'contenido' => $cuerpo,
			 'adjuntos' => array($pdfPath)
		 )
	);

	//Guardo en la base que ya se envió el mail
	$recibo->mailEnviado = 'S';
	$recibo->update();

	Html::jsonSuccess('', array('numero' => $recibo->numero));
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El recibo que intentó enviar por mail no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar enviar el recibo por mail');
}

?>
<?php } ?>