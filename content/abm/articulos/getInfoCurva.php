<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/articulos/buscar/')) { ?>
<?php

$idCurva = Funciones::get('idCurva');

try {
	if (!$idCurva) {
		throw new FactoryExceptionCustomException('Debe indicarse la curva');
	}
	$curva = Factory::getInstance()->getCurva($idCurva);

	Html::jsonEncode('', $curva);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>