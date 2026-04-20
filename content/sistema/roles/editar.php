<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/roles/editar/')) { ?>
<?php
$idRol = Funciones::post('idRol');
$nombre = Funciones::post('nombre');
$funcionalidadesPorRol = Funciones::post('funcionalidades');
$tipo = Funciones::post('tipo');
try {
	if (!isset($idRol))
		throw new FactoryExceptionRegistroNoExistente();
	$rol = Factory::getInstance()->getRol($idRol);
	$roles = Factory::getInstance()->getListObject('Rol', 'cod_rol <> ' . Datos::objectToDB($idRol) . ' AND nombre = ' . Datos::objectToDB($nombre));
	if (count($roles) != 0)
		throw new FactoryExceptionRegistroExistente();
	$rol->nombre = $nombre;
	if ($tipo != 'P' && $tipo != 'C')
		throw new FactoryException('Debe elegir un tipo de rol');
	$aux = array();
	foreach ($funcionalidadesPorRol as $fun){
		$funcionalidad = Factory::getInstance()->getFuncionalidadPorRol();
		$funcionalidad->idRol = $rol->id;
		$funcionalidad->idFuncionalidad = Funciones::toInt($fun);
		$aux[] = $funcionalidad;
	}
	$rol->funcionalidades = $aux;
	Factory::getInstance()->persistir($rol);
	Html::jsonSuccess('El rol fue guardado correctamente');
} catch (FactoryException $e) {
	Html::jsonError($e->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El rol que intentó editar no existe');
} catch (FactoryExceptionRegistroExistente $e) {
	Html::jsonError('Ya existe un rol con el nombre "' . $nombre . '". Por favor ingrese otro');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el rol');
}
?>
<?php } ?>