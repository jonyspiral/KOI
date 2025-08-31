<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/aplicacion/editar/')) { ?>
<?php

//Esto corresponde al aplicar automático

$empresa = Funciones::session('empresa');
$idCliente = Funciones::post('cliente');
$desde = Funciones::post('desde');
$hasta = Funciones::post('hasta');

try {
	$where = '';
	if (is_null($idCliente)) {
		throw new FactoryExceptionCustomException('Ocurrió un error el intentar asignar los documentos (no se obtuvo correctamente el cliente). Por favor recargue la página e inténtelo nuevamente');
	}
	$cliente = Factory::getInstance()->getCliente($idCliente);
	$where = Funciones::strFechas($desde, $hasta, 'fecha') . ' AND ';
	$where .= 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= 'cod_cliente = ' . Datos::objectToDB($cliente->id) . ' AND ';
	$where .= 'importe_pendiente > 0';
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha ASC';

	$ld = Factory::getInstance()->getListObject('DocumentoAplicacionDebe', $where . $order);
	$lh = Factory::getInstance()->getListObject('DocumentoAplicacionHaber', $where . $order);

	$j = 0;
	$k = 0;
	while($j < count($ld) && $k < count($lh)) {
		$pendDebe = $ld[$j]->importePendiente;
		$pendHaber = $lh[$k]->importePendiente;
		$haberMayorDebe = ($pendHaber >= $pendDebe);

		$ld[$j]->aplicar($lh[$k]);

		$j += ($pendHaber >= $pendDebe ? 1 : 0);
		$k += ($pendDebe >= $pendHaber ? 1 : 0);
	}
	$ld = array_splice($ld, $j);
	$lh = array_splice($lh, $k);

	$return = array();
	if (count($ld) > 0 || count($lh) > 0) {
		$return = array('debe' => $ld, 'haber' => $lh);
	}

	Html::jsonEncode('', $return);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError('Alguno de los documentos no existe. Por favor actualice la lista');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar aplicar los documentos');
}

?>
<?php } ?>