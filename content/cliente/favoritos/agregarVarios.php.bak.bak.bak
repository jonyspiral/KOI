<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ==== CORS con credenciales (sin *) ==== */
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$allow  = array(
  'http://192.168.2.210:8195',
  'http://192.168.2.210:8081',
  'http://encinitas.local:8195'
);
if (in_array($origin, $allow, true)) {
  header('Access-Control-Allow-Origin: ' . $origin);
  header('Access-Control-Allow-Credentials: true');
  header('Vary: Origin');
} else {
  header('Access-Control-Allow-Origin: http://192.168.2.210:8195');
}
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: POST, OPTIONS');

/* Preflight */
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(array('status'=>204,'message'=>'preflight'));
  exit;
}

/* ===== logger bootstrap (no depende de clases) ===== */
$__logCandidates = array(__DIR__.'/../tmp/agregarVarios.debug.log','/tmp/agregarVarios.debug.log');
$__logFile = null;
foreach ($__logCandidates as $__cand) {
  $__dir = dirname($__cand);
  if (!is_dir($__dir)) { @mkdir($__dir, 0777, true); }
  if (is_dir($__dir) && is_writable($__dir)) { $__logFile = $__cand; break; }
}
function __json_enc($d){ $j=json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); return $j===false?var_export($d,true):$j; }
function __dbg($label,$data=null){ $line='['.date('c').'] '.$label.(is_null($data)?'':' | '.__json_enc($data));
  if (!empty($GLOBALS['__logFile'])) { @file_put_contents($GLOBALS['__logFile'],$line.PHP_EOL,FILE_APPEND); } else { @error_log('[agregarVarios] '.$line); } }
register_shutdown_function(function(){ __dbg('SHUTDOWN', error_get_last()); });
set_error_handler(function($no,$str,$file,$line){ __dbg('PHP_ERROR', compact('no','str','file','line')); return false; });

__dbg('== agregarVarios.php START ==');
__dbg('SERVER_METHOD_CT', array(
  'REQUEST_METHOD' => isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null,
  'CONTENT_TYPE'   => isset($_SERVER['CONTENT_TYPE'])?$_SERVER['CONTENT_TYPE']:null
));

/* (Opcional) STUB Memcache si la extensión no está cargada */
if (!class_exists('Memcache')) {
  class Memcache {
    public function connect($host=null,$port=null){ return false; }
    public function get($key){ return false; }
    public function set($key,$val,$flags=0,$ttl=0){ return true; }
    public function delete($key){ return true; }
    public function close(){ return true; }
  }
}

/* === ABRIR SESIÓN ANTES DEL PREMASTER === */
__dbg('COOKIE_HDR', isset($_SERVER['HTTP_COOKIE'])?$_SERVER['HTTP_COOKIE']:'(sin HTTP_COOKIE)');
if (function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) {
  @session_start();
  __dbg('SESSION_STARTED_PREMASTER', array('sid'=>session_id()));
} else {
  __dbg('SESSION_ALREADY_ACTIVE', array('sid'=>function_exists('session_id')?session_id():null));
}

/* ==== cargar clases/autoload/login ==== */
__dbg('PREMASTER_EXISTS', array('exists'=>file_exists('../../../premaster.php')));
require_once('../../../premaster.php');  // UsuarioLogin::login() ahora ve $_SESSION
header('Content-Type: application/json; charset=utf-8');

/* === DIAGNÓSTICO SESIÓN === */
__dbg('ORIGIN_HDR', isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '(sin ORIGIN)');
__dbg('COOKIE_HDR', isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : '(sin HTTP_COOKIE)');
if (function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
    __dbg('SESSION_STARTED', array('ok' => true, 'sid' => session_id()));
} else {
    __dbg('SESSION_STARTED', array('ok' => false, 'sid' => function_exists('session_id') ? session_id() : null));
}
__dbg('SESSION_META', array(
    'name'       => function_exists('session_name') ? session_name() : null,
    'save_path'  => ini_get('session.save_path'),
    'params'     => function_exists('session_get_cookie_params') ? session_get_cookie_params() : null
));
__dbg('SESSION_KEYS', array(
    'usuarioLogueadoUser' => isset($_SESSION['usuarioLogueadoUser']),
    'usuarioLogueadoPass' => isset($_SESSION['usuarioLogueadoPass']),
    'empresa'             => isset($_SESSION['empresa']) ? $_SESSION['empresa'] : null
));

