<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/aplicacion/borrar/')) { ?>
<?php

//Esto corresponde al DESAPLICAR

$id = Funciones::post('id');

try {
	if (!preg_match('/^[0-9]+$/', (string) $id)) {
		throw new FactoryExceptionRegistroNoExistente();
	}

	$hija = Factory::getInstance()->getDocumentoHija($id);
	$hija->desaplicar();
	Html::jsonEncode('', array('hija' => $hija));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError('No existe la aplicacion. Por favor actualice la lista');
} catch (FactoryExceptionRegistroNoExistente $ex){
	Html::jsonError('No existe la aplicacion. Por favor actualice la lista');
} catch (Exception $ex){
	Html::jsonError('Ocurrio un error al intentar desaplicar los documentos');
}

?>
<?php } ?>