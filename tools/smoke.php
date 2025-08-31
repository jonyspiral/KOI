<?php
require __DIR__.'/../factory/Factory.php';
$db = Factory::getInstance()->db();
$now = $db->value("SELECT NOW()");
$logDir = __DIR__.'/../logs';
$okWrite = @file_put_contents($logDir.'/smoke.log', "[".date('c')."] ping\n", FILE_APPEND);
header('Content-Type: text/plain; charset=UTF-8');
echo "DB: ". $db->value("SELECT DATABASE()") . "\n";
echo "NOW: $now\n";
echo "WRITE logs/: ".($okWrite!==false ? "OK" : "FAIL")."\n";
