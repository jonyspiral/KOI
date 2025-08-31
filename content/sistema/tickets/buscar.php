<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/tickets/buscar/')) { ?>
<?php

$idAreaEmpresa = Funciones::get('idAreaEmpresa');
$estado = Funciones::get('estado');
$autor = Funciones::get('autor');
$prioridad = Funciones::get('prioridad');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$numeroTicket = Funciones::get('numeroTicket');
$ordenarPor = Funciones::get('ordenarPor');

try {
	if (is_null($estado) || is_null($autor) || is_null($prioridad)) {
		throw new FactoryExceptionCustomException('Los filtros "Estado", "Autor" y "Prioridad" son obligatorios');
	}
	if (is_null($idAreaEmpresa) && is_null($numeroTicket) && ($autor != '2')) {
		throw new FactoryExceptionCustomException('Sólo se pueden pedir tickets de distintas áreas si en el filtro "Autor" se selecciona "Yo"');
	}

	$arrayOrdenarPor = array(
		'0' => 'prioridad DESC',
		'1' => 'fecha_alta ASC'
	);

	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	if ($idAreaEmpresa) {
		$where .= 'cod_area_empresa = ' . Datos::objectToDB($idAreaEmpresa) . ' AND ';
	}
	if (!$numeroTicket && $estado == '0') {
		$where .= 'estado = ' . Datos::objectToDB(KoiTicket::ESTADO_PENDIENTE) . ' AND ';
	} elseif (!$numeroTicket && $estado == '1') {
		$where .= 'estado <> ' . Datos::objectToDB(KoiTicket::ESTADO_PENDIENTE) . ' AND ';
	}
	if ($autor == '2') {
		$where .= 'cod_usuario = ' . Datos::objectToDB(Usuario::logueado()->id) . ' AND ';
	}
	if ($prioridad != 0) {
		$where .= 'prioridad = ' . Datos::objectToDB($prioridad) . ' AND ';
	}
	if ($numeroTicket) {
		$where .= 'cod_koi_ticket = ' . Datos::objectToDB($numeroTicket) . ' AND ';
	}
	$where .= Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_alta') . ' AND ';
	$where = trim($where, ' AND ');
	$orderBy = (empty($arrayOrdenarPor[$ordenarPor]) ? '' : ' ORDER BY ' . $arrayOrdenarPor[$ordenarPor]);
	$tickets = Factory::getInstance()->getListObject('KoiTicket', $where . $orderBy);
	foreach ($tickets as $ticket) {
		/** @var KoiTicket $ticket */
		$ticket->expand();
	}
	Html::jsonEncode('', $tickets);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>