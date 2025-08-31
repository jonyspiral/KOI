<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_debito/reimpresion/editar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$letra = Funciones::get('letra');

try {
	$ndb = Factory::getInstance()->getNotaDeDebito($empresa, $puntoDeVenta, 'NDB', $numero, $letra);
	$error = $ndb->obtenerCae(); //No hace falta persistir

	$arr['puntoDeVenta'] = $ndb->puntoDeVenta;
	$arr['nro'] = $ndb->numero;
	$arr['letra'] = $ndb->letra;

	if ($error !== true)
		Html::jsonAlert($error, $arr);
	else
		Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La nota de débito que intentó obtener el CAE no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar obtener el CAE: ' . $ex->getMessage());
}

?>
<?php } ?>