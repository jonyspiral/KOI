<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/generacion/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$productiva = snf(Funciones::get('productiva'));
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');

try {
	if (empty($idProveedor)) {
		throw new FactoryExceptionCustomException('Debe especificar un proveedor');
	}

	/*$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_alta');
	$where = 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ';
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_alta ASC, cod_presupuesto ASC';*/

	$explosiones = Factory::getInstance()->getListObject('ExplosionLoteTemp', $where . $order);

	if (count($explosiones) == 0) {
		throw new FactoryExceptionCustomException('No existen explosiones con los filtros especificados');
	}

	$arr = array();
	foreach ($explosiones as $explosion) {
		/** @var ExplosionLoteTemp $explosion */
		$item = array();


		$arr[] = $item;
	}

	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>