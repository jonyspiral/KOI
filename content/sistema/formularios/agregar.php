<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/agregar/')) { ?>
<?php

$cli = Funciones::post('cliente');

try {
	if (is_null($cli['cuit']))
		throw new FactoryExceptionRegistroExistente('Debe ingresar un cuit válido');
	if (count(Factory::getInstance()->getListObject('Cliente', 'cuit = ' . Datos::objectToDB($cli['cuit']))) != 0)
		throw new FactoryExceptionRegistroExistente('Ya existe un cliente con ese cuit');
	try {
		$cliente = Factory::getInstance()->getCliente();
		$cliente->nombre = $cli['nombre'];
		$cliente->razonSocial = $cli['razonSocial'];
		$cliente->cuit = $cli['cuit'];
		$cliente->condicionIva = Factory::getInstance()->getCondicionIva($cli['condicionIva']);
		$cliente->rubro = Factory::getInstance()->getRubro($cli['idRubro']);
		$cliente->direccionCalle = $cli['calle'];
		$cliente->direccionNumero = $cli['numero'];
		$cliente->direccionPiso = $cli['piso'];
		$cliente->direccionDepartamento = $cli['dpto'];
		$cliente->direccionCodigoPostal = $cli['codPostal'];
		$cliente->direccionPais = Factory::getInstance()->getPais($cli['idPais']);
		$cliente->direccionProvincia = Factory::getInstance()->getProvincia($cliente->direccionPais->id, $cli['idProvincia']);
		$cliente->direccionLocalidad = Factory::getInstance()->getLocalidad($cliente->direccionPais->id, $cliente->direccionProvincia->id, $cli['idLocalidad']);
		$cliente->telefono1 = $cli['telefono1'];
		$cliente->interno1 = $cli['interno1'];
		$cliente->email = $cli['email'];
		$cliente->observaciones = $cli['observaciones'];
		$cliente->marcasQueComercializa = $cli['marcasQueComercializa'];
		$cliente->referenciasBancarias = $cli['referenciasBancarias'];
		$cliente->referenciasComerciales = $cli['referenciasComerciales'];
		if (!Usuario::logueado()->esVendedor()) {
			if (Usuario::logueado()->tieneRol('creditos')) {
				$cliente->creditoPlazoMaximo = $cli['plazoMaximo'];
				$cliente->creditoFormaDePago = Factory::getInstance()->getFormaDePago($cli['formaDePago']);
				$cliente->calificacion = $cli['calificacion'];
				$cliente->creditoLimite = $cli['limiteDeCredito'];
				$cliente->creditoDescuentoEspecial = $cli['descuentoEspecial'];
				$cliente->observacionesCobranza = $cli['observacionesCobranza'];
			}
			$cliente->creditoPrimeraEntrega = $cli['primeraEntega'];
			$cliente->grupoEmpresa = Factory::getInstance()->getGrupoEmpresa($cli['idGrupoEmpresa']);
			$cliente->listaAplicable = $cli['listaAplicable'];
			$cliente->vendedor = Factory::getInstance()->getVendedor($cli['idVendedor']);
		}
		if (Usuario::logueado()->esVendedor())
			$cliente->vendedor = Factory::getInstance()->getVendedor(Usuario::logueado()->getCodigoPersonal());
		Factory::getInstance()->persistir($cliente);
	} catch (Exception $ex) {
		throw new FactoryExceptionRegistroExistente('Ocurrió un error: no se pudo dar de alta el cliente');
	}

	//El cliente se guardó correctamente => creo una sucursal
	$clientes = Factory::getInstance()->getListObject('Cliente', 'cuit = ' . Datos::objectToDB($cliente->cuit));
	$cliente = $clientes[0];
	
	try {
		//Creo la primera sucursal, CASA CENTRAL
		$casaCentral = Factory::getInstance()->getSucursal();
		$casaCentral->cliente = $cliente;
		$casaCentral->nombre = 'CASA CENTRAL';
		$casaCentral->direccionCalle = $cliente->direccionCalle;
		$casaCentral->direccionNumero = $cliente->direccionNumero;
		$casaCentral->direccionPiso = $cliente->direccionPiso;
		$casaCentral->direccionDepartamento = $cliente->direccionDepartamento;
		$casaCentral->direccionPais = $cliente->direccionPais;
		$casaCentral->direccionProvincia = $cliente->direccionProvincia;
		$casaCentral->direccionLocalidad = $cliente->direccionLocalidad;
		$casaCentral->direccionCodigoPostal = $cliente->direccionCodigoPostal;
		$casaCentral->esCasaCentral = 'S';
		$casaCentral->telefono1 = $cliente->telefono1;
		$casaCentral->email = $cliente->email;
		$casaCentral->vendedor = $cliente->vendedor;
		$casaCentral->observaciones = $cliente->observaciones;
		Factory::getInstance()->persistir($casaCentral);
	} catch (Exception $ex) {
		throw new FactoryException('El cliente se creó correctamente, pero ocurrió un error al intentar crear el domicilio fiscal');
	}

	try {
		//La sucursal se guardó correctamente => creo un contacto
		$contacto = Factory::getInstance()->getContacto();
		$contacto->apellido = $cli['apellidoContacto'];
		$contacto->nombre = $cli['nombreContacto'];
		$contacto->direccionCalle = $cliente->direccionCalle;
		$contacto->direccionNumero = $cliente->direccionNumero;
		$contacto->direccionPiso = $cliente->direccionPiso;
		$contacto->direccionDepartamento = $cliente->direccionDepartamento;
		$contacto->direccionPais = $cliente->direccionPais;
		$contacto->direccionProvincia = $cliente->direccionProvincia;
		$contacto->direccionLocalidad = $cliente->direccionLocalidad;
		$contacto->direccionCodigoPostal = $cliente->direccionCodigoPostal;
		$contacto->email1 = $cliente->email;
		$contacto->areaEmpresa = Factory::getInstance()->getAreaEmpresa(AreasEmpresa::casaCentral);
		$contacto->cliente = $cliente;
		try {$contacto->sucursal = $cliente->sucursales[0];} catch (Exception $ex) {}
		$contacto->telefono1 = $cliente->telefono1;
		$contacto->tipo = TiposContacto::cliente;
		$newContacto = Factory::getInstance()->persistir($contacto);
	} catch (Exception $ex) {
		throw new FactoryException('El cliente y el domicilio fiscal se crearon correctamente, pero ocurrió un error al intentar crear el contacto del cliente');
	}

	try {
		//El contacto se guardó correctamente => creo el usuario
		$usuario = Factory::getInstance()->getUsuario();
		$usuario->id = $cliente->cuit;
		$usuario->password = Funciones::toSHA1($usuario->id);
		$usuario->contacto = $newContacto;
		$usuario->tipoPersona = TiposUsuario::contacto;
		//Creo un nuevo Rol y se lo asigno
		$rol = Factory::getInstance()->getListObject('Rol', 'nombre = \'cliente\' AND tipo = \'C\'');
		$rol = $rol[0];
		$nuevoRol = Factory::getInstance()->getRolPorUsuario();
		$nuevoRol->id = $rol->id;
		$nuevoRol->idUsuario = $usuario->id; //Uso idUsuario porque en el INSERT de roles se usa idUsuario
		$arrRoles = array($nuevoRol);
		$usuario->roles = $arrRoles;
		Factory::getInstance()->persistir($usuario);
	} catch (Exception $ex) {
		throw new FactoryException('El cliente, el domicilio fiscal y el contacto se crearon correctamente, pero ocurrió un error al intentar crear el usuario del cliente');
	}

	Html::jsonSuccess('El cliente fue guardado correctamente');
} catch (FactoryExceptionRegistroExistente $ex){
	Html::jsonError($ex->getMessage());
} catch (FactoryException $ex){
	Html::jsonAlert($ex->getMessage());
} catch (Exception $ex){
	Html::jsonNull();
}
?>
<?php } ?>