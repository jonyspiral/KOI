<?php
require __DIR__.'/../factory/Factory.php';
$db = Factory::getInstance()->db();
echo "DB actual: ".$db->value("SELECT DATABASE()");
