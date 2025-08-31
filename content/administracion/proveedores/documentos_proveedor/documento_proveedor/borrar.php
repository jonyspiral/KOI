<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/documentos_proveedor/documento_proveedor/borrar/')) { ?>
<?php

$idDocumentoProveedor = Funciones::post('idDocumentoProveedor');

try {
	$documentoProveedor = Factory::getInstance()->getDocumentoProveedor($idDocumentoProveedor);
	if($documentoProveedor->importePendiente != $documentoProveedor->importeTotal) {
		throw new FactoryExceptionCustomException('No puede borrar un documento que ya fue aplicado.');
	}

	Factory::getInstance()->beginTransaction();
	$documentoProveedor->borrar();
	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El documento fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El documento que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el documento');
}
?>
<?php } ?>