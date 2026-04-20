<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/paises/borrar/')) { ?>
<?php
$idPais = Funciones::post('idPais');
try {
	$pais = Factory::getInstance()->getPais($idPais);
	Factory::getInstance()->marcarParaBorrar($pais);
	Factory::getInstance()->persistir($pais);
	Html::jsonSuccess('El país fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El país que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el país');
}
?>
<?php } ?>