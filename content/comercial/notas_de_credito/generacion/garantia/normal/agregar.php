<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/garantia/normal/agregar/')) { ?>
<?php

$idGarantia = Funciones::post('idGarantia');
$cantidades = Funciones::post('cantidades');

try {
	if (!$idGarantia) {
		throw new FactoryExceptionCustomException('Deberá indicarse el número de garantía que se desea aprobar');
	}

	$garantia = Factory::getInstance()->getGarantia($idGarantia);
	if (!is_null($garantia->solucionNcr)) {
		throw new FactoryExceptionCustomException('La garantía ya fue resuelta. Por favor recargue la página');
	}
	$garantia->finalizar('comercial/notas_de_credito/generacion/garantia/normal/agregar/', true, $cantidades);

	Html::jsonSuccess('La garantía fue aprobada y se ha creado correctamente la nota de crédito');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar aprobar la garantía y generar la nota de crédito');
}

?>
<?php } ?>