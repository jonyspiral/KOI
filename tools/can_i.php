<?php require __DIR__.'/../premaster.php';
try{ UsuarioLogin::login(); }catch(Exception $e){}
$p = isset($_GET['p']) ? $_GET['p'] : '';
header('Content-Type: text/plain; charset=iso-8859-1');
echo "p=$p\n";
echo "puede? ".(Usuario::logueado(true)->puede($p) ? 'SI' : 'NO')."\n";
