<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/predespachos/reimpresion/buscar/')) { ?>
<?php

$tipo = Funciones::get('tipo');
$idCliente = Funciones::get('idCliente');
$idSucursal = Funciones::get('idSucursal');
$idPedido = Funciones::get('idPedido');

try {
	if ($tipo == 'C') {
		Factory::getInstance()->getSucursal($idCliente, $idSucursal)->abrirPredespachados();
	} elseif ($tipo == 'P') {
		Factory::getInstance()->getPedido($idPedido)->abrirPredespachados();
	} else {
		throw new FactoryExceptionCustomException('Tipo de predespacho inexistente');
	}
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>