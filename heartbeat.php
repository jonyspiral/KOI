<?php
require __DIR__.'/premaster.php';
try{ UsuarioLogin::login(); }catch(Exception $e){}
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'tipo' => 'OK',
  'msg'  => '',
  'notificaciones' => [],
  'anuladas' => [],
  'vistas' => [],
  'ultimaFechaHora' => date('Y-m-d H:i:s'),
], JSON_UNESCAPED_UNICODE);
