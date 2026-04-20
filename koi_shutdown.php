<?php
register_shutdown_function(function () {
  $e = error_get_last();
  if ($e && in_array($e['type'], [E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR])) {
    if (ob_get_length()) { ob_clean(); }
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['status'=>500,'message'=>'fatal','data'=>[]]);
    @file_put_contents('/tmp/error_favoritos_shutdown.log', date('c').' '.print_r($e,true).PHP_EOL, FILE_APPEND);
  }
});
