<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/listado_proveedores/buscar/')) { ?>
<?php

$cuit = Funciones::get('cuit');
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
		1 => 'cod_prov ASC',
		2 => 'pais ASC, provincia ASC, localidad ASC',
		3 => 'denom_provincia ASC, denom_localidad ASC',
		4 => 'denom_localidad ASC'
	);

	//Armo el where
	$where .=  ($cuit ? 'cuit LIKE ' . Datos::objectToDB('%' . $cuit . '%') . ' AND ' : '');
	$where .=  ($idPais ? 'pais = ' . Datos::objectToDB($idPais) . ' AND ' : '');
	$where .=  ($idProvincia ? 'provincia = ' . Datos::objectToDB($idProvincia) . ' AND ' : '');
	$where .=  ($idLocalidad ? 'localidad = ' . Datos::objectToDB($idLocalidad) . ' AND ' : '');
	$where .=  ($calle ? 'calle LIKE ' . Datos::objectToDB('%' . $calle . '%') . ' AND ' : '');
	$where .=  ($numero ? 'numero LIKE ' . Datos::objectToDB('%' . $numero . '%') . ' AND ' : '');
	$where = trim($where, ' AND ');
	$where = ($where ? $where : '1 = 1');
	$order = ' ORDER BY ' . $arrayOrderBy[$orderBy];

	$listaProveedores = Factory::getInstance()->getArrayFromView('listado_proveedores_v', $where . $order);
	if (empty($listaProveedores)) {
		throw new FactoryExceptionCustomException('No existen proveedores con el filtro especificado');
	}

	$tabla = new HtmlTable(array('cantRows' => count($listaProveedores), 'cantCols' => 10, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray'));


	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Proveedor', 'width' => 16),
			 array('content' => 'Denom. fant.', 'width' => 10, 'title' => 'Denominación fantasía'),
			 array('content' => 'Cuit', 'dataType' => 'Center', 'width' => 8),
			 array('content' => 'Pais', 'dataType' => 'Center', 'width' => 3),
			 array('content' => 'Provincia', 'width' => 9),
			 array('content' => 'Localidad', 'width' => 6),
			 array('content' => 'Dirección', 'width' => 12),
			 array('content' => 'Tel. 1', 'dataType' => 'Center', 'width' => 11),
			 array('content' => 'Tel. 2', 'dataType' => 'Center', 'width' => 11),
			 array('content' => 'E-mail', 'width' => 14)
		)
	);

	$tabla->getRowCellArray($rows, $cells);
	for ($i = 0; $i < count($listaProveedores); $i++) {
		$proveedor = $listaProveedores[$i];

		$proveedor['denom_fantasia'] = trim($proveedor['denom_fantasia'], ' ');
		$proveedor['piso'] = trim($proveedor['piso'], ' ');
		$proveedor['oficina_depto'] = trim($proveedor['oficina_depto'], ' ');
		$proveedor['telefono_1'] = trim($proveedor['telefono_1'], ' ');
		$proveedor['telefono_2'] = trim($proveedor['telefono_2'], ' ');

		$rows[$i]->id = $proveedor['cod_prov'];
		$cells[$i][0]->content = Funciones::acortar('[' . $proveedor['cod_prov'] . '] ' . $proveedor['razon_social'], 34);
		$cells[$i][0]->class .= ' cPointer proveedor';
		$cells[$i][0]->title = 'Ir a proveedor ' . $proveedor['cod_prov'];
		$cells[$i][1]->content = (empty($proveedor['denom_fantasia']) ? '-' : Funciones::acortar($proveedor['denom_fantasia'], 15));
		$cells[$i][1]->title = (empty($proveedor['denom_fantasia']) ? null : $proveedor['denom_fantasia']);
		$cells[$i][2]->content = Funciones::ponerGuionesAlCuit($proveedor['cuit']);
		$cells[$i][3]->content = (empty($proveedor['pais']) ? '-' : $proveedor['pais']);
		$cells[$i][4]->content = (empty($proveedor['denom_provincia']) ? '-' : $proveedor['denom_provincia']);
		$cells[$i][5]->content = (empty($proveedor['denom_localidad']) ? '-' : $proveedor['denom_localidad']);
		$cells[$i][6]->content = $proveedor['calle'] . ' ' . $proveedor['numero'] . (empty($proveedor['piso']) ? '' : ' - Piso: ' . $proveedor['piso']) . (empty($proveedor['oficina_depto']) ? '' : ' - Oficina: ' . $proveedor['oficina_depto']);
		$cells[$i][7]->content = (empty($proveedor['telefono_1']) ? '-' : $proveedor['telefono_1']);
		$cells[$i][8]->content = (empty($proveedor['telefono_2']) ? '-' : $proveedor['telefono_2']);
		$cells[$i][9]->title = (empty($proveedor['e_mail']) ? '-' : $proveedor['e_mail']);
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
