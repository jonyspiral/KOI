<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/por_almacen/buscar/')) { ?>
<?php

$idAlmacen = Funciones::get('idAlmacen');

try {
	if (empty($idAlmacen)) {
		throw new FactoryExceptionCustomException('Debe seleccionar un almacén');
	}
	$uxas = Factory::getInstance()->getListObject('UsuarioPorAlmacen', 'cod_almacen = ' . Datos::objectToDB($idAlmacen) . ' ORDER BY cod_usuario ASC');

	foreach($uxas as $item) {
		/** @var UsuarioPorAlmacen $item */
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