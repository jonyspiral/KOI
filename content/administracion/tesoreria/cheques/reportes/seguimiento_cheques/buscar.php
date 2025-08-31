<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/reportes/seguimiento_cheques/buscar/')) { ?>
<?php

function armarHistoria(Cheque $cheque) {
	$historia = array();
	foreach ($cheque->historia as $ipoi) {
		/** @var ImportePorOperacionItem $ipoi */
		$ope = $ipoi->operacion;
		if ($ope instanceof TransferenciaDoble && $ope->entradaSalida == 'E') {
			$exit = false;
			foreach ($ope->contrapartida->importePorOperacion->detalle as $d) {
				/** @var ImportePorOperacionItem $d */
				if ($d->tipoImporte == $ipoi->tipoImporte && $d->idImporte == $ipoi->idImporte) {
					$exit = true;
					continue;
				}
			}
			if ($exit) {
				continue;
			}
		}
		$historia[$ipoi->idImportePorOperacion] = array(
			'fecha'			=> $ope->fecha(),
			'de'			=> $ope->getTextoDe(true),
			'para'			=> $ope->getTextoPara(true),
			'operacion'		=> implode(' ', preg_split('/(?=[A-Z])/', $ope->getClass(), -1, PREG_SPLIT_NO_EMPTY)) . ' Nº ' . $ope->numero,
			'observacion'	=> $ope->observaciones,
			'anulado'		=> $ipoi->anulado(),
			'tipoOperacion'	=> $ope->getTipoTransferenciaBase()
		);
	}
	return $historia;
}

function armarTitulo(Cheque $cheque, $motivoOP) {
	global $pdf;
	$class = $cheque->rechazado() ? 'indicador-rojo' : ($cheque->anulado() ? 'indicador-gris' : '');
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 7, 'class' => '', 'cellSpacing' => 1, 'width' => '100%'));
	if ($pdf) {
		$tabla->body->tdBaseClass = 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray';
		$tabla->body->tdBaseClassLast = 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray';
	}
	$tabla->getRowCellArray($rows, $cells);
	if ($pdf) {
		$tabla->createHeaderFromArray(
			array(
				 array('content' => 'Número', 'width' => 8),
				 array('content' => 'F. Vto.', 'dataType' => 'Fecha', 'width' => 7),
				 array('content' => 'Importe', 'dataType' => 'Moneda', 'width' => 9),
				 array('content' => 'Banco', 'width' => 19),
				 array('content' => 'Librador', 'width' => 15),
				 array('content' => 'Cliente', 'width' => 19),
				 array('content' => 'Proveedor', 'width' => 23)
			)
		);
		$tabla->headClass('tableHeader' . ($class ? ' selected ' . $class : ''));
	}

	$rows[0]->class = '';

	$cells[0][0]->class = 'w8p bold';
	$cells[0][1]->class = 'w7p bold';
	$cells[0][2]->class = 'w9p aRight pRight10 bold';
	$cells[0][3]->class = 'w19p';
	$cells[0][4]->class = 'w15p';
	$cells[0][5]->class = 'w19p';
	$cells[0][6]->class = 'w23p';

	$cells[0][0]->content = $cheque->numero;
	$cells[0][1]->content = $cheque->fechaVencimiento;
	$cells[0][2]->content = Funciones::formatearMoneda($cheque->importe);
	$cells[0][3]->content = $cheque->banco->getIdNombre('nombre', 'idBanco');
	$cells[0][4]->content = $cheque->libradorNombre;
	$cells[0][5]->content = ($cheque->esDeCliente() ? $cheque->cliente->getIdNombre() : '');
	$cells[0][6]->content = ($cheque->entregadoProveedor() ? $cheque->proveedor->getIdNombre() : $motivoOP);

	return $tabla->create(true);
}

$empresa = Funciones::session('empresa');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$idCliente = Funciones::get('idCliente');
$diasDesde = Funciones::get('diasDesde');
$diasHasta = Funciones::get('diasHasta');
$importeDesde = Funciones::get('importeDesde');
$importeHasta = Funciones::get('importeHasta');
$idCuentaBancaria = Funciones::get('idCuentaBancaria');
$idCaja = Funciones::get('idCaja');
$tipo = Funciones::get('tipo');
$numero = Funciones::get('numero');
$rechazado = Funciones::get('rechazado');
$orden = Funciones::get('orden');
$pdf = Funciones::get('pdf');

