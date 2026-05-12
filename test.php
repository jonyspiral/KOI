<?php

require_once('factory/Cache.php');

//Cache::set('hola2', 'as2d', 'un tag', 200);

$clase = 'Personal';

$query = "SELECT TOP 10 * FROM personal WHERE (anulado = 'N') AND (cod_personal LIKE '%%' OR legajo_nro LIKE '%%' OR apellido LIKE '%%' OR nombres LIKE '%%'); ";

$hash = md5($query);

$result = Cache::get($hash, $clase);

var_dump($result);

//Cache::deleteAllByTag('un tag');

//var_dump(Cache::stats());

?>