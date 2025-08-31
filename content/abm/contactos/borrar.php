<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/contactos/borrar/')) { ?>
<?php
$idContacto = Funciones::post('idContacto');
try {
	$contacto = Factory::getInstance()->getContacto($idContacto);
	Factory::getInstance()->marcarParaBorrar($contacto);
	Factory::getInstance()->persistir($contacto);
	Html::jsonSuccess('El contacto fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El contacto que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el contacto');
}
?>
<?php } ?>