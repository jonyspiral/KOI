<?php
header('Content-Type: application/json; charset=utf-8');
$resp = ['ok'=>false];

try {
  require __DIR__.'/../premaster.php';
  try { UsuarioLogin::login(); } catch(Exception $e) {} // sesión opcional

  $aux = isset($_GET['p']) ? trim($_GET['p'], "/") : '';
  $scopePrefix = Usuario::logueado(true)->esCliente() ? 'cliente/' : '';
  $p = ($aux !== '') ? $scopePrefix.$aux : $scopePrefix.'index';

  $base = __DIR__.'/../content/';
  $f1 = $base.$p.'.php';
  $f2 = $base.$p.'/index.php';

  if (is_file($f1))      { $resolved = $p.'.php'; }
  elseif (is_file($f2))  { $resolved = $p.'/index.php'; }
  else                   { $resolved = $scopePrefix.'index.php'; }

  $permKey = preg_replace('#/index\.php$#','',$resolved);
  $permKey = preg_replace('#\.php$#','',$permKey);

  $puede = false;
  try { $puede = Usuario::logueado(true)->puede($permKey); } catch(Exception $e) {
    $resp['perm_error'] = $e->getMessage();
  }

  $resp += [
    'ok'       => true,
    'user'     => Usuario::logueado() ? Usuario::logueado(true)->id : null,
    'scope'    => ($scopePrefix ?: '(interno)'),
    'query_p'  => $aux,
    'resolved' => $resolved,
    'permKey'  => $permKey,
    'puede'    => $puede ? 'SI' : 'NO'
  ];
} catch(Exception $e) {
  $resp['error'] = $e->getMessage();
  $resp['file']  = $e->getFile();
  $resp['line']  = $e->getLine();
}

echo json_encode($resp, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
