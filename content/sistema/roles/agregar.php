<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/roles/agregar/')) { ?>
<?php
$nombre = Funciones::post('nombre');
$funcionalidadesPorRol = Funciones::post('funcionalidades');
$tipo = Funciones::post('tipo');
try {
	$rol = Factory::getInstance()->getRol();
	$roles = Factory::getInstance()->getListObject('Rol', 'nombre = ' . Datos::objectToDB($nombre));
	if (count($roles) != 0)
		throw new FactoryExceptionRegistroExistente();
	$rol->nombre = $nombre;
	if ($tipo != 'P' && $tipo != 'C')
		throw new FactoryException('Debe elegir un tipo de rol');
	$rol->tipo = $tipo;
	//$aux = array();
	foreach ($funcionalidadesPorRol as $fun){
		$funcionalidad = Factory::getInstance()->getFuncionalidadPorRol();
		$funcionalidad->idFuncionalidad = Funciones::toInt($fun);
		//$aux[] = $funcionalidad;
		$rol->addFuncionalidad($funcionalidad);
	}
	//$rol->funcionalidades = $aux;
	Factory::getInstance()->persistir($rol);
	Html::jsonSuccess('El rol fue guardado correctamente');
} catch (FactoryException $e) {
	Html::jsonError($e->getMessage());
} catch (FactoryExceptionRegistroExistente $e) {
	Html::jsonError('Ya existe un rol con el nombre "' . $nombre . '". Por favor ingrese otro nombre');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el rol');
}
?>
<?php } ?>