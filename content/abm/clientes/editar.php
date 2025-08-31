<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/editar/')) { ?>
<?php

$cli = Funciones::post('cliente');

try {
	Factory::getInstance()->beginTransaction();

	$cliente = Factory::getInstance()->getClienteTodos($cli['idCliente']);
	$cliente->sucursalFiscal = Factory::getInstance()->getSucursal($cliente->id, $cli['sucursalFiscal']);
	$cliente->sucursalCentral = Factory::getInstance()->getSucursal($cliente->id, $cli['sucursalCentral']);
	$cliente->sucursalCobranza = Factory::getInstance()->getSucursal($cliente->id, $cli['sucursalCobranza']);

	if($cli['entregarSucEntrega'] == 'S'){
		$cliente->sucursalEntrega = Factory::getInstance()->getSucursal($cliente->id, $cli['sucursalEntrega']);
	} else {
		$cliente->sucursalEntrega = Factory::getInstance()->getSucursal();
	}

	$cliente->rubro = Factory::getInstance()->getRubro($cli['idRubro']);
	if (!is_null($cliente->sucursalFiscal->id)) {
		$cliente->direccionCalle = $cliente->sucursalFiscal->direccionCalle;
		$cliente->direccionNumero = $cliente->sucursalFiscal->direccionNumero;
		$cliente->direccionPiso = $cliente->sucursalFiscal->direccionPiso;
		$cliente->direccionDepartamento = $cliente->sucursalFiscal->direccionDepartamento;
		$cliente->direccionCodigoPostal = $cliente->sucursalFiscal->direccionCodigoPostal;
		$cliente->direccionPais = $cliente->sucursalFiscal->direccionPais;
		$cliente->direccionProvincia = $cliente->sucursalFiscal->direccionProvincia;
		$cliente->direccionLocalidad = $cliente->sucursalFiscal->direccionLocalidad;
	}
	$cliente->telefono1 = $cli['telefono1'];
	$cliente->interno1 = $cli['interno1'];
	$cliente->email = $cli['email'];
	$cliente->dni = $cli['dni'];
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
		$cliente->nombre = $cli['nombre'];
		$cliente->razonSocial = $cli['razonSocial'];
		$cliente->condicionIva = Factory::getInstance()->getCondicionIva($cli['condicionIva']);
		$cliente->creditoPrimeraEntrega = $cli['primeraEntega'];
		$cliente->grupoEmpresa = Factory::getInstance()->getGrupoEmpresa($cli['idGrupoEmpresa']);
		$cliente->listaAplicable = $cli['listaAplicable'];
		$cliente->vendedor = Factory::getInstance()->getVendedor($cli['idVendedor']);
	}
	if (Usuario::logueado()->esVendedor())
		$cliente->vendedor = Factory::getInstance()->getVendedor(Usuario::logueado()->getCodigoPersonal());
	$cliente->guardar()->notificar('abm/clientes/editar/');

	foreach ($cli['sucursales'] as $suc){
		if ($suc['nombre'] != 'NUEVA...') {
			$sucursal = ($suc['esNueva'] == 'false') ? Factory::getInstance()->getSucursal($cliente->id, $suc['id']) : Factory::getInstance()->getSucursal();
			if ($suc['borrar'] == 'true') {
				$sucursal->borrar()->notificar('abm/clientes/editar/');
			} else {
				if ($suc['esPuntoDeVenta'] == 'S' && (!isset($suc['latitud']) || !isset($suc['longitud']))) {
					throw new FactoryExceptionCustomException('Si la sucursal es punto de venta deberá completar los campos "latitud" y "longitud" para el Store Locator');
				}

				$sucursal->nombre = $suc['nombre'];
				$sucursal->cliente = $cliente;
				$sucursal->direccionCalle = $suc['calle'];
				$sucursal->direccionNumero = $suc['numero'];
				$sucursal->direccionPiso = $suc['piso'];
				$sucursal->direccionDepartamento = $suc['dpto'];
				$sucursal->direccionPais = Factory::getInstance()->getPais($suc['pais']);
				$sucursal->direccionProvincia = Factory::getInstance()->getProvincia($sucursal->direccionPais->id, $suc['provincia']);
				$sucursal->direccionLocalidad = Factory::getInstance()->getLocalidad($sucursal->direccionPais->id, $sucursal->direccionProvincia->id, $suc['localidad']);
				$sucursal->direccionCodigoPostal = $suc['codPostal'];
				if ($suc['esNueva'] == 'false')
					try {$sucursal->esCasaCentral = ($cliente->sucursalFiscal->id == $sucursal->id ? 'S' : 'N');} catch (Exception $ex) {}
				else
					$sucursal->esCasaCentral = 'N';
				$sucursal->telefono1 = $suc['telefono1'];
				$sucursal->telefono2 = $suc['telefono2'];
				$sucursal->celular = $suc['celular'];
				$sucursal->fax = $suc['fax'];
				$sucursal->email = $suc['email'];
				$sucursal->horarioAtencion = $suc['horarioDeAtencion'];
				$sucursal->esPuntoDeVenta = $suc['esPuntoDeVenta'];
				$sucursal->direccionLatitud = $suc['latitud'];
				$sucursal->direccionLongitud = $suc['longitud'];
				$sucursal->sucursalEntrega = Factory::getInstance()->getSucursal($cliente->id, $suc['sucursalEntrega']);
				$sucursal->reparto = $suc['reparto'];
				$sucursal->transporte = Factory::getInstance()->getTransporte($suc['transporte']);
				$sucursal->vendedor = Factory::getInstance()->getVendedor($suc['vendedor']);
				$sucursal->observaciones = $suc['observaciones'];

				$sucursal->guardar()->notificar('abm/clientes/editar/');
			}
		}
	}

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El cliente fue editado correctamente');
} catch (FactoryExceptionCustomException $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar guardar el cliente');
}
?>
<?php } ?>