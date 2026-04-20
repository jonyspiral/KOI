<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/garantia/ecommerce/borrar/')) { ?>
<?php

$idGarantia = Funciones::post('idGarantia');

try {
	if (!$idGarantia) {
		throw new FactoryExceptionCustomException('Deberá indicarse el número de garantía que se desea rechazar');
	}
	$garantia = Factory::getInstance()->getGarantia($idGarantia);
	if (!is_null($garantia->solucionNcr)) {
		throw new FactoryExceptionCustomException('La garantía ya fue resuelta. Por favor recargue la página');
	}
	$garantia->finalizar('comercial/notas_de_credito/generacion/garantia/ecommerce/borrar/', false);

	Html::jsonSuccess('La nota de crédito por la garantía fue rechazada correctamente. Sin embargo, se realizaron los correspondientes movimientos de stock según fue clasificado previamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar rechazar la nota de crédito por la garantía');
}

?>
<?php } ?>