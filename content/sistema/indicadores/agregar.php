<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/indicadores/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');
$view = Funciones::post('view');
$valor1 = Funciones::post('valor1');
$valor2 = Funciones::post('valor2');
$valor3 = Funciones::post('valor3');
$fields = Funciones::post('fields');
$where = Funciones::post('where');
$query = Funciones::post('query');
$roles = Funciones::post('roles');

try {
	$indicador = Factory::getInstance()->getIndicador();
	$indicador->nombre = $nombre;
	$indicador->descripcion = $descripcion;
	$indicador->view = $view;
	$indicador->valor1 = $valor1;
	$indicador->valor2 = $valor2;
	$indicador->valor3 = $valor3;
	if (count(explode(',', str_replace(' ', '', $fields))) > Indicador::maxFields) {
		throw new FactoryExceptionCustomException('No se pueden ingresar más de ' . Indicador::maxFields . ' campos por indicador');
	}
	$indicador->fields = $fields;
	$indicador->where = $where;
	$indicador->query = $query;
	foreach ($roles as $r){
		try {
			$rolExistente = Factory::getInstance()->getRol(Funciones::toInt($r)); //Con esto verifico que el rol exista.
			$indicador->addRol($rolExistente);
		} catch (Exception $eeex) {
			continue;
		}
	}
	$indicador->guardar()->notificar('sistema/indicadores/agregar/');
	Html::jsonSuccess('El indicador fue guardado correctamente');
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el indicador');
}

?>
<?php } ?>