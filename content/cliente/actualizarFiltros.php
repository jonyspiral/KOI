<?php require_once('../../premaster.php'); ?>
<?php

$filtros = (array_key_exists('filtros', $_REQUEST)) ? $_REQUEST['filtros'] : array();
Funciones::session('catalogo_filtros', json_encode($filtros));

?>

