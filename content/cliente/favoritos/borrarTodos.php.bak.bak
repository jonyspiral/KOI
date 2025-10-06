<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);

/* CORS */
$origin = isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'';
$allow  = array('http://192.168.2.210:8195','http://192.168.2.210:8081','http://encinitas.local:8195');
if (in_array($origin,$allow,true)) { header('Access-Control-Allow-Origin: '.$origin); header('Access-Control-Allow-Credentials: true'); header('Vary: Origin'); }
else { header('Access-Control-Allow-Origin: http://192.168.2.210:8195'); }
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: POST, OPTIONS');
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']==='OPTIONS') { header('Content-Type: application/json; charset=utf-8'); echo json_encode(array('status'=>204,'message'=>'preflight')); exit; }

/* log */
$__log = __DIR__ . '/../tmp/borrarTodos.debug.log';
@is_dir(dirname($__log)) || @mkdir(dirname($__log), 0777, true);
function __dbg($l,$d=null){ @file_put_contents($GLOBALS['__log'], '['.date('c')."] $l".(is_null($d)?'':' | '.json_encode($d)).PHP_EOL, FILE_APPEND); }

/* sesión antes del premaster */
if (function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
__dbg('== borrarTodos.php START ==', array('sid'=>function_exists('session_id')?session_id():null));

require_once('../../../premaster.php');
header('Content-Type: application/json; charset=utf-8');

/* ACL */
$u = Usuario::logueado();
if (!$u || !is_object($u)) { echo json_encode(array('status'=>403,'message'=>'No logueado','data'=>array())); exit; }
if (!$u->puede('cliente/favoritos/borrar/')) { echo json_encode(array('status'=>403,'message'=>'Permiso denegado','data'=>array())); exit; }

/* cliente */
$clienteId = (isset($u->cliente) && isset($u->cliente->id)) ? $u->cliente->id : null;
if (!$clienteId) { echo json_encode(array('status'=>400,'message'=>'Cliente no asociado a la sesión','data'=>array())); exit; }

/* delete all */
$sql = "DELETE FROM favoritos_cliente WHERE cod_cliente = '".addslashes($clienteId)."'; ";
__dbg('DELETE_ALL.sql', $sql);
try {
  Datos::EjecutarSQLsinQuery($sql);
  echo json_encode(array('status'=>200,'message'=>'success','data'=>array('deleted_all'=>true)));
} catch (Exception $ex) {
  __dbg('DELETE_ALL.err', $ex->getMessage());
  echo json_encode(array('status'=>500,'message'=>$ex->getMessage(),'data'=>array()));
}
exit;
