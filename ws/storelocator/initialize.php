<?php

/**
 * Este webservice no está en KOI sino en spiralshoes.com
 * Sirve para actualizar los stores/sucursales cuando hay cambios en Koi
 */

error_reporting(-1);

function logg($tipo, $metodo, $descripcion = '') {
	$echo = '<span style="font-weight: bold; color: ' . (($tipo == 'ERROR') ? 'red' : ($tipo == 'SUCCESS' ? 'green' : ($tipo == 'INFO' ? 'blue' : 'black'))) . '">';
	$echo .= date('H:i:s', time()) . ' || ';
	$echo .= $tipo . ' || ';
	$echo .= $metodo . ' ';
	$echo .= '</span>';
	$echo .= ' [' . $descripcion . ']';
	$echo .= '<br><br>';

	$fp = fopen(getcwd() . '/logs/' . date('Y-m-d') . '.html', 'a+');
	fwrite($fp, $echo);
	fclose($fp);
}

function response($code = 0, $message = 'Success') {
	echo json_encode(array(
		'response' => array(
			'error' => $code,
			'message' => $message
		)
	));
}

$input = json_decode(file_get_contents('php://input'), true);
$request = $input['request'];
if (!isset($request['sessionkey']) || !isset($request['model'])) {
	logg('ERROR', 'Bad request', '(400) El request no tiene un formato válido');
	throw new Exception('La clave de acceso es incorrecta', 400);
}
if ($request['sessionkey'] !== 'ACAUNACLAVE') {
	logg('ERROR', 'Auth', '(401) La clave de acceso es incorrecta');
	throw new Exception('La clave de acceso es incorrecta', 401);
}

$requestModel = $request['model'];

include_once 'NotORM.php';

$host = 'localhost';
$dbname = 'ph000414_spiralnews';
$user = 'ph000414_ecuser';
$password = 'Eshop2014';
//$user = 'root';
//$password = '';

try {
	$pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
	$db = new NotORM($pdo);
	$stores = $db->spiral_stores()->select('*')->where('CODCLI', $requestModel['CODCLI'])->and('CODSUC', $requestModel['CODSUC']);
} catch (Exception $ex) {
	logg('ERROR', 'DB Connection', '(' . $ex->getCode() . ') ' . $ex->getMessage());
	throw $ex;
}

?>