header('Content-Type: application/json; charset=utf-8');
if (function_exists('headers_list')) { __dbg('headers_after_premaster', headers_list()); }

/* ===== ACL ===== */
try {
    $usr = Usuario::logueado();
    __dbg('USUARIO_LOGUEADO_TYPE', gettype($usr));
    if (!$usr || !is_object($usr)) {
        __dbg('ACL_DENEGADO_SIN_USUARIO');
        echo json_encode(array('status'=>403,'message'=>'No hay sesión de cliente en este request','data'=>array()));
        exit;
    }
    __dbg('USUARIO_LOGUEADO_CLASS', get_class($usr));

    $puede = $usr->puede('cliente/favoritos/agregar/');
    __dbg('ACL', array('puede'=>$puede));
    if (!$puede) {
        __dbg('ACL_DENEGADO_POR_PERMISO');
        echo json_encode(array('status'=>403,'message'=>'Permiso denegado','data'=>array()));
        exit;
    }
} catch (Exception $e) {
    __dbg('ACL_EXCEPTION', array('msg'=>$e->getMessage()));
    echo json_encode(array('status'=>500,'message'=>'Error evaluando ACL: '.$e->getMessage(),'data'=>array()));
    exit;
}

/* ===== INPUT ===== */
$raw = @file_get_contents('php://input');
__dbg('RAW', array('len'=>strlen($raw),'preview'=>substr($raw,0,512)));

$decoded = json_decode($raw, true);
$je  = function_exists('json_last_error')     ? json_last_error()     : null;
$jem = function_exists('json_last_error_msg') ? json_last_error_msg() : null;
__dbg('JSON_DECODE', array('err'=>$je,'msg'=>$jem,'is_array'=>is_array($decoded)));

if (!is_array($decoded) || !isset($decoded['favorites']) || !is_array($decoded['favorites'])) {
    __dbg('DECODE_INVALID', $decoded);
    echo json_encode(array('status'=>400,'message'=>'Formato inválido: se requiere {"favorites":[...]}','data'=>array()));
    exit;
}

/* cliente en sesión (si está) */
$clienteId = null;
try { if (isset($usr->cliente) && isset($usr->cliente->id)) { $clienteId = $usr->cliente->id; } } catch (Exception $e) {}
__dbg('CLIENTE', array('id'=>$clienteId));

$response = array();
__dbg('FAVORITES_COUNT', array('count'=>count($decoded['favorites'])));

