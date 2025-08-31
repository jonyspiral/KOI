<?php
require __DIR__.'/../premaster.php';
try { UsuarioLogin::login(); } catch (Exception $e) {}

$base = realpath(__DIR__.'/..');            // raíz de la app
$aux  = Funciones::get('pagename');        // puede ser null
$pref = Usuario::logueado(true)->esCliente() ? 'cliente/' : '';
$pg   = $pref . ($aux ?: 'index');

header('Content-Type: text/plain; charset=iso-8859-1');
echo "base=$base\n";
echo "prefix=$pref\n";
echo "aux=$aux\n";
echo "pg=$pg\n";
echo "exists($base/content/$pg.php)=".(file_exists("$base/content/$pg.php")?'SI':'NO')."\n";
echo "exists($base/content/$pg/index.php)=".(file_exists("$base/content/$pg/index.php")?'SI':'NO')."\n";
