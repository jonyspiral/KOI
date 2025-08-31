<?php
header("Content-Type: text/plain; charset=utf-8");
session_name("KOITEST");
ini_set("session.save_path","/tmp");
 = microtime(true);
session_start();
session_write_close();
 = (microtime(true)-)*1000;
echo "SESS-OK latency_ms=" . sprintf("%.2f",) . "\n";
flush(); @ob_flush();
