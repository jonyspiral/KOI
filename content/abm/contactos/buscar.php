<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/contactos/buscar/')) { ?>
<?php
$idContacto = Funciones::get('idContacto');
try {
	$contacto = Factory::getInstance()->getContacto($idContacto);
	Html::jsonEncode('', $contacto->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El contacto "' . $idContacto . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>