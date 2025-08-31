<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/por_seccion/buscar/')) { ?>
<?php

$idSeccionProduccion = Funciones::get('idSeccionProduccion');

try {
	if (empty($idSeccionProduccion)) {
		throw new FactoryExceptionCustomException('Debe seleccionar una sección');
	}
	$uxas = Factory::getInstance()->getListObject('UsuarioPorSeccionProduccion', 'cod_seccion = ' . Datos::objectToDB($idSeccionProduccion) . ' ORDER BY cod_usuario ASC');

	foreach($uxas as $item) {
		/** @var UsuarioPorSeccionProduccion $item */
		$item->expand();
	}
	Html::jsonEncode('', $uxas);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>