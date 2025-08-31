<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/devolucion/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$where = 'cod_cliente = ' . Datos::objectToDB($idCliente);
	$where .= ' AND cod_articulo = ' . Datos::objectToDB($idArticulo);
	$where .= ' AND cod_color_articulo = ' . Datos::objectToDB($idColor);
	$order = ' ORDER BY fecha_alta DESC';
	$items = Factory::getInstance()->getListObject('DespachoItem', $where . $order, 3);
	$color = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);
	$arrayPrecios = array();
	foreach($items as $item) {
		$arrayPrecios[Funciones::toString($item->precioUnitario)] = $item->despachoNumero . '_' . $item->numeroDeItem;
	}

	$json = array(
		'idCliente' => $idCliente,
		'idAlmacen' => $idAlmacen,
		'color' => $color->expand(),
		'articulo' => $color->articulo->expand(),
		'precios' => $arrayPrecios
	);
	Html::jsonEncode('', $json);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El artículo-color que intentó buscar no existe o no está disponible');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>