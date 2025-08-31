<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/paises/buscar/')) { ?>
<?php
$idPais = Funciones::get('idPais');
try {
	$pais = Factory::getInstance()->getPais($idPais);
	Html::jsonEncode('', $pais->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El país "' . $idPais . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>