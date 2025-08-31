<?php
require __DIR__.'/../premaster.php';
try { UsuarioLogin::login(); } catch(Exception $e) {}

$aux = isset($_GET['p']) ? trim($_GET['p'], "/") : '';
$prefix = Usuario::logueado(true)->esCliente() ? 'cliente/' : '';
$pagename = $aux !== '' ? $prefix.$aux : $prefix.'index';

// normalización rápida
$f1 = __DIR__."/../content/$pagename.php";
$f2 = __DIR__."/../content/$pagename/index.php";
if (!is_file($f1) && is_file($f2)) $pagename .= '/index';
if (!is_file($f1) && !is_file($f2)) $pagename = $prefix.'index';

$GLOBALS['pagename'] = $pagename;
$titPart = basename($pagename);
$GLOBALS['titulo']   = ucfirst(str_replace('_',' ', $titPart));

// usá SIEMPRE el layout común
include __DIR__."/../main.php";
