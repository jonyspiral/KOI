<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/vendedores/reimpresion_documentos/editar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$tipoDocumento = Funciones::get('tipoDocumento');
$letra = Funciones::get('letra');

try {
	$documento = Factory::getInstance()->getDocumento($empresa, $puntoDeVenta, $tipoDocumento, $numero, $letra);
	if (!isset($documento->cae))
		throw new FactoryExceptionCustomException('No se puede enviar por mail un documento que aún no tiene CAE');
	if (!PHPMailer::ValidateAddress($documento->cliente->email))
		throw new FactoryExceptionCustomException('No se puede enviar el documento porque el cliente no tiene email o es incorrecto');

	//Envío el documento por mail al cliente!
	$asunto = 'Spiral Shoes - Documento ' . $documento->tipoDocumento . ' Nº ' . $documento->numeroComprobante;
	if (!PHPMailer::ValidateAddress($documento->cliente->vendedor->email))
		throw new FactoryExceptionCustomException('No se puede enviar el documento porque el vendedor no tiene email o es incorrecto');
	$para = array($documento->cliente->vendedor->email);
	($documento->empresa == 1) && $para[] = $documento->cliente->email;
	//Le tengo que mandar el PDF adjunto!
	$pdfPath = $documento->crear();
	$cuerpo = 'Se adjunta el comprobante electrónico correspondiente a el documento ' . $documento->tipoDocumento . ' Nº ' . $documento->numeroComprobante . '. Muchas gracias. ';
	Email::enviar(
		 array(
			 'para' => $para,
			 'asunto' => $asunto,
			 'contenido' => $cuerpo,
			 'adjuntos' => array($pdfPath)
		 )
	);

	//Guardo en la base que ya se envió el mail
	$documento->mailEnviado = 'S';
	Factory::getInstance()->persistir($documento);

	$arr['puntoDeVenta'] = $documento->puntoDeVenta;
	$arr['nro'] = $documento->numero;
	$arr['nroComprobante'] = $documento->numeroComprobante;
	$arr['tipoDocumento'] = $documento->tipoDocumento;
	$arr['letra'] = $documento->letra;
	Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El documento que intentó enviar por mail no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar enviar el documento por mail');
}

?>
<?php } ?>