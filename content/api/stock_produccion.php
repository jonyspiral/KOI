<?php
ini_set('display_errors','0');
header('Content-Type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET');

	require_once __DIR__.'/funciones.php';

	$codigoArticulo = sqlEscape($_GET['articulo']);
	$codigoColor = sqlEscape($_GET['color']);

	if (!$codigoArticulo && !$codigoColor) {
		echo json_encode(array('error' => 'no hay filtros para buscar')); die;
	}

	echo json_encode(getStockEnProduccion($codigoArticulo, $codigoColor));die;