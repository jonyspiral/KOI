<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_debito/reimpresion/editar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$letra = Funciones::get('letra');

try {
	$ndb = Factory::getInstance()->getNotaDeDebito($empresa, $puntoDeVenta, 'NDB', $numero, $letra);
	if (!isset($ndb->cae))
		throw new FactoryExceptionCustomException('No se puede enviar por mail una nota de débito que aún no tiene CAE');
	if (!PHPMailer::ValidateAddress($ndb->cliente->email))
		throw new FactoryExceptionCustomException('No se puede enviar la nota de débito porque el cliente no tiene email o es incorrecto');

	//Envío la nota de débito por mail al cliente!
	$asunto = 'Spiral Shoes - Nota de débito Nº ' . $ndb->numeroComprobante;
	$para = array($ndb->cliente->vendedor->email);
	($ndb->empresa == 1) && $para[] = $ndb->cliente->email;
	//Le tengo que mandar el PDF adjunto!
	$pdfPath = $ndb->crear();
	$cuerpo = 'Se adjunta el comprobante electrónico correspondiente a la nota de débito Nº ' . $ndb->numeroComprobante . '. Muchas gracias. ';
	Email::enviar(
		 array(
			 'para' => $para,
			 'asunto' => $asunto,
			 'contenido' => $cuerpo,
			 'adjuntos' => array($pdfPath)
		 )
	);

	//Guardo en la base que ya se envió el mail
	$ndb->mailEnviado = 'S';
	Factory::getInstance()->persistir($ndb);

	$arr['puntoDeVenta'] = $ndb->puntoDeVenta;
	$arr['nro'] = $ndb->numero;
	$arr['nroComprobante'] = $ndb->numeroComprobante;
	$arr['letra'] = $ndb->letra;
	Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La nota de débito que intentó enviar por mail no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar enviar la nota de débito por mail');
}

?>
<?php } ?>