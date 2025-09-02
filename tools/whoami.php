<?php
@session_start();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
  'sid'     => session_id(),
  'keys'    => array_keys($_SESSION),
  'session' => $_SESSION,
], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