/* ===== LOOP ===== */
foreach ($decoded['favorites'] as $idx => $fav) {
    __dbg('ITEM_IN', array('idx'=>$idx,'fav'=>$fav));
    try {
        /* 1) Claves de entrada */
        $idArticulo = isset($fav['idArticulo']) ? $fav['idArticulo'] : null;
        $idColorPorArticulo = null;
        if (isset($fav['idColorPorArticulo'])) { $idColorPorArticulo = $fav['idColorPorArticulo']; }
        elseif (isset($fav['idColor']))       { $idColorPorArticulo = $fav['idColor']; }

        /* 2) Normalización defensiva */
        $idArticulo         = trim((string)$idArticulo);
        $idColorPorArticulo = strtoupper(trim((string)$idColorPorArticulo));

        __dbg('ITEM_KEYS', array('idArticulo'=>$idArticulo, 'idColorPorArticulo'=>$idColorPorArticulo));
        __dbg('KEY_NORMALIZADA', array('key'=>$idArticulo . '_' . $idColorPorArticulo));

        if ($idArticulo === '' || $idColorPorArticulo === '') {
            $msg = 'Faltan claves idArticulo/idColorPorArticulo';
            __dbg('ITEM_KEYS_MISSING', array('msg'=>$msg));
            $response[] = array(
                'idArticulo'=>$idArticulo,
                'idColorPorArticulo'=>$idColorPorArticulo,
                'saved'=>false,
                'message'=>$msg
            );
            continue;
        }

        /* 3) Instanciar y setear relaciones */
        $favorito = FavoritoCliente::find(); // modo insert por defecto
        __dbg('FAVORITO_NEW');

        $favorito->cliente          = $usr->cliente;
        __dbg('SET_CLIENTE', array('clienteId'=> isset($favorito->cliente->id) ? $favorito->cliente->id : null));

        __dbg('GET_COLOR_POR_ARTICULO_BEFORE', array('idArticulo'=>$idArticulo,'idColorPorArticulo'=>$idColorPorArticulo));
        $favorito->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColorPorArticulo);
        __dbg('GET_COLOR_POR_ARTICULO_OK', array('colorId'=> isset($favorito->colorPorArticulo->id) ? $favorito->colorPorArticulo->id : null));

        $favorito->articulo         = $favorito->colorPorArticulo->articulo;
        __dbg('SET_ARTICULO', array('articuloCodigo'=> isset($favorito->articulo->codigo) ? $favorito->articulo->codigo : null));

        /* 4) Setear también los escalares de PK (clave para existeEnDB) */
        $favorito->idCliente          = $usr->cliente->id;
        $favorito->idArticulo         = $idArticulo;
        $favorito->idColorPorArticulo = $idColorPorArticulo;
        __dbg('FAV_PK_SCALARS', array(
            'idCliente' => $favorito->idCliente,
            'idArticulo' => $favorito->idArticulo,
            'idColorPorArticulo' => $favorito->idColorPorArticulo
        ));

        /* 4.b) (Debug opcional) Invocar métodos protegidos con Reflection */
        try {
            $rm = new ReflectionMethod(get_class($favorito), 'getQuery');
            $rm->setAccessible(true);
            $sqlSel = $rm->invoke($favorito, Modos::select);
            __dbg('FAV_SELECT_SQL', $sqlSel);
        } catch (Exception $eqq) {
            __dbg('FAV_SELECT_SQL_SKIPPED', array('msg'=>$eqq->getMessage()));
        }
        try {
            $rm2 = new ReflectionMethod(get_class($favorito), 'getPKWithValues');
            $rm2->setAccessible(true);
            $pkVals = $rm2->invoke($favorito);
            __dbg('FAV_PK_WITH_VALUES', $pkVals);
        } catch (Exception $eqq2) {
            __dbg('FAV_PK_WITH_VALUES_SKIPPED', array('msg'=>$eqq2->getMessage()));
        }

        /* 5) Guardar */
        $favorito->guardar();
        __dbg('GUARDAR_OK');

        $response[] = array(
            'idArticulo' => $idArticulo,
            'idColorPorArticulo' => $idColorPorArticulo,
            'saved' => true,
            'message' => 'Guardado'
        );

    } catch (FactoryExceptionRegistroExistente $ex) {
        __dbg('EXISTENTE', array('msg'=>$ex->getMessage()));
        $response[] = array(
            'idArticulo' => $idArticulo,
            'idColorPorArticulo' => $idColorPorArticulo,
            'saved' => true,
            'message' => 'Ya estaba guardado'
        );
    } catch (Exception $ex) {
        @file_put_contents('/tmp/error_favoritos.log', date('c') . ' - ' . $ex->getMessage() . PHP_EOL, FILE_APPEND);
        __dbg('ERROR_ITEM', array('msg'=>$ex->getMessage()));
        $response[] = array(
            'idArticulo' => $idArticulo,
            'idColorPorArticulo' => $idColorPorArticulo,
            'saved' => false,
            'message' => $ex->getMessage()
        );
    }
}



/* ===== RESPONSE ===== */
if (function_exists('headers_list')) { __dbg('headers_final', headers_list()); }
echo json_encode(array('status'=>200,'message'=>'success','data'=>$response));
__dbg('END');
exit;
