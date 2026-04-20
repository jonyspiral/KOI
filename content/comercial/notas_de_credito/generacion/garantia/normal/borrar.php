<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/garantia/normal/borrar/')) { ?>
<?php

$idGarantia = Funciones::post('idGarantia');

try {
	if (!$idGarantia) {
		throw new FactoryExceptionCustomException('Deberá indicarse el número de garantía que se desea rechazar');
	}
	$garantia = Factory::getInstance()->getGarantia($idGarantia);
	if ($garantia->clasificada != 'S') {
		throw new FactoryExceptionCustomException('La garantía no está clasificada aún');
	}
	if (!is_null($garantia->solucionNcr)) {
		throw new FactoryExceptionCustomException('La garantía ya fue resuelta. Por favor recargue la página');
	}
	$garantia->finalizar('comercial/notas_de_credito/generacion/garantia/normal/borrar/', false);

	Html::jsonSuccess('La garantía fue rechazada y no se generó nota de crédito');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar rechazar la garantía y la nota de crédito');
}

?>
<?php } ?>