<?php
@ini_set('log_errors', 1);
@ini_set('error_log', '/var/www/encinitas/logs/php_errors.log');
@require_once __DIR__.'/trace_fatal.php';

$__t0 = microtime(true);
if (function_exists('session_id') && session_id()==='') @session_start();

if (!function_exists('__tlog')) {
  function __tlog($m){ @error_log('[TRACE] '.date('Y-m-d H:i:s').' '.$m); }
}
__tlog('BEGIN uri='.@$_SERVER['REQUEST_URI'].' pagename='.(isset($_GET['pagename'])?$_GET['pagename']:'(none)').' ip='.(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'-'));

if (!function_exists('__trace_shutdown')) {
  function __trace_shutdown() {
    $dur = sprintf('%.1fms', (microtime(true)-$GLOBALS['__t0'])*1000);
    $sess = array(
      'usuario'          => isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '-',
      'cliente_mayorista'=> isset($_SESSION['cliente_mayorista']) ? $_SESSION['cliente_mayorista'] : '-',
      'empresa_oculta'   => isset($_SESSION['empresa_oculta']) ? $_SESSION['empresa_oculta'] : '-',
      'user_type'        => isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '-',
    );
    $headers = function_exists('headers_list') ? @headers_list() : array();
    $inc = get_included_files();
    $last = array_slice($inc, -6);
    if (function_exists('json_encode')) {
      @error_log('[TRACE] END dur='.$dur.' sess='.json_encode($sess).' headers='.json_encode($headers).' last_includes='.json_encode($last));
    } else {
      @error_log('[TRACE] END dur='.$dur);
    }
  }
}
register_shutdown_function('__trace_shutdown');
