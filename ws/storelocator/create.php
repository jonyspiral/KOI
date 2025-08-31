<?php

include_once 'initialize.php';

if (count($stores)) {
	logg('INFO', 'Create', 'El store buscado para CREATE ya existe. Se pasa a UPDATE');
	include_once 'update.php';
} elseif (count($stores) == 0) {
	$db->spiral_stores()->insert($requestModel);
	logg('SUCCESS', 'Create', 'El store ' . $requestModel['NOMSUC'] . ' del cliente ' . $requestModel['RZNSOCIAL'] . ' fue creado correctamente');
	response();
}

?>