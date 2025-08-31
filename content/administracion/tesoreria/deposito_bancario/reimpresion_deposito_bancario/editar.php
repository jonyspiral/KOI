<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/reimpresion_deposito_bancario/editar/')) { ?>
<?php

$numero = Funciones::post('numero');
$empresa = Funciones::session('empresa');
$observaciones = Funciones::post('observaciones');

try {
	if (!isset($numero))
		throw new FactoryExceptionRegistroNoExistente();
	
	$depositoBancarioCabecera = Factory::getInstance()->getDepositoBancarioCabecera($numero, $empresa);

	$depositoBancarioCabecera->observaciones = $observaciones;

	$depositoBancarioCabecera->update();
	Html::jsonSuccess(($depositoBancarioCabecera->esVentaCheque() ? 'La venta de cheques fue editada' : 'El deposito bancario fue editado') . ' correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError(($depositoBancarioCabecera->esVentaCheque() ? 'La venta de cheques' : 'El deposito bancario') . ' que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar' . ($depositoBancarioCabecera->esVentaCheque() ? 'la venta de cheques' : 'el deposito bancario'));
}
?>
<?php } ?>