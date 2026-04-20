<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/stock_a_fecha/buscar/')) { ?>
<?php

function armoHead($tabla, $rango) {
	$headerArray = array();
	$headerArray[] = array('content' => 'Almacén', 'width' => 15);
	$headerArray[] = array('content' => 'Material', 'width' => 30);
	for ($i = 0; $i < 8; $i++) {
		$headerArray[] = array('content' => ($rango->posicion[$i + 1] ? $rango->posicion[$i + 1] : '-'), 'dataType' => 'Center', 'width' => 5);
	}
	$headerArray[] = array('content' => 'Total', 'dataType' => 'Center', 'width' => 5);
	$tabla->createHeaderFromArray($headerArray);
}

function meterItem(&$cells, $i, $fila, Almacen $almacen) {
	$cells[$i][0]->content = $almacen->getIdNombre();
	$cells[$i][1]->content = '[' . $fila['cod_material'] . '-' . $fila['cod_color'] . '] ' . $fila['nombre_material'] . ' ' . $fila['nombre_color'];
	for ($j = 1; $j <= 8; $j++) {
		$cells[$i][$j + 1]->content = $fila['cant_' . $j];
	}
	$cells[$i][10]->content = $fila['cantidad'];
}

$cacheAlmacenes = array();
function getAlmacen($id) {
	global $cacheAlmacenes;
	if (!isset($cacheAlmacenes[$id])) {
		$cacheAlmacenes[$id] = Factory::getInstance()->getAlmacen($id);
	}
	return $cacheAlmacenes[$id];
}

$cacheRangos = array();
function getRango($id) {
	global $cacheRangos;
	if (!isset($cacheRangos[$id])) {
		$cacheRangos[$id] = Factory::getInstance()->getRangoTalle($id);
	}
	return $cacheRangos[$id];
}

$idAlmacen = Funciones::get('idAlmacen');
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$fecha = Funciones::get('fecha');

try {
	if(empty($idAlmacen) && empty($idMaterial)) {
		throw new FactoryExceptionCustomException('Debe ingresar al menos un almacén o un material para realizar la búsqueda');
	}

	$where = Datos::objectToDB($idAlmacen) . ', ' . Datos::objectToDB($idMaterial) . ', ' . Datos::objectToDB($idColor) . ', ' . Datos::objectToDB($fecha);
	$stock = Factory::getInstance()->getArrayFromStoredProcedure('sp_stock_mp_a_fecha', $where);

	if (!count($stock)) {
		throw new FactoryExceptionCustomException('No existen registros con ese filtro');
	}

	$agrupamiento = $idMaterial ? 'cod_material' : 'cod_almacen';
	$arrayFinal = array();
	foreach ($stock as $fila) {
		if (!isset($arrayFinal[$fila[$agrupamiento]])) {
			$arrayFinal[$fila[$agrupamiento]] = array();
		}
		if (!isset($arrayFinal[$fila[$agrupamiento]][$fila['cod_rango']])) {
			$arrayFinal[$fila[$agrupamiento]][$fila['cod_rango']] = array(
				'cantidadFilas' => 0,
				'almacenes' => array()
			);
		}
		if (!isset($arrayFinal[$fila[$agrupamiento]][$fila['cod_rango']]['almacenes'][$fila['cod_almacen']])) {
			$arrayFinal[$fila[$agrupamiento]][$fila['cod_rango']]['almacenes'][$fila['cod_almacen']] = array();
		}
		if (!isset($arrayFinal[$fila[$agrupamiento]][$fila['cod_rango']]['almacenes'][$fila['cod_almacen']][$fila['cod_material']])) {
			$arrayFinal[$fila[$agrupamiento]][$fila['cod_rango']]['almacenes'][$fila['cod_almacen']][$fila['cod_material']] = array();
		}
		$arrayFinal[$fila[$agrupamiento]][$fila['cod_rango']]['almacenes'][$fila['cod_almacen']][$fila['cod_material']][$fila['cod_color']] = $fila;
		$arrayFinal[$fila[$agrupamiento]][$fila['cod_rango']]['cantidadFilas']++;
	}

	//Imprimo la tabla
	$html = '';
	foreach ($arrayFinal as $rangos) {
		$captionSetted = false;
		foreach ($rangos as $idRango => $detalleRango) {
			$rangoTalle = getRango($idRango);
			$tabla = new HtmlTable(array('cantRows' => $detalleRango['cantidadFilas'], 'cantCols' => 11, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
										 'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
			armoHead($tabla, $rangoTalle);
			$tabla->getRowCellArray($rows, $cells);
			$i = 0;
			foreach ($detalleRango['almacenes'] as $materiales) {
				foreach ($materiales as $colores) {
					foreach ($colores as $fila) {
						$almacen = getAlmacen($fila['cod_almacen']);
						if (!$captionSetted) {
							if ($idMaterial) {
								$tabla->caption = $fila['nombre_material'];
							} else {
								$tabla->caption = $almacen->nombre;
							}
							$captionSetted = true;
						}
						meterItem($cells, $i, $fila, $almacen);
						$i++;
					}
				}

			}
			$html .= $tabla->create(true);
		}
	}
	echo $html;

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar obtener el stock');
}

?>
<?php } ?>