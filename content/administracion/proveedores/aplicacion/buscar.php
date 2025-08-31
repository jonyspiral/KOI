<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/aplicacion/buscar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$idProveedor = Funciones::get('proveedor');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$modo = Funciones::get('modo');

function armarJsonHijas($hijas) {
	$return = array();
	foreach ($hijas as $hija) {
		/** @var DocumentoProveedorHija $hija */
		$return[] = array(
			'id'			=> $hija->id,
			'fecha_debe'	=> $hija->madre->fecha,
			'fecha_haber'	=> $hija->documentoCancelatorio->fecha,
			'debe'			=> $hija->madre,
			'haber'			=> $hija->documentoCancelatorio,
			'importe'		=> $hija->importe
		);
	}
	return $return;
}

try {
	$where = '';
	$order = '';
	if (is_null($idProveedor)) {
		throw new FactoryExceptionCustomException('Debe elegir al menos el proveedor a aplicar');
	}
	$proveedor = Factory::getInstance()->getProveedor($idProveedor);
	if ($modo == '2') {
		//Desaplicar
		$where = Funciones::strFechas($desde, $hasta, 'fecha_debe') . ' AND ';
		$where .= Funciones::strFechas($desde, $hasta, 'fecha_haber') . ' AND ';
		$where = Funciones::reemplazar(' AND  AND ', ' AND ', $where);
		$order .= ' ORDER BY fecha_debe ASC, fecha_haber ASC';
	} else {
		//Aplicar
		$where = Funciones::strFechas($desde, $hasta, 'fecha') . ' AND ';
		$where .= 'importe_pendiente > 0 AND ';
		$order .= ' ORDER BY fecha ASC';
	}
	$where .= 'factura_gastos = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= 'cod_proveedor = ' . Datos::objectToDB($proveedor->id) . ' ';
	$where = trim($where, ' AND ');

	$return = array();
	if ($modo == '2') {
		//Desaplicar
		$listaHijas = Factory::getInstance()->getListObject('DocumentoProveedorHija', $where . $order);
		if (count($listaHijas) > 0) {
			$return = array('hijas' => armarJsonHijas($listaHijas));
		}
	} else {
		//Aplicar
		$listaDebe = Factory::getInstance()->getListObject('DocumentoProveedorAplicacionDebe', $where . $order);
		$listaHaber = Factory::getInstance()->getListObject('DocumentoProveedorAplicacionHaber', $where . $order);
		if (count($listaDebe) > 0 || count($listaHaber) > 0) {
			$return = array('debe' => $listaDebe, 'haber' => $listaHaber);
		}
	}

	Html::jsonEncode('', $return);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>