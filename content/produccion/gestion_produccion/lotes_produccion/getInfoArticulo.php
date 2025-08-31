<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/gestion_produccion/lotes_produccion/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$idPatron = Funciones::get('idPatron');

try {
	if (!$idArticulo || !$idColor || !$idPatron) {
		throw new FactoryExceptionCustomException('Debe completar todos los campos (artículo, color y patrón)');
	}
    $patron = Factory::getInstance()->getPatron($idArticulo, $idColor, $idPatron);
    $patron->colorPorArticulo;
    $patron->articulo->rangoTalle;
    $patron->articulo->curvasDeProduccion;

	Html::jsonEncode('', $patron);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>