<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/reportes/listado_clientes/buscar/')) { ?>
<?php

$cuit = Funciones::get('cuit');
$idVendedor = Funciones::get('idVendedor');
$idPais = Funciones::get('idPais');
$idProvincia = Funciones::get('idProvincia');
$idLocalidad = Funciones::get('idLocalidad');
$calle = Funciones::get('calle');
$numero = Funciones::get('numero');
$orderBy = Funciones::get('orderBy');
$esXls = Funciones::get('esXls');

	try {
	$arrayOrderBy = array(
		0 => 'razon_social ASC',
		1 => 'cod_cliente ASC',
		2 => 'cod_pais ASC, denom_provincia ASC, denom_localidad ASC',
		3 => 'denom_provincia ASC, denom_localidad ASC',
		4 => 'denom_localidad ASC'
	);

	//Armo el where
	$where .=  ($cuit ? 'cuit LIKE ' . Datos::objectToDB('%' . $cuit . '%') . ' AND ' : '');
	$where .=  ($idVendedor ? 'cod_vendedor = ' . Datos::objectToDB($idVendedor) . ' AND ' : '');
	$where .=  ($idPais ? 'cod_pais = ' . Datos::objectToDB($idPais) . ' AND ' : '');
	$where .=  ($idProvincia ? 'cod_provincia = ' . Datos::objectToDB($idProvincia) . ' AND ' : '');
	$where .=  ($idLocalidad ? 'cod_localidad = ' . Datos::objectToDB($idLocalidad) . ' AND ' : '');
	$where .=  ($calle ? 'calle LIKE ' . Datos::objectToDB('%' . $calle . '%') . ' AND ' : '');
	$where .=  ($numero ? 'numero LIKE ' . Datos::objectToDB('%' . $numero . '%') . ' AND ' : '');
	$where = trim($where, ' AND ');
	$where = ($where ? $where : '1 = 1');
	$order = ' ORDER BY ' . $arrayOrderBy[$orderBy];

	$listaClientes = Factory::getInstance()->getArrayFromView('listado_clientes_v', $where . $order);
	if (empty($listaClientes)) {
		throw new FactoryExceptionCustomException('No existen clientes con el filtro especificado');
	}

	$tabla = new HtmlTable(array('cantRows' => count($listaClientes), 'cantCols' => 10, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray'));


	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Cliente', 'width' => 16),
			 array('content' => 'Denom. fant.', 'width' => 10, 'title' => 'Denominación fantasía'),
			 array('content' => 'Cuit', 'dataType' => 'Center', 'width' => 8),
			 array('content' => 'Pais', 'dataType' => 'Center', 'width' => 3),
			 array('content' => 'Provincia', 'width' => 9),
			 array('content' => 'Localidad', 'width' => 10),
			 array('content' => 'Dirección', 'width' => 12),
			 array('content' => 'Tel. 1', 'dataType' => 'Center', 'width' => 9),
			 array('content' => 'Tel. 2', 'dataType' => 'Center', 'width' => 9),
			 array('content' => 'E-mail', 'width' => 14)
		)
	);

	$tabla->getRowCellArray($rows, $cells);
	for ($i = 0; $i < count($listaClientes); $i++) {
		$cliente = $listaClientes[$i];

		$cliente['denom_fantasia'] = trim($cliente['denom_fantasia'], ' ');
		$cliente['piso'] = trim($cliente['piso'], ' ');
		$cliente['oficina_depto'] = trim($cliente['oficina_depto'], ' ');
		$cliente['telefono_1'] = trim($cliente['telefono_1'], ' ');
		$cliente['telefono_2'] = trim($cliente['telefono_2'], ' ');
		$cliente['email_cliente'] = trim($cliente['email_cliente'], ' ');
		$cliente['email_cliente'] = trim($cliente['email_cliente'], ' ');

		$rows[$i]->id = $cliente['cod_cliente'];
		$cells[$i][0]->content = '[' . $cliente['cod_cliente'] . '] ' . $cliente['razon_social'];
		$cells[$i][0]->class .= ' cPointer cliente';
		$cells[$i][0]->title = 'Ir a cliente ' . $cliente['cod_cliente'];
		$cells[$i][1]->content = (empty($cliente['denom_fantasia']) ? '-' : Funciones::acortar($cliente['denom_fantasia'], 15));
		$cells[$i][1]->title = (empty($cliente['denom_fantasia']) ? null : $cliente['denom_fantasia']);
		$cells[$i][2]->content = Funciones::ponerGuionesAlCuit($cliente['cuit']);
		$cells[$i][3]->content = $cliente['cod_pais'];
		$cells[$i][4]->content = $cliente['denom_provincia'];
		$cells[$i][5]->content = $cliente['denom_localidad'];
		$cells[$i][6]->content = $cliente['calle'] . ' ' . $cliente['numero'] . (empty($cliente['piso']) ? '' : ' - Piso: ' . $cliente['piso']) . (empty($cliente['oficina_depto']) ? '' : ' - Oficina: ' . $cliente['oficina_depto']);
		$cells[$i][7]->content = (empty($cliente['telefono_1']) ? '-' : $cliente['telefono_1']);
		$cells[$i][8]->content = (empty($cliente['telefono_2']) ? '-' : $cliente['telefono_2']);
		$cells[$i][9]->title = (empty($cliente['email_cliente']) ? (empty($cliente['email_sucursal']) ? '-' : $cliente['email_sucursal']) : $cliente['email_cliente']);
		$cells[$i][9]->content = ($esXls ? $cells[$i][9]->title : Funciones::acortar($cells[$i][9]->title, 24));
	}

	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
