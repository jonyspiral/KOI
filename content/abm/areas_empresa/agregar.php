<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/areas_empresa/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$habilitadaTicket = Funciones::post('habilitadaTicket');
$usuariosPorAreaEmpresa = Funciones::post('usuarios');

try {
	$areaEmpresa = Factory::getInstance()->getAreaEmpresa();
	$areaEmpresa->nombre = $nombre;
	$areaEmpresa->habilitadaTicket = $habilitadaTicket;

	if ($areaEmpresa->habilitadaTicket == 'S') {
		$aux = array();
		foreach ($usuariosPorAreaEmpresa as $u) {
			try {
				$usuarioExistente = Factory::getInstance()->getUsuario(Funciones::toInt($u)); //Con esto verifico que el usuario exista
				$uxae = Factory::getInstance()->getUsuarioPorAreaEmpresa();
				$uxae->id = $u;
				$aux[] = $uxae;
			} catch (Exception $ex) {
				continue;
			}
		}
		$areaEmpresa->usuarios = $aux;
	}

	$areaEmpresa->guardar()->notificar('abm/areas_empresa/agregar/');
	Html::jsonSuccess('El área empresa fue guardado correctamente');
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar guardar el área empresa');
}

?>
<?php } ?>