<?php
// Loguea fatales/parse antes de que el script muera
if (!function_exists('_trace_fatal_guard')) {
  function _trace_fatal_guard() {
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR))) {
      $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-';
      $sid = function_exists('session_id') ? session_id() : '-';
      $ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-';
      @error_log('[FATAL] uri='.$uri.' ip='.$ip.' sid='.$sid.' file='.$e['file'].' line='.$e['line'].' msg='.$e['message']);
    }
  }
  register_shutdown_function('_trace_fatal_guard');
}
