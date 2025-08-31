<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reimpresion_ordenes_de_pago/buscar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');

try {
	$ordenDePago = Factory::getInstance()->getOrdenDePago($numero, $empresa);

	if($ordenDePago->anulado == 'S')
		throw new FactoryExceptionCustomException('No se puede enviar por mail una orden de pago anulada');

	if(empty($ordenDePago->idProveedor))
		throw new FactoryExceptionCustomException('No se puede enviar por mail una orden de pago autónoma');

	if (!PHPMailer::ValidateAddress($ordenDePago->proveedor->email))
		throw new FactoryExceptionCustomException('No se puede enviar la orden de pago porque el proveedor no tiene email o es incorrecto');

	//Envío la factura por mail al cliente!
	$mail = new PHPMailer();
	$asunto = 'Spiral Shoes - Orden de pago Nº ' . $ordenDePago->numero . ' - ' . Funciones::formatearMoneda($ordenDePago->importeTotal);
	$para = array();
	($ordenDePago->empresa == 1) && $para[] = $ordenDePago->proveedor->email;
	//Le tengo que mandar el PDF adjunto!
	$pdfPath = $ordenDePago->crear();
	$cuerpo = 'Se adjunta la orden de pago Nº ' . $ordenDePago->numero . '. Muchas gracias. ';
	Email::enviar(
		 array(
			 'para' => $para,
			 'asunto' => $asunto,
			 'contenido' => $cuerpo,
			 'adjuntos' => array($pdfPath)
		 )
	);

	//Guardo en la base que ya se envió el mail
	$ordenDePago->mailEnviado = 'S';
	$ordenDePago->update();

	Html::jsonSuccess('', array('numero' => $ordenDePago->numero));
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La orden de pago que intentó enviar por mail no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar enviar la orden de pago por mail');
}

?>
<?php } ?>