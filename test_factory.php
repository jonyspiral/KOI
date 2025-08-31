<?php
require __DIR__.'/factory/Factory.php';
$db = Factory::getInstance()->db();  // usa el método de instancia
var_dump($db->queryOne("SELECT DATABASE() AS db, NOW() AS now, 1 AS ok"));
