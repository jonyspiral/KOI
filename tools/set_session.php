<?php
session_start();
if (isset($_GET['empresa'])) $_SESSION['empresa'] = $_GET['empresa'];
if (isset($_GET['tipo']))    $_SESSION['user_type'] = $_GET['tipo'];
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['sid'=>session_id(),'session'=>$_SESSION], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
