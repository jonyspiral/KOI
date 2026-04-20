<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/aplicacion/borrar/')) { ?>
<?php

//Esto corresponde al DESAPLICAR

$id = Funciones::post('id');

try {
	$hija = Factory::getInstance()->getDocumentoHija($id);
	$hija->desaplicar();
	Html::jsonEncode('', array('hija' => $hija));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError('No existe la aplicación. Por favor actualice la lista');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar desaplicar los documentos');
}

?>
<?php } ?>