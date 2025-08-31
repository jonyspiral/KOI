<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/auditoria/calificacion_clientes/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');

try {
	$strFecha = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha');

	$where .= ($idCliente ? 'cod_cliente = ' . Datos::objectToDB($idCliente) . ' AND ' : '');
	$where .= ($strFecha ? $strFecha . ' AND ' : '');
	$where = trim($where, ' AND ');
	$where = ($where ? $where : '1=1');
	$orderBy = ' ORDER BY fecha DESC';

	$cambiosSituacionCliente = Factory::getInstance()->getListObject('CambiosSituacionCliente', $where, 500);

	if (empty($cambiosSituacionCliente)) {
		throw new FactoryExceptionCustomException('No existen cambios en la situación de los clientes para los filtros especificados');
	}

	$tabla = new HtmlTable(array('cantRows' => count($cambiosSituacionCliente), 'cantCols' => 6, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'Fecha cambio', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'Hora cambio', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'Cliente', 'width' => 45),
			   array('content' => 'Calificación<br>anterior', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'Calificación<br>nueva', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'Usuario', 'dataType' => 'Center', 'width' => 15)
		  )
	);

	$i = 0;
	foreach($cambiosSituacionCliente as $cambioSituacionCliente) {
		/** @var CambiosSituacionCliente $cambioSituacionCliente */

		$cells[$i][0]->content = $cambioSituacionCliente->fecha;
		$cells[$i][1]->content = $cambioSituacionCliente->hora;
		$cells[$i][2]->content = $cambioSituacionCliente->cliente->getIdNombre();
		$cells[$i][3]->content = ($cambioSituacionCliente->calificacionAnterior ? $cambioSituacionCliente->calificacionAnterior : '-');
		$cells[$i][3]->class .= 'c_' . $cambioSituacionCliente->calificacionAnterior;
		$cells[$i][4]->content = $cambioSituacionCliente->calificacionNueva;
		$cells[$i][4]->class .= 'c_' . $cambioSituacionCliente->calificacionNueva;
		$cells[$i][5]->content = $cambioSituacionCliente->usuario->id;

		$i++;
	}

	$tabla->create();
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>