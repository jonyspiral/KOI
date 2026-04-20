<?php

include_once 'initialize.php';

if (count($stores) == 1) {
	foreach ($stores as $store) {
		$store->delete();
	}
	logg('SUCCESS', 'Delete', 'El store ' . $requestModel['NOMSUC'] . ' del cliente ' . $requestModel['RZNSOCIAL'] . ' fue eliminado correctamente');
	response();
}

?>