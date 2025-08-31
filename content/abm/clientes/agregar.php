<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/agregar/')) { ?>
<?php

$cli = Funciones::post('cliente');

try {
	if (is_null($cli['cuit']))
		throw new FactoryExceptionRegistroExistente('Debe ingresar un cuit válido');
	if (count(Factory::getInstance()->getListObject('Cliente', 'cuit = ' . Datos::objectToDB($cli['cuit']))) != 0)
		throw new FactoryExceptionRegistroExistente('Ya existe un cliente con ese cuit');

	try {
		Factory::getInstance()->beginTransaction();

		$cliente = Factory::getInstance()->getCliente();
		$cliente->habilitadoCae = 'S';
		$cliente->nombre = $cli['nombre'];
		$cliente->razonSocial = $cli['razonSocial'];
		$cliente->cuit = $cli['cuit'];
		$cliente->dni = $cli['dni'];
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
		$cliente->guardar()->notificar('abm/clientes/agregar/');


		//El cliente se guardó correctamente => creo una sucursal

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
		$casaCentral->transporte = Factory::getInstance()->getTransporte($cli['transporte']);
		$casaCentral->vendedor = $cliente->vendedor;
		$casaCentral->observaciones = $cliente->observaciones;
		$casaCentral->guardar();

		Factory::getInstance()->commitTransaction();
	} catch (Exception $ex) {
		throw new FactoryExceptionRegistroExistente('Ocurrió un error: no se pudo dar de alta el cliente');
	}

	//Mando un mail para avisar que se creó un cliente nuevo!
	try {
		$cuerpo = 'Se ha ingresado un nuevo cliente: "' . $cliente->nombre . '". Razón social:  "' . $cliente->razonSocial . '". ';
		$cuerpo .= 'Para que pueda empezar a operar deberá ser autorizado.';
		Email::enviar(
			 array(
				 'para' => array('gc@spiralshoes.com', 'gg@spiralshoes.com'),
				 'asunto' => 'Nuevo cliente en Spiral',
				 'contenido' => $cuerpo
			 )
		);
	} catch (Exception $ex) {
		//Como es un simple error al enviar el mail, no hago nada
	}

	try {
		//La sucursal se guardó correctamente => creo un contacto
		Factory::getInstance()->beginTransaction();

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
		$contacto->guardar();

		Factory::getInstance()->commitTransaction();
	} catch (Exception $ex) {
		throw new FactoryException('El cliente y el domicilio fiscal se crearon correctamente, pero ocurrió un error al intentar crear el contacto del cliente');
	}
/*
	try {
		//El contacto se guardó correctamente => creo el usuario
		$usuario = Factory::getInstance()->getUsuarioLogin();
		$arrMail = explode('@', $cliente->email);
		$usuario->id = $arrMail[0];
		$usuario->password = Funciones::toSHA1($usuario->id);
		$usuario->contacto = $contacto;
		$usuario->tipoPersona = TiposUsuario::contacto;
		//Creo un nuevo Rol y se lo asigno
		$rol = Factory::getInstance()->getListObject('Rol', 'nombre = \'cliente\' AND tipo = \'C\'');
		$rol = $rol[0];
		$nuevoRol = Factory::getInstance()->getRolPorUsuario();
		$nuevoRol->id = $rol->id;
		$nuevoRol->idUsuario = $usuario->id; //Uso idUsuario porque en el INSERT de roles se usa idUsuario
		$arrRoles = array($nuevoRol);
		$usuario->roles = $arrRoles;
		$usuario->guardar();
	} catch (Exception $ex) {
		Factory::getInstance()->commitTransaction();
		throw new FactoryException('El cliente, el domicilio fiscal y el contacto se crearon correctamente, pero ocurrió un error al intentar crear el usuario del cliente');
	}
*/

	Html::jsonSuccess('El cliente fue guardado correctamente');
} catch (FactoryExceptionRegistroExistente $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (FactoryException $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonAlert($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonNull();
}

?>
<?php } ?>