<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/reportes/pendientes/editar/')) { ?>
<?php

$idOrden = Funciones::post('idOrden');
$numeroItem = Funciones::post('numeroItem');
$fecha = Funciones::post('fecha');

try {
	if (empty($fecha)) {
		throw new FactoryExceptionCustomException('Debe especificar una nueva fecha válida');
	}
	$item = Factory::getInstance()->getOrdenDeCompraItem($idOrden, $numeroItem);
	$item->fechaEntrega = $fecha;
	$item->guardar();

	Html::jsonEncode('', $item);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
