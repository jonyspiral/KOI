<?php
@ini_set('log_errors', 1);
@ini_set('error_log', '/var/www/encinitas/logs/php_errors.log');
if (session_id()==='') { @session_start(); }
/* Simulación mínima de sesión mayorista (solo para diagnóstico) */
$_SESSION['usuario'] = 'diag_mayorista';
$_SESSION['cliente_mayorista'] = 'S';
$_SESSION['empresa_oculta'] = 'N';
/* Redirige a master con pagename explícito */
header('Location: /master.php?pagename=cliente/main&ts='.time());
exit;
