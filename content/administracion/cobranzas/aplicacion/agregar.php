<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/aplicacion/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');

$debePuntoVenta = Funciones::post('debePuntoVenta');
$debeTipo = Funciones::post('debeTipo');
$debeNumero = Funciones::post('debeNumero');
$debeLetra = Funciones::post('debeLetra');

$haberPuntoVenta = Funciones::post('haberPuntoVenta');
$haberTipo = Funciones::post('haberTipo');
$haberNumero = Funciones::post('haberNumero');
$haberLetra = Funciones::post('haberLetra');

try {
	$debe = Factory::getInstance()->getDocumentoAplicacionDebe($empresa, $debePuntoVenta, $debeTipo, $debeNumero, $debeLetra);
	$haber = Factory::getInstance()->getDocumentoAplicacionHaber($empresa, $haberPuntoVenta, $haberTipo, $haberNumero, $haberLetra);

	$debe->aplicar($haber);

	$return = array(
		'debe'	=> $debe,
		'haber'	=> $haber
	);

	Html::jsonEncode('', $return);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError('Alguno de los documentos no existe. Por favor actualice la lista');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar aplicar los documentos');
}

?>
<?php } ?>