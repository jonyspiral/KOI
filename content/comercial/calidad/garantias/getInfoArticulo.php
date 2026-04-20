<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/calidad/garantias/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');

try {
	if (!$idArticulo) {
		throw new FactoryExceptionCustomException('Debe indicarse el artículo');
	}

	$articulo = Factory::getInstance()->getArticulo($idArticulo);

	Html::jsonEncode('', array(
		'rangoTalle' => $articulo->rangoTalle->posicion
	));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>