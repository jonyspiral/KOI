<?php
require __DIR__.'/../premaster.php';
header('Content-Type: text/plain; charset=utf-8');
try{ UsuarioLogin::login(); }catch(Exception $e){}

$pagename = 'index';
echo "TRACE: antes de include main.php\n";
include __DIR__.'/../main.php';
echo "TRACE: después de include main.php\n";
