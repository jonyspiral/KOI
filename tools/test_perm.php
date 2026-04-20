<?php
require __DIR__.'/../premaster.php';
header('Content-Type: text/plain; charset=utf-8');
try { UsuarioLogin::login(); $u=Usuario::logueado(true); }
catch(Exception $e){ echo "login error: ".$e->getMessage()."\n"; exit; }

$k = isset($_GET['k']) ? $_GET['k'] : 'index';
$t0 = microtime(true);
$ok = false; $err = '';
try { $ok = $u->puede($k); } catch(Exception $e){ $err = $e->getMessage(); }
$ms = (microtime(true)-$t0)*1000;

printf("user=%s key=%s ok=%s ms=%.1f err=%s\n", $u->id, $k, $ok?'SI':'NO', $ms, $err);