try {
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_vencimiento');
	$where .= ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	($idCliente)		&& $where .= ' AND (cod_cliente = ' . 			Datos::objectToDB($idCliente) . ')';
	($diasDesde)		&& $where .= ' AND (dias_vencimiento >= ' . 	Datos::objectToDB($diasDesde) . ')';
	($diasHasta)		&& $where .= ' AND (dias_vencimiento <= ' . 	Datos::objectToDB($diasHasta) . ')';
	($importeDesde)		&& $where .= ' AND (importe >= ' . 				Datos::objectToDB($importeDesde) . ')';
	($importeHasta)		&& $where .= ' AND (importe <= ' . 				Datos::objectToDB($importeHasta) . ')';
	($idCuentaBancaria)	&& $where .= ' AND (cod_cuenta_bancaria = ' . 	Datos::objectToDB($idCuentaBancaria) . ')';
	($idCaja)			&& $where .= ' AND (cod_caja_actual = ' . 		Datos::objectToDB($idCaja) . ')';
	($tipo != '0')		&& $where .= ' AND (cod_cuenta_bancaria IS ' . ($tipo == '1' ? 'NOT' : '') . ' NULL)';
	($numero)			&& $where .= ' AND (numero LIKE ' . 			Datos::objectToDB('%' . $numero . '%') . ')';
	($rechazado != '0')	&& $where .= ' AND (cod_rechazo_cheque IS ' . 	($rechazado == '1' ? 'NOT' : '') . ' NULL)';
	$where = trim($where, ' AND ');

	$order = ' ORDER BY ';
	switch ($orden) {
		case 1: $order .= 'fecha_vencimiento DESC, importe ASC, numero ASC'; break;
		case 2: $order .= 'importe ASC, fecha_vencimiento ASC, numero ASC'; break;
		case 3: $order .= 'importe DESC, fecha_vencimiento ASC, numero ASC'; break;
		case 4: $order .= 'numero ASC, fecha_vencimiento ASC, importe ASC'; break;
		case 5: $order .= 'numero ASC, fecha_vencimiento ASC, importe ASC'; break;
		default: $order .= 'fecha_vencimiento ASC, importe ASC, numero ASC'; break;
	}

	$cheques = Factory::getInstance()->getListObject('Cheque', $where . $order);
	if (count($cheques) == 0) {
		throw new FactoryExceptionCustomException('No hay cheques que cumplan con esos filtros');
	}
	if (count($cheques) > 500) {
		throw new FactoryExceptionCustomException('La consulta devolvió más de 500 cheques. Por favor, reduzca el rango de búsqueda');
	}

	$html = '';
	foreach ($cheques as $cheque) {
		/** @var Cheque $cheque */
		$historia = armarHistoria($cheque);
		$class = $cheque->rechazado() ? 'indicador-rojo' : ($cheque->anulado() ? 'indicador-gris' : '');

		//CREO TABLA NUEVA
		$tabla = new HtmlTable(array('cantRows' => count($historia), 'cantCols' => 5, 'class' => ($pdf ? 'pBottom30' : ''), 'cellSpacing' => 1, 'width' => '100%',
									'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			array(
				 array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 10),
				 array('content' => 'De', 'width' => 25),
				 array('content' => 'Para', 'width' => 25),
				 array('content' => 'Operación', 'width' => 20),
				 array('content' => 'Observaciones', 'width' => 20)
			)
		);
		$tabla->headClass('tableHeader' . ($class ? ' selected ' . $class : ''));

		$i = 0;
		$motivoOP = '';
		foreach ($historia as $item) {
			($item['anulado']) && $rows[$i]->class .= ' indicador-gris';
			$cells[$i][0]->content = $item['fecha'];
			$cells[$i][1]->content = $item['de'];
			$cells[$i][2]->content = $item['para'];
			$cells[$i][3]->content = $item['operacion'];
			$cells[$i][4]->content = $item['observaciones'];
			$i++;

			if($item['tipoOperacion'] == TiposTransferenciaBase::ordenDePago){
				$motivoOP = $item['para'];
			}
		}

		//CIERRO TABLA
			$echo = '<div id="">' .
				'<div class="' . $class . '" style="color: black; font-size: 13px; padding-left: 3px;">' . armarTitulo($cheque, $motivoOP) . '</div>' .
				'<div>' . $tabla->create(true) . '</div>' .
				'</div>';
		$html .= $echo;
		$i = $j;
	}

	echo $html;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}
?>
<?php } ?>