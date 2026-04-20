<?php
require __DIR__.'/../includes.php';
ini_set('display_errors',1); error_reporting(E_ALL);

$p = isset($_GET['pagename']) ? trim($_GET['pagename'],'/') : '';
$base = rtrim(Config::pathBase,'/').'/content/';
$f1 = $base.$p.'.php';
$f2 = $base.$p.'/index.php';

$resolved = file_exists($f1) ? $f1 : (file_exists($f2) ? $f2 : null);
$perm = $p && substr($p,-5)!=='/index' ? $p.'/index' : $p; // convención típica

header('Content-Type: text/plain; charset=iso-8859-1');
echo "pagename={$p}\n";
echo "exists(file)      = ".(file_exists($f1)?'SI':'NO')." -> $f1\n";
echo "exists(folder/ix) = ".(file_exists($f2)?'SI':'NO')." -> $f2\n";
echo "resolved          = ".($resolved?:'N/A')."\n";
if (class_exists('Usuario')) {
  $u = @Usuario::logueado(true);
  if ($u) {
    echo "usuario_id        = ".(@$u->id?:'N/A')."\n";
    echo "permiso a chequear= {$perm}\n";
    $puede = (method_exists($u,'puede') ? ($u->puede($perm)?'SI':'NO') : 'N/A');
    echo "puede?            = {$puede}\n";
  } else {
    echo "usuario_id        = N/A (no logueado)\n";
  }
}
