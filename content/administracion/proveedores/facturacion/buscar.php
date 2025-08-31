<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/facturacion/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');
$docFAC = (Funciones::get('docFAC') == 'true') ? true : false;
$docNCR = (Funciones::get('docNCR') == 'true') ? true : false;
$docNDB = (Funciones::get('docNDB') == 'true') ? true : false;
$tipoDocumento = Funciones::get('tipoDocumento');
$tipoDocumento = ($tipoDocumento != 'S' && $tipoDocumento != 'N' ? false : $tipoDocumento);
$proveedor = Funciones::get('proveedor');
$orderBy = Funciones::get('orderBy');
$tipoReporte = Funciones::get('tipoReporte');
$tipoFecha = Funciones::get('tipoFecha');
$esXls = Funciones::get('esXls') == '1';

try {
	//Validaciones
	if (!($docFAC || $docNDB || $docNCR)) {
		throw new FactoryExceptionCustomException('Debe seleccionar al menos un tipo de documento');
	}

	//Inicializo totales
	$totalNetoGravado = 0;
	$totalNetoNoGravado = 0;
	$totalIva = 0;
	$totalPercepcionIibb = 0;
	$totalPercepcionGanancias = 0;
	$totalIvaPercepcion = 0;
	$total = 0;

	$nombreCampoFecha = ($tipoFecha == 'D' ? 'fecha' : 'fecha_periodo_fiscal');

	//Armo el where
	$where .= '(' . ($docFAC ? 'tipo_docum = ' . Datos::objectToDB('FAC') . ' OR ' : '');
	$where .= ($docNDB ? 'tipo_docum = ' . Datos::objectToDB('NDB') . ' OR ' : '');
	$where .= ($docNCR ? 'tipo_docum = ' . Datos::objectToDB('NCR') . ' OR ' : '');
	$where = rtrim($where, ' OR ');
	$where .= ') AND ';
	$where .= ($tipoDocumento ? 'factura_gastos = ' . Datos::objectToDB($tipoDocumento) . ' AND ' : '');
	$where .= (empty($proveedor) ? '' : 'cod_proveedor = ' . Datos::objectToDB($proveedor) . ' AND ');
	$where .= (($empresa != 1 && $empresa != 2) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
	$where .= Funciones::strFechas($fechaDesde, $fechaHasta, $nombreCampoFecha) . ' AND ';
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where = trim($where, ' AND ');
	$where .= (empty($orderBy) ? '' : ' ORDER BY ' . $orderBy);

	$listaDocumentos = Factory::getInstance()->getListObject('DocumentoProveedor', $where);
	if (empty($listaDocumentos)) {
		throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
	}

	$netoGravadoOtrosImpuestos = 0;
	$percepcionGanancias = 0;
	$percepcionIibb = 0;

	if ($tipoReporte == 'D') {
		$tabla = new HtmlTable(array(
									'cantRows'    => count($listaDocumentos), 'cantCols' => 18, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
									'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'
							   ));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			  array(
				   array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 5),
				   array('content' => 'Doc.', 'title' => 'Documento', 'dataType' => 'Center', 'width' => 3),
				   array('content' => 'Nro.', 'title' => 'Número', 'dataType' => 'Center', 'width' => 5),
				   array('content' => 'Let.', 'title' => 'Letra', 'dataType' => 'Center', 'width' => 3),
				   array('content' => 'Cod.<br>Prov.', 'dataType' => 'Center', 'width' => 4),
				   array('content' => 'Razón<br>Social', 'width' => 8),
				   array('content' => 'Cuit', 'width' => 6),
				   array('content' => 'Imp.<br>esp.', 'dataType' => 'Center', 'width' => 5),
				   array('content' => 'Imp.esp.<br>denom', 'width' => 7),
				   array('content' => 'Neto grav', 'title' => 'Neto gravado', 'dataType' => 'Moneda', 'width' => 6),
				   array('content' => 'Neto<br>no gr', 'title' => 'Neto no gravado', 'dataType' => 'Moneda', 'width' => 6),
				   array('content' => 'Iva 21', 'dataType' => 'Moneda', 'width' => 6),
				   array('content' => 'Iva 10,5', 'dataType' => 'Moneda', 'width' => 6),
				   array('content' => 'Iva 27', 'dataType' => 'Moneda', 'width' => 6),
				   array('content' => 'P.<br>Iibb', 'title' => 'Percepción IVA', 'dataType' => 'Moneda', 'width' => 6),
				   array('content' => 'P.<br>Iva', 'title' => 'Percepción IVA', 'dataType' => 'Moneda', 'width' => 6),
				   array('content' => 'P.<br>Ganan', 'title' => 'Percepción ganancias', 'dataType' => 'Moneda', 'width' => 6),
				   array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 6)
			  )
		);

		for ($i = 0; $i < count($listaDocumentos); $i++) {
			$doc = $listaDocumentos[$i];
			/** @var DocumentoProveedor $doc */
			$signo = 1;
			if($doc->tipoDocum == TiposDocumento::notaDeCredito) {
				$signo = -1;
			}
			$importeIva21 = 0;
			$importeIva10 = 0;
			$importeIva27 = 0;
			$importeIvaPercepcion = 0;
			$percepcionGanancias = 0;
			$percepcionIibb = 0;
			foreach ($doc->impuestos as $impuesto) {
				switch ($impuesto->idImpuesto) {
					case Impuestos::iva21:
						$importeIva21 = $impuesto->importe * $signo;
						break;
					case Impuestos::iva10:
						$importeIva10 = $impuesto->importe * $signo;
						break;
					case Impuestos::iva27:
						$importeIva27 = $impuesto->importe * $signo;
						break;
					case Impuestos::ivaPercepcion:
						$importeIvaPercepcion = $impuesto->importe * $signo;
						break;
				}

				if ($impuesto->impuesto->tipo == TipoImpuesto::iibb) {
					$percepcionIibb += $impuesto->importe * $signo;
				}

				if ($impuesto->impuesto->tipo == TipoImpuesto::ganancias) {
					$percepcionGanancias += $impuesto->importe * $signo;
				}
			}

			$rows[$i]->class = 's10';
			$cells[$i][0]->content = Funciones::formatearFecha($doc->fecha, 'd/m/Y');
			$cells[$i][1]->content = $doc->tipoDocum;
			$cells[$i][2]->content = $doc->nroDocumentoCompleto;
			$cells[$i][3]->content = $doc->letra;
			$cells[$i][4]->content = ($doc->esProveedor ? $doc->proveedor->id : '-');
			$cells[$i][5]->content = ($esXls ? $doc->esProveedor ? $doc->proveedor->razonSocial : $doc->documentoGastoDatos->razonSocial : Funciones::acortar(($doc->esProveedor ? $doc->proveedor->razonSocial : $doc->documentoGastoDatos->razonSocial), 30));
			$cells[$i][6]->content = Funciones::ponerGuionesAlCuit(($doc->esProveedor ? $doc->proveedor->cuit : $doc->documentoGastoDatos->cuit));
			$cells[$i][7]->content = ($doc->esProveedor ? $doc->proveedor->imputacionGeneral->id : $doc->documentoGastoDatos->imputacion->id);
			$cells[$i][8]->content = ($esXls ? $doc->esProveedor ? $doc->proveedor->imputacionGeneral->nombre : $doc->documentoGastoDatos->imputacion->nombre : Funciones::acortar(($doc->esProveedor ? $doc->proveedor->imputacionGeneral->nombre : $doc->documentoGastoDatos->imputacion->nombre), 35));
			$cells[$i][9]->content = $doc->netoGravado * $signo;
			$cells[$i][10]->content = $doc->netoNoGravado * $signo;
			$cells[$i][11]->content = $importeIva21;
			$cells[$i][12]->content = $importeIva10;
			$cells[$i][13]->content = $importeIva27;
			$cells[$i][14]->content = $percepcionIibb;
			$cells[$i][15]->content = $importeIvaPercepcion;
			$cells[$i][16]->content = $percepcionGanancias;
			$cells[$i][17]->content = $doc->importeTotal * $signo;

			$totalNetoGravado += $doc->netoGravado * $signo;
			$totalNetoNoGravado += $doc->netoNoGravado * $signo;
			$totalIva21 += $importeIva21;
			$totalIva10 += $importeIva10;
			$totalIva27 += $importeIva27;
			$totalPercepcionIibb += $percepcionIibb;
			$totalIvaPercepcion += $importeIvaPercepcion;
			$totalPercepcionGanancias += $percepcionGanancias;
			$total += $doc->importeTotal * $signo;
		}

		$tabla->getFootArray($foots);
		$tabla->foot->tdBaseClass = 'bold white s10 p5 bLightOrange bTopWhite aRight ';
		$tabla->foot->tdBaseClassFirst = 'bold white s10 p5 bLightOrange bTopWhite aRight cornerBL5 ';
		$tabla->foot->tdBaseClassLast = 'bold white s10 p5 bLightOrange bTopWhite aRight cornerBR5 ';

		$foots[0]->content = 'TOTALES';
		$foots[0]->class .= ' aCenter';
		$foots[0]->colspan = 9;
		$foots[9]->content = Funciones::formatearMoneda($totalNetoGravado);
		$foots[10]->content = Funciones::formatearMoneda($totalNetoNoGravado);
		$foots[11]->content = Funciones::formatearMoneda($totalIva21);
		$foots[12]->content = Funciones::formatearMoneda($totalIva10);
		$foots[13]->content = Funciones::formatearMoneda($totalIva27);
		$foots[14]->content = Funciones::formatearMoneda($totalPercepcionIibb);
		$foots[15]->content = Funciones::formatearMoneda($totalIvaPercepcion);
		$foots[16]->content = Funciones::formatearMoneda($totalPercepcionGanancias);
		$foots[17]->content = Funciones::formatearMoneda($total);
	} else {
		$tabla = new HtmlTable(array(
									'cantRows'    => 1, 'cantCols' => 9, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
									'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'
							   ));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			  array(
				   array('content' => 'Neto grav', 'title' => 'Neto gravado', 'dataType' => 'Moneda', 'width' => 12),
				   array('content' => 'Neto no gr', 'title' => 'Neto no gravado', 'dataType' => 'Moneda', 'width' => 13),
				   array('content' => 'Iva 21', 'dataType' => 'Moneda', 'width' => 10),
				   array('content' => 'Iva 10,5', 'dataType' => 'Moneda', 'width' => 10),
				   array('content' => 'Iva 27', 'dataType' => 'Moneda', 'width' => 10),
				   array('content' => 'P. Iibb', 'title' => 'Percepción IVA', 'dataType' => 'Moneda', 'width' => 10),
				   array('content' => 'P. Iva', 'title' => 'Percepción IVA', 'dataType' => 'Moneda', 'width' => 10),
				   array('content' => 'P. Ganan', 'title' => 'Percepción ganancias', 'dataType' => 'Moneda', 'width' => 10),
				   array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 15)
			  )
		);

		$importeIva21 = 0;
		$importeIva10 = 0;
		$importeIva27 = 0;
		$importeIvaPercepcion = 0;

		foreach ($listaDocumentos as $doc) {
			/** @var DocumentoProveedor $doc */
			$signo = 1;
			if($doc->tipoDocum == TiposDocumento::notaDeCredito) {
				$signo = -1;
			}
			$totalNetoGravado += $doc->netoGravado;
			$totalNetoNoGravado += $doc->netoNoGravado;

			foreach ($doc->impuestos as $impuesto) {
				switch ($impuesto->idImpuesto) {
					case Impuestos::iva21:
						$importeIva21 += $impuesto->importe * $signo;
						break;
					case Impuestos::iva10:
						$importeIva10 += $impuesto->importe * $signo;
						break;
					case Impuestos::iva27:
						$importeIva27 += $impuesto->importe * $signo;
						break;
					case Impuestos::ivaPercepcion:
						$importeIvaPercepcion += $impuesto->importe * $signo;
						break;
				}

				if ($impuesto->impuesto->tipo == TipoImpuesto::iibb) {
					$percepcionIibb += $impuesto->importe * $signo;
				}

				if ($impuesto->impuesto->tipo == TipoImpuesto::ganancias) {
					$percepcionGanancias += $impuesto->importe * $signo;
				}
			}

			$total += $doc->importeTotal;
		}

		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$cells[0][$j]->class = ' bBottomDarkGray bold';
		}

		$cells[0][0]->content = $totalNetoGravado;
		$cells[0][1]->content = $totalNetoNoGravado;
		$cells[0][2]->content = $importeIva21;
		$cells[0][3]->content = $importeIva10;
		$cells[0][4]->content = $importeIva27;
		$cells[0][5]->content = $percepcionIibb;
		$cells[0][6]->content = $importeIvaPercepcion;
		$cells[0][7]->content = $percepcionGanancias;
		$cells[0][8]->content = $total;
	}

	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
