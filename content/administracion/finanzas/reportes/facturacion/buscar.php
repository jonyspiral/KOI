<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/finanzas/reportes/facturacion/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');
$docFAC = (Funciones::get('docFAC') == 'true') ? true : false;
$docNCR = (Funciones::get('docNCR') == 'true') ? true : false;
$docNDB = (Funciones::get('docNDB') == 'true') ? true : false;
$cliente = Funciones::get('cliente');
$orderBy = Funciones::get('orderBy');
$tipoReporte = Funciones::get('tipoReporte');
$esXls = Funciones::get('esXls') == 1;

function buscar($where){
	$listaDocumentos = Factory::getInstance()->getArrayFromView('reporte_facturacion_v', $where);
	if(empty($listaDocumentos)) {
		throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
	}
	return $listaDocumentos;
}

try {
	//Validaciones
	if(!($docFAC || $docNDB || $docNCR)) {
		throw new FactoryExceptionCustomException('Debe seleccionar al menos un tipo de documento');
	}

	//Armo el where
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha', true, true, 800);
	$where .= (empty($cliente) ? '' : ' AND cod_cliente = ' . Datos::objectToDB($cliente));
	$where .= ($empresa != 1 && $empresa != 2) ? '' : ' AND empresa = ' . Datos::objectToDB($empresa);
	$where .= ' AND (';
	$where .=  ($docFAC ? 'tipo_documento = ' . Datos::objectToDB('FAC') . ' OR ' : '');
	$where .=  ($docNDB ? 'tipo_documento = ' . Datos::objectToDB('NDB') . ' OR ' : '');
	$where .=  ($docNCR ? 'tipo_documento = ' . Datos::objectToDB('NCR') . ' OR ' : '');
	$where = rtrim($where, ' OR ');
	$where .= ')';
	$orderBy && $order = ' ORDER BY ' . $orderBy;

	if ($tipoReporte == 'D') {
		$listaDocumentos = Factory::getInstance()->getArrayFromView('reporte_facturacion_v', $where . $order);
		if (empty($listaDocumentos)) {
			throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
		}
	}

	$fields = '	SUM(CASE tipo_documento WHEN ' . Datos::objectToDB('NDB') . ' THEN 0 ELSE pares END) pares,
				SUM(neto) neto,
				SUM(neto_ng) neto_ng,
				SUM(iva) iva,
				SUM(descuento) descuento,
				SUM(total) total';
	$totales = Factory::getInstance()->getArrayFromView('reporte_facturacion_v', $where, 0, $fields);

	$tabla = new HtmlTable(array('cantRows' => ($tipoReporte == 'D' ? count($listaDocumentos) : 1), 'cantCols' => ($tipoReporte == 'D' ? 12 + ($esXls ? 1 : 0) : 6), 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray'));

	if ($tipoReporte == 'D') {
		$arrayHeader = array(
			array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 8),
			array('content' => 'Doc.', 'dataType' => 'Center', 'width' => 4),
			array('content' => 'Número', 'dataType' => 'Center', 'width' => 10),
			array('content' => 'L', 'dataType' => 'Center', 'width' => 3, 'title' => 'Letra'),
			array('content' => 'Cliente', 'width' => 23),
			array('content' => 'Provincia', 'title' => 'Operación', 'width' => 10),
			array('content' => 'Pares', 'dataType' => 'Entero', 'width' => 4),
			array('content' => 'Neto GV', 'dataType' => 'DosDecimales', 'title' => 'Neto gravado', 'width' => 8),
			array('content' => 'Neto NG', 'dataType' => 'DosDecimales', 'title' => 'Neto no gravado', 'width' => 8),
			array('content' => 'Iva', 'dataType' => 'DosDecimales', 'width' => 7),
			array('content' => 'Desc', 'dataType' => 'DosDecimales', 'title' => 'Descuento', 'width' => 7),
			array('content' => 'Total', 'dataType' => 'DosDecimales', 'width' => 8)
		);

		if ($esXls) {
			$arrayHeader[] = array('content' => 'CUIT Cliente', 'dataType' => 'Center');
		}

		$tabla->createHeaderFromArray($arrayHeader);

		$tabla->getRowCellArray($rows, $cells);
		for ($i = 0; $i < count($listaDocumentos); $i++) {
			$doc = $listaDocumentos[$i];
			$doc['tipo_documento'] == 'NDB' && $doc['pares'] = 0;

			$cells[$i][0]->content = Funciones::formatearFecha($doc['fecha'], 'd/m/Y');
			$cells[$i][1]->content = $doc['tipo_documento'];
			$cells[$i][2]->content = '0002-' . Funciones::padLeft($doc['numero'], 8 , 0);
			$cells[$i][3]->content = $doc['letra'];
			$cells[$i][4]->content = '[' . $doc['cod_cliente'] . '] ' . $doc['razon_social'];
			$cells[$i][5]->content = $doc['provincia'];
			$cells[$i][6]->content = $doc['pares'];
			$cells[$i][7]->content = $doc['neto'];
			$cells[$i][8]->content = $doc['neto_ng'];
			$cells[$i][9]->content = $doc['iva'];
			$cells[$i][10]->content = $doc['descuento'];
			$cells[$i][11]->content = $doc['total'];

			if ($esXls) {
				$cells[$i][12]->content = Funciones::ponerGuionesAlCuit(Factory::getInstance()->getCliente($doc['cod_cliente'])->cuit);
			}
		}

		//Agrego la de total movimientos
		$j = 5;
		$tabla->getFootArray($foots);
		$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
		$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
		$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';
		$tot = $totales[0];

		$foots[0]->content = 'TOTALES';
		$foots[0]->class .= ' aCenter';
		$foots[0]->colspan = ($j + 1);
		$foots[$j + 1]->content = $tot['pares'];
		$foots[$j + 2]->content = Funciones::formatearMoneda($tot['neto']);
		$foots[$j + 3]->content = Funciones::formatearMoneda($tot['neto_ng']);
		$foots[$j + 4]->content = Funciones::formatearMoneda($tot['iva']);
		$foots[$j + 5]->content = Funciones::formatearMoneda($tot['descuento']);
		$foots[$j + 6]->content = Funciones::formatearMoneda($tot['total']);
		$foots[$j + 7]->content = '-';
	} else {
		$tabla->createHeaderFromArray(
			array(
				 array('content' => 'Pares', 'dataType' => 'Center', 'width' => 15),
				 array('content' => 'Neto GV', 'dataType' => 'Moneda', 'width' => 15),
				 array('content' => 'Neto NG', 'dataType' => 'Moneda', 'title' => 'Neto no gravado', 'width' => 15),
				 array('content' => 'Iva', 'dataType' => 'Moneda', 'width' => 15),
				 array('content' => 'Desc', 'dataType' => 'Moneda', 'title' => 'Descuento', 'width' => 15),
				 array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 25)
			)
		);
		$tot = $totales[0];
		$tabla->getRowCellArray($rows, $cells);

		$cells[0][0]->content = $tot['pares'];
		$cells[0][1]->content = $tot['neto'];
		$cells[0][2]->content = $tot['neto_ng'];
		$cells[0][3]->content = $tot['iva'];
		$cells[0][4]->content = $tot['descuento'];
		$cells[0][5]->content = $tot['total'];
	}

	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
