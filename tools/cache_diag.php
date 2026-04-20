<?php
error_reporting(E_ALL); ini_set('display_errors',1);
echo "PHP: ".PHP_VERSION."\n";
echo "EXT(memcache): ".(class_exists('Memcache')?'SI':'NO')."\n";
$m = new Memcache();
$ok = @$m->connect('127.0.0.1',11211);
echo "CONNECT: ".($ok?'OK':'FAIL')."\n";
if($ok){
  $m->set('k','v',0,10);
  echo "RW: ".($m->get('k')==='v'?'OK':'FAIL')."\n";
}
