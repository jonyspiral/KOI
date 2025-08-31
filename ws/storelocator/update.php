<?php

include_once 'initialize.php';

if (!count($stores)) {
	logg('INFO', 'Update', 'El store buscado para UPDATE no existe. Se pasa a CREATE');
	include_once 'create.php';
} elseif (count($stores) == 1) {
	foreach ($stores as $store) {
		$store['RZNSOCIAL'] = $requestModel['RZNSOCIAL'];
		$store['NOMFANTA'] = $requestModel['NOMFANTA'];
		$store['NOMSUC'] = $requestModel['NOMSUC'];
		$store['CALLE'] = $requestModel['CALLE'];
		$store['CALLENUM'] = $requestModel['CALLENUM'];
		$store['LOCALIDAD'] = $requestModel['LOCALIDAD'];
		$store['PROVINCIA'] = $requestModel['PROVINCIA'];
		$store['LAT'] = $requestModel['LAT'];
		$store['LNG'] = $requestModel['LNG'];
		$store['formatted_address'] = $requestModel['formatted_address'];
		$store['estado'] = $requestModel['estado'];
		$store->update();
	}
	logg('SUCCESS', 'Update', 'El store ' . $requestModel['NOMSUC'] . ' del cliente ' . $requestModel['RZNSOCIAL'] . ' fue actualizado correctamente');
	response();
}

?>