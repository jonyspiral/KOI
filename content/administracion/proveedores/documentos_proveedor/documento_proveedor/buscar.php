<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/documentos_proveedor/documento_proveedor/buscar/')) { ?>
<?php

$idDocumentoProveedor = Funciones::get('idDocumentoProveedor');

try {
	$documentoProveedor = Factory::getInstance()->getDocumentoProveedor($idDocumentoProveedor);

	if ($documentoProveedor->anulado())
		throw new FactoryExceptionCustomException('El documento está anulado');

	if ($documentoProveedor->esFacturaGastos())
		throw new FactoryExceptionCustomException('El documento seleccionado no existe.');

	$documentoProveedor->proveedor->id;
	$documentoProveedor->tipo->id;
	$documentoProveedor->detalle;
	$documentoProveedor->impuestos;

	$array = $documentoProveedor->expand();

	Html::jsonEncode('', $array);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>