<?php

error_reporting(E_ERROR | E_PARSE);

define('WSNAME', 'emails');

define('BASEPATH', realpath(getcwd() . '/' . WSNAME . '/') . DIRECTORY_SEPARATOR);

define('LOGSPATH', BASEPATH . 'logs/');

require_once BASEPATH . '../../includes.php';

function logg($tipo, $metodo, $descripcion = '') {
	$echo = '<span style="font-weight: bold; color: ' . (($tipo == 'ERROR') ? 'red' : ($tipo == 'SUCCESS' ? 'green' : ($tipo == 'INFO' ? 'blue' : 'black'))) . '">';
	$echo .= date('H:i:s', time()) . ' || ';
	$echo .= $tipo . ' || ';
	$echo .= $metodo . ' ';
	$echo .= '</span>';
	$echo .= ' [' . $descripcion . ']';
	$echo .= '<br><br>';

	$fp = fopen(LOGSPATH . date('Y-m-d') . '.html', 'a+');
	fwrite($fp, $echo);
	fclose($fp);
}

$mutex = new Mutex('EnvioEmails');
try {
	$mutex->lock();
	UsuarioLogin::login('emails', 'bc533432cc6497178f2006a3600d129dfe7c471a');

	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'fecha_enviado IS NULL AND ';
	$where .= 'fecha_programada < GETDATE()';
	$order = 'ORDER BY fecha_programada ASC, cod_email ASC';
	$emails = Factory::getInstance()->getListObject('Email', $where . $order);

	if (!count($emails)) {
		logg('INFO', 'EnvioEmails', 'No hay emails en cola para enviar');
	}
	foreach ($emails as $email) {
		/** @var Email $email */
		try {
			$email->enviarReal();
			logg('SUCCESS', 'EnvioEmails', 'Se ha enviado correctamente el email ' . $email->id . ' - "' . $email->asunto . '"');
		} catch (Exception $ex) {
			logg('ERROR', 'EnvioEmails', 'Ocurrio un error al intentar enviar el email ' . $email->id . ' - "' . $email->asunto . '" (' . $ex->getMessage() . ')');
		}
	}
	$mutex->unlock();
} catch (Exception $ex) {
	$mutex->unlock();
	logg('ERROR', 'EnvioEmails', 'Ocurrio un error general al intentar llamar al envio de emails (' . $ex->getMessage() . ')');
	throw $ex;
}
