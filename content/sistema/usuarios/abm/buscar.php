<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/abm/buscar/')) { ?>
<?php
$idUsuario = Funciones::get('idUsuario');
try {
	$usuario = Factory::getInstance()->getUsuario($idUsuario);
	$echoArr = $usuario->expand();
	Html::jsonEncode('', $echoArr);
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>