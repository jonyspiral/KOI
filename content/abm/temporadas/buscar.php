<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/temporadas/buscar/')) { ?>
<?php

$idTemporada = Funciones::get('idTemporada');

try {
	$temporada = Factory::getInstance()->getTemporada($idTemporada);
	Html::jsonEncode('', $temporada->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La temporada "' . $idTemporada . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>