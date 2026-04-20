<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/areas_empresa/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$habilitadaTicket = Funciones::post('habilitadaTicket');
$usuariosPorAreaEmpresa = Funciones::post('usuarios');

try {
	if (!isset($id)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$areaEmpresa = Factory::getInstance()->getAreaEmpresa($id);
	if ($areaEmpresa->anulado()) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$areaEmpresa->nombre = $nombre;
	$areaEmpresa->habilitadaTicket = $habilitadaTicket;

	if ($areaEmpresa->habilitadaTicket == 'S') {
		$aux = array();
		foreach ($usuariosPorAreaEmpresa as $u) {
			try {
				$usuarioExistente = Factory::getInstance()->getUsuario(Funciones::toInt($u)); //Con esto verifico que el usuario exista
				$uxae = Factory::getInstance()->getUsuarioPorAreaEmpresa();
				$uxae->id = $u;
				$uxae->idAreaEmpresa = $id;
				$aux[] = $uxae;
			} catch (Exception $ex) {
				continue;
			}
		}
		$areaEmpresa->usuarios = $aux;
	}

	$areaEmpresa->guardar()->notificar('abm/areas_empresa/editar/');
	Html::jsonSuccess('El área empresa fue guardado correctamente');
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El área empresa que intentó editar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar guardar el área empresa');
}

?>
<?php } ?>