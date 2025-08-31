<?php
$ln = mysqli_init();
mysqli_options($ln, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
mysqli_options($ln, MYSQLI_SET_CHARSET_NAME, 'utf8');
if (!mysqli_real_connect($ln,'192.168.2.210','koi1_php56','Route667?','koi2',3306)) {
  die('Conn error: '.mysqli_connect_error());
}
mysqli_set_charset($ln,'utf8');
mysqli_query($ln,"SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
mysqli_query($ln,"SET time_zone='-03:00'");
$r = mysqli_query($ln,"SELECT @@version AS mysql, @@character_set_server AS cs_srv, @@collation_server AS coll_srv, NOW() AS now, 1 AS ok");
var_dump(mysqli_fetch_assoc($r));
