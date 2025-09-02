<?php
require_once __DIR__ . '/../factory/drivers/DbMysql.php';
$d = new DbMysql(); // o como instanciás normalmente
$r = $d->query("SELECT @@character_set_connection cset, @@collation_connection coll;")->fetch_assoc();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($r, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
