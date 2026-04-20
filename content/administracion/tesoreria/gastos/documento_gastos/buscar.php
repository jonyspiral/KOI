<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/documento_gastos/buscar/')) { ?>
<?php

$idDocumentoProveedor = Funciones::get('idDocumentoProveedor');

try {
	$documentoProveedor = Factory::getInstance()->getDocumentoProveedor($idDocumentoProveedor);

	if ($documentoProveedor->anulado())
		throw new FactoryExceptionCustomException('El documento está anulado');

	if ($documentoProveedor->facturaGastos == 'N')
		throw new FactoryExceptionCustomException('El documento seleccionado no existe.');

	$documentoProveedor->proveedor->id;
	$documentoProveedor->detalle;
	$documentoProveedor->impuestos;
	$documentoProveedor->documentoGastoDatos->direccion->pais;
	$documentoProveedor->documentoGastoDatos->direccion->provincia;
	$documentoProveedor->documentoGastoDatos->direccion->localidad;
	$documentoProveedor->documentoGastoDatos->condicionIva;
	$documentoProveedor->documentoGastoDatos->imputacion;

	Html::jsonEncode('', $documentoProveedor->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>