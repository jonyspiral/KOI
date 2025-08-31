<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/roles/buscar/')) { ?>
<?php
$idRol = Funciones::get('idRol');
try {
	$rol = Factory::getInstance()->getRol($idRol);
	if ($rol->anulado == 'S') {
		throw new FactoryException('El rol está anulado');
	}
	$funcionalidadesPorRol = Factory::getInstance()->getListObject('FuncionalidadPorRol', 'cod_rol = ' . Datos::objectToDB($rol->id));
	$echoArr = array();
	$echoArr['nombre'] = $rol->nombre;
	$echoArr['tipo'] = $rol->tipo;
	$echoArr['funcs'] = array();
	foreach ($funcionalidadesPorRol as $fun){
		$echoArr['funcs'][] = $fun->idFuncionalidad;
	}
	Html::jsonEncode('', $echoArr);
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>