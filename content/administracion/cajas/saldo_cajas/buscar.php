<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/saldo_cajas/buscar/')) { ?>
<?php

try {
	$cajasPorUsuario = Factory::getInstance()->getListObject('PermisoPorUsuarioPorCaja', 'cod_usuario = ' . Datos::objectToDB(Usuario::logueado()->id) . ' AND cod_permiso = ' . Datos::objectToDB(PermisosUsuarioPorCaja::verCaja) . ' ORDER BY cod_caja');

	if (empty($cajasPorUsuario)) {
		throw new FactoryExceptionCustomException('Actualmente no es usuario de ninguna caja');
	}

	$tabla = new HtmlTable(array('cantRows' => count($cajasPorUsuario), 'cantCols' => 9, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Código', 'dataType' => 'Center', 'width' => 6),
			 array('content' => 'Nombre', 'width' => 15),
			 array('content' => 'Imputación', 'dataType' => 'Center', 'width' => 4),
			 array('content' => 'Denominación imputación', 'width' => 15),
			 array('content' => 'Efectivo (gastos no confirmados)', 'dataType' => 'Right', 'width' => 20),
			 array('content' => 'Descubierto', 'dataType' => 'Moneda', 'width' => 10),
			 array('content' => 'Límite', 'dataType' => 'Moneda', 'width' => 10),
			 array('content' => 'Disp. p/ neg. total', 'dataType' => 'Moneda', 'width' => 10),
			 array('content' => 'Disp. p/ neg. act.', 'dataType' => 'Moneda', 'width' => 10)
		)
	);

	$i = -1;
	$totalEft = 0;
	$totalGastitos = 0;
	$descubierto = 0;
	$limite = 0;
	$totalDispParaNegociar = 0;
	$totalDispParaNegociarActual = 0;
	$hoy = Funciones::hoy();
	foreach ($cajasPorUsuario as $cajaPorUsuario) {
		$disponibilidadActualParaNegociar = 0;
		if ($cajaPorUsuario->caja->dispParaNegociar > 0) {
			$disponibilidadActualParaNegociar = $cajaPorUsuario->caja->dispParaNegociar;

			$where = 'tipo_documento = ' . Datos::objectToDB('VC') . ' AND ';
			$where .= 'tipo = ' . Datos::objectToDB('E') . ' AND ';
			$where .= 'para = ' . Datos::objectToDB($cajaPorUsuario->caja->getIdNombre());

			$ventasCheque = Factory::getInstance()->getArrayFromView('movimientos_caja_v', $where);
			foreach ($ventasCheque as $arrayVentaCheque) {
				$ventaCheque = Factory::getInstance()->getVentaCheques($arrayVentaCheque['numero'], $arrayVentaCheque['empresa'], 'S');

				foreach ($ventaCheque->importePorOperacion->detalle as $item) {
					/** @var ImportePorOperacionItem $item */
					if ($item->importe->tipoImporte == TiposImporte::cheque) {
						if (Funciones::esFechaMenorOIgual($hoy, $item->importe->fechaVencimiento)) {
							$disponibilidadActualParaNegociar -= $item->importe->importe;
						}
					}
				}
			}
		}

		$i++;

		$cells[$i][0]->content = $cajaPorUsuario->caja->id;
		$cells[$i][0]->class = 'bold';
		$cells[$i][1]->content = $cajaPorUsuario->caja->nombre;
		$cells[$i][1]->class = 'bold';
		$cells[$i][2]->content = $cajaPorUsuario->caja->imputacion->id;
		$cells[$i][3]->content = $cajaPorUsuario->caja->imputacion->nombre;
		$cells[$i][4]->content = Funciones::formatearMoneda($cajaPorUsuario->caja->importeEfectivo - $cajaPorUsuario->caja->importeGastitos) . ($cajaPorUsuario->caja->importeGastitos ? ' (' . Funciones::formatearMoneda($cajaPorUsuario->caja->importeGastitos) . ')' : '');
		$cells[$i][4]->class = 'bold';
		$cells[$i][5]->content = $cajaPorUsuario->caja->importeDescubierto;
		$cells[$i][6]->content = $cajaPorUsuario->caja->importeMaximo;
		$cells[$i][7]->content = $cajaPorUsuario->caja->dispParaNegociar;
		$cells[$i][8]->content = $disponibilidadActualParaNegociar;

		$totalEft += ($cajaPorUsuario->caja->importeEfectivo - $cajaPorUsuario->caja->importeGastitos);
		$totalGastitos += $cajaPorUsuario->caja->importeGastitos;
		$descubierto += $cajaPorUsuario->caja->importeDescubierto;
		$limite += $cajaPorUsuario->caja->importeMaximo;
		$totalDispParaNegociar += $cajaPorUsuario->caja->dispParaNegociar;
		$totalDispParaNegociarActual += $disponibilidadActualParaNegociar;
	}

	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

	$foots[0]->content = 'TOTALES';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 4;
	$foots[4]->content = Funciones::formatearMoneda($totalEft) . ($totalGastitos ? ' (' . Funciones::formatearMoneda($totalGastitos) . ')' : '');
	$foots[5]->content = Funciones::formatearMoneda($descubierto);
	$foots[6]->content = Funciones::formatearMoneda($limite);
	$foots[7]->content = Funciones::formatearMoneda($totalDispParaNegociar);
	$foots[8]->content = Funciones::formatearMoneda($totalDispParaNegociarActual);

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