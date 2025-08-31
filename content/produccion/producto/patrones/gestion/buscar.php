<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/patrones/gestion/buscar/')) { ?>
<?php

function jsonRecibo(Patron $patron) {
	$json = array();
	$json['idArticulo'] = $patron->colorPorArticulo->articulo->id;
	$json['denomArticulo'] = $patron->colorPorArticulo->articulo->nombre;
	$json['idColor'] = $patron->colorPorArticulo->id;
	$json['denomColor'] = $patron->colorPorArticulo->nombre;
	$json['idVersion'] = $patron->version;
	$json['tipoPatron'] = $patron->tipoPatron;
	$json['confirmado'] = $patron->confirmado;
	$json['versionActual'] = $patron->versionActual;
	return $json;
}

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$where = '';
	$where .= (empty($idArticulo) ? '' : 'cod_articulo = ' . Datos::objectToDB($idArticulo)) . ' AND ';
	$where .= (empty($idColor) ? '' : 'cod_color_articulo = ' . Datos::objectToDB($idColor)) . ' AND ';
	$where = trim($where, ' AND ');
	$where = (empty($where) ? '1=1' : $where);
	$order = ' ORDER BY cod_articulo DESC';

	$patrones = Factory::getInstance()->getListObject('Patron', $where . $order);
	if (count($patrones) == 0) {
		throw new FactoryExceptionCustomException('No hay patrones con ese filtro');
	}

	$arr = array();
	foreach ($patrones as $patron) {
		$arr[] = jsonRecibo($patron);
	}

	Html::jsonEncode('', $arr);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>