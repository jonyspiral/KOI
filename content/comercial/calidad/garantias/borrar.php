<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/calidad/garantias/borrar/')) { ?>
<?php

$idGarantia = Funciones::post('idGarantia');

try {
	$garantia = Factory::getInstance()->getGarantia($idGarantia);
	if ($garantia->devuelta == 'S') {
		throw new FactoryExceptionCustomException('La garantía ya fue devuelta. Por favor recargue la página');
	}
	if ($garantia->anulado()) {
		throw new FactoryExceptionCustomException('La garantía está anulada. Por favor recargue la página');
	}
	$garantia->borrar()->notificar('comercial/calidad/garantias/borrar/');

	Html::jsonSuccess('La garantía fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La garantía que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la garantía');
}

?>
<?php } ?>