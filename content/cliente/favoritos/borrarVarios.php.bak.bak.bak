<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

/* ==== CORS con credenciales ==== */
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$allow  = array('http://192.168.2.210:8195','http://192.168.2.210:8081','http://encinitas.local:8195');
if (in_array($origin,$allow,true)) { header('Access-Control-Allow-Origin: '.$origin); header('Access-Control-Allow-Credentials: true'); header('Vary: Origin'); }
else { header('Access-Control-Allow-Origin: http://192.168.2.210:8195'); }
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: POST, OPTIONS');
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']==='OPTIONS') {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(array('status'=>204,'message'=>'preflight')); exit;
}

/* ==== logger robusto ==== */
$__candidates = array(
  __DIR__.'/../tmp/borrarVarios.debug.log',
  '/tmp/borrarVarios.debug.log'
);
$__logPath = null;
foreach ($__candidates as $cand) {
  $dir = dirname($cand);
  if (!is_dir($dir)) @mkdir($dir, 0777, true);
  if (is_dir($dir) && is_writable($dir)) { $__logPath = $cand; break; }
}
if ($__logPath) header('X-Fav-Log-Path: '.$__logPath);

function __j($d){ $j=@json_encode($d, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); return $j!==false?$j:var_export($d,true); }
function __dbg($l,$d=null){
  $line='['.date('c').'] '.$l.(is_null($d)?'':' | '.__j($d));
  if (!empty($GLOBALS['__logPath'])) { @file_put_contents($GLOBALS['__logPath'],$line.PHP_EOL,FILE_APPEND|LOCK_EX); }
  @error_log('[borrarVarios] '.$line); // SIEMPRE duplica en error_log
}
register_shutdown_function(function(){ __dbg('SHUTDOWN', error_get_last()); });

/* ==== sesión ANTES del premaster ==== */
if (function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
__dbg('== borrarVarios.php START ==', array('sid'=>function_exists('session_id')?session_id():null));

require_once('../../../premaster.php');
header('Content-Type: application/json; charset=utf-8');


if (function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) {
  @session_start();
  __dbg('SESSION_STARTED', array('ok'=>true,'sid'=>session_id()));
} else {
  __dbg('SESSION_STARTED', array('ok'=>false,'sid'=>function_exists('session_id')?session_id():null));
}
__dbg('SESSION_KEYS', array(
  'usuarioLogueadoUser'=>isset($_SESSION['usuarioLogueadoUser']),
  'usuarioLogueadoPass'=>isset($_SESSION['usuarioLogueadoPass']),
  'empresa'=>isset($_SESSION['empresa'])?$_SESSION['empresa']:null
));

/* ==== ACL / usuario ==== */
try {
  $usr = Usuario::logueado();
  __dbg('USUARIO_LOGUEADO_TYPE', gettype($usr));
  if (is_object($usr)) { __dbg('USUARIO_LOGUEADO_CLASS', get_class($usr)); }

  if (!$usr || !is_object($usr)) {
    __dbg('ACL_DENEGADO_SIN_USUARIO');
    echo json_encode(array('status'=>403,'message'=>'No logueado','data'=>array())); exit;
  }

  $puede = $usr->puede('cliente/favoritos/borrar/');
  __dbg('ACL', array('puede'=>$puede));
  if (!$puede) {
    echo json_encode(array('status'=>403,'message'=>'Permiso denegado','data'=>array())); exit;
  }
} catch (Exception $e) {
  __dbg('ACL_EXCEPTION', array('msg'=>$e->getMessage()));
  echo json_encode(array('status'=>500,'message'=>'Error ACL: '.$e->getMessage(),'data'=>array())); exit;
}

/* ==== INPUT ==== */
$raw = @file_get_contents('php://input');
__dbg('RAW', array('len'=>strlen($raw),'preview'=>substr($raw,0,512)));

$decoded = json_decode($raw, true);
$je  = function_exists('json_last_error')     ? json_last_error()     : null;
$jem = function_exists('json_last_error_msg') ? json_last_error_msg() : null;
__dbg('JSON_DECODE', array('err'=>$je,'msg'=>$jem,'is_array'=>is_array($decoded)));

$lista = array();
if (is_array($decoded)) {
  // Acepto {favorites:[...]} o directamente [...]
  if (isset($decoded['favorites']) && is_array($decoded['favorites'])) {
    $lista = $decoded['favorites'];
  } elseif (isset($decoded[0]) || empty($decoded)) {
    $lista = $decoded;
  }
}
if (!is_array($lista)) { $lista = array(); }

if (!count($lista)) {
  __dbg('EMPTY_LIST');
  echo json_encode(array('status'=>400,'message'=>'Formato inválido: se requiere {"favorites":[...] }','data'=>array()));
  exit;
}

/* ==== cliente actual ==== */
$clienteId = null;
try {
  $cliObj = $usr->cliente; // fuerza carga perezosa
  if (isset($cliObj->id)) { $clienteId = $cliObj->id; }
} catch (Exception $e) {}
__dbg('CLIENTE', array('id'=>$clienteId));

if (!$clienteId) {
  __dbg('CLIENTE_NULL');
  echo json_encode(array('status'=>400,'message'=>'Cliente no asociado a la sesión','data'=>array()));
  exit;
}

/* ==== BORRADO ==== */
$response = array();
__dbg('FAVORITES_COUNT', array('count'=>count($lista)));

foreach ($lista as $idx => $fav) {
  __dbg('ITEM_IN', array('idx'=>$idx,'fav'=>$fav));
  try {
    $idArticulo = isset($fav['idArticulo']) ? $fav['idArticulo'] : null;
    $idColor    = null;
    if (isset($fav['idColorPorArticulo'])) { $idColor = $fav['idColorPorArticulo']; }
    elseif (isset($fav['idColor']))       { $idColor = $fav['idColor']; }

    // Normalizo
    $idArticulo = trim((string)$idArticulo);
    $idColor    = strtoupper(trim((string)$idColor));
    __dbg('ITEM_KEYS', array('idArticulo'=>$idArticulo,'idColorPorArticulo'=>$idColor));

    if ($idArticulo === '' || $idColor === '') {
      $msg = 'Faltan claves idArticulo/idColorPorArticulo';
      __dbg('ITEM_KEYS_MISSING', array('msg'=>$msg));
      $response[] = array('idArticulo'=>$idArticulo,'idColorPorArticulo'=>$idColor,'removed'=>false,'message'=>$msg);
      continue;
    }

    // DELETE directo (sin Factory) para evitar issues de transacciones heredadas
    $cCli = Datos::objectToDB($clienteId);
    $cArt = Datos::objectToDB($idArticulo);
    $cCol = Datos::objectToDB($idColor);

    $sql = "DELETE FROM favoritos_cliente ".
           "WHERE cod_cliente = {$cCli} AND cod_articulo = {$cArt} AND cod_color_articulo = {$cCol}; ";
    __dbg('DELETE.sql', $sql);

    // Ejecuta y registra filas afectadas si tu driver lo expone (si no, igual dispara excepciones si falla)
    Datos::EjecutarSQLsinQuery($sql);

    $response[] = array(
      'idArticulo' => $idArticulo,
      'idColorPorArticulo' => $idColor,
      'removed' => true,
      'message' => 'Eliminado'
    );
    __dbg('DELETE_OK', array('key'=>$idArticulo.'_'.$idColor));

  } catch (Exception $ex) {
    __dbg('ERROR_ITEM', array('msg'=>$ex->getMessage()));
    $response[] = array(
      'idArticulo' => isset($idArticulo)?$idArticulo:null,
      'idColorPorArticulo' => isset($idColor)?$idColor:null,
      'removed' => false,
      'message' => $ex->getMessage()
    );
  }
}

/* ==== salida ==== */
if (function_exists('headers_list')) { __dbg('headers_final', headers_list()); }
echo json_encode(array('status'=>200,'message'=>'success','data'=>$response));
__dbg('END');
exit;
