<?php
header("Content-Type: text/plain; charset=utf-8");

$host = "192.168.2.210";
$db   = "koi1_stage";
$user = "koiuser";
$pass = "Route667?";

$t0 = microtime(true);
$mysqli = @new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo "ERR {$mysqli->connect_errno}: {$mysqli->connect_error}\n";
    exit;
}

$lat = sprintf("%.3f", (microtime(true) - $t0) * 1000);
$r   = $mysqli->query("SELECT NOW() AS now");
$now = $r ? $r->fetch_assoc()["now"] : "(sin resultado)";

echo "OK host=$host db=$db now=$now latency_ms=$lat\n";

$mysqli->close();
