<?php
@session_start();
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
$ip   = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-';
$user = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '-';
$msg  = isset($_POST['msg'])  ? $_POST['msg']  : '';
$src  = isset($_POST['src'])  ? $_POST['src']  : '';
$line = isset($_POST['line']) ? $_POST['line'] : '';
$col  = isset($_POST['col'])  ? $_POST['col']  : '';
$stk  = isset($_POST['stack'])? $_POST['stack']: '';
error_log("[JSERROR] user=$user ip=$ip src=$src line=$line:$col msg=$msg stack=$stk");
http_response_code(204); // sin body
exit;
