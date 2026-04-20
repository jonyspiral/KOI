<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/abm/agregar/')) { ?>
<?php

function validaUsuario($idUsuario){
	if (strlen($idUsuario) > 40)
		throw new FactoryException('El nombre de usuario no puede superar los 40 caracteres');
	if (count(explode(' ', $idUsuario)) > 1)
		throw new FactoryException('El nombre de usuario no puede contener espacios');
}

$idUsuario = Funciones::post('idUsuario');
$password = Funciones::toSHA1(Funciones::post('password'));
$tipo = Funciones::post('tipo');
$rolesPorUsuario = Funciones::post('roles');

try {
	if ($tipo == 'P')
		$idPersonal = Funciones::post('idPersonal');
	elseif ($tipo == 'C')
		$idContacto = Funciones::post('idContacto');
	else
		throw new FactoryException('Por favor seleccione un tipo de usuario');
	validaUsuario($idUsuario);
	$usuario = Factory::getInstance()->getUsuarioLogin();
	$usuario->id = $idUsuario;
	$usuario->password = $password;
	if (isset($tipo)) {
		//$usuario->tipoUsuario = $tipo;
		if ($tipo == 'P') {
			$usuario->tipoPersona = TiposPersonal::personal;
			$usuario->idPersonal = $idPersonal;
			$listaOperadores = Factory::getInstance()->getListObject('Operador', 'cod_personal = ' . Datos::objectToDB($usuario->idPersonal) . ' AND tipo_operador = \'' . TiposPersonal::vendedor . '\'');
			if (count($listaOperadores) == 1)
				$usuario->tipoPersona = TiposPersonal::vendedor;
			else {
				$listaOperadoresVendedor = Factory::getInstance()->getListObject('Operador', 'cod_personal = ' . Datos::objectToDB($usuario->idPersonal) . ' AND tipo_operador = \'' . TiposPersonal::personal . '\'');
				if (count($listaOperadoresVendedor) == 1)
					$usuario->tipoPersona = TiposPersonal::operador;
			}
		}
		if ($tipo == 'C') {
			$usuario->idContacto = $idContacto;
			$usuario->tipoPersona = $usuario->contacto->tipo;
		}
	}
	$aux = array();
	foreach ($rolesPorUsuario as $r){
		try {
			$rolExistente = Factory::getInstance()->getRol(Funciones::toInt($r)); //Con esto verifico que el rol exista.
			if (($usuario->tipoUsuario == 'P' && $rolExistente->tipo == 'C') || ($usuario->tipoUsuario == 'C' && $rolExistente->tipo == 'P'))
				throw new FactoryException();
			$rol = Factory::getInstance()->getRolPorUsuario();
			$rol->id = Funciones::toInt($r);
			$rol->idUsuario = $idUsuario;
			$aux[] = $rol;
		} catch (Exception $eeex) {
			continue;
		}
	}
	$usuario->roles = $aux;
	Factory::getInstance()->persistir($usuario);
	Html::jsonSuccess('El usuario fue guardado correctamente');
} catch (FactoryException $e) {
	Html::jsonError($e->getMessage());
} catch (FactoryExceptionRegistroExistente $e) {
	Html::jsonError('Ya existe el usuario "' . $idUsuario . '". Por favor ingrese otro nombre');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el usuario');
}
?>
<?php } ?>