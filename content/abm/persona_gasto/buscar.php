<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/persona_gasto/buscar/')) { ?>
<?php

$idPersonaGasto = Funciones::get('idPersonaGasto');

try {
	$personaGasto = Factory::getInstance()->getPersonaGasto($idPersonaGasto);
	Html::jsonEncode('', $personaGasto);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La persona "' . $idPersonaGasto . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>