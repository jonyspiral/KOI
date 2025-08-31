<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/aplicacion/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$debeId = Funciones::post('debeId');
$haberId = Funciones::post('haberId');
$tipoHaber = Funciones::post('tipoHaber');

try {
	$debe = Factory::getInstance()->getDocumentoProveedorAplicacionDebe($empresa, $debeId);
	$haber = Factory::getInstance()->getDocumentoProveedorAplicacionHaber($empresa, $haberId, $tipoHaber);

	$debe->aplicar($haber);

	$return = array(
		'debe'	=> $debe,
		'haber'	=> $haber
	);

	Html::jsonEncode('', $return);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError('Alguno de los documentos no existe. Por favor actualice la lista');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar aplicar los documentos. ' . $ex->getMessage());
}

?>
<?php } ?>