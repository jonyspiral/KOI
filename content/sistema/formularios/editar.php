<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/editar/')) { ?>
<?php

$cli = Funciones::post('cliente');

try {
	$cliente = Factory::getInstance()->getClienteTodos($cli['idCliente']);
	$cliente->sucursalFiscal = Factory::getInstance()->getSucursal($cliente->id, $cli['sucursalFiscal']);
	$cliente->sucursalCentral = Factory::getInstance()->getSucursal($cliente->id, $cli['sucursalCentral']);
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
	Factory::getInstance()->persistir($cliente);

	foreach ($cli['sucursales'] as $suc){
		if ($suc['nombre'] != 'NUEVA...') {
			$sucursal = Factory::getInstance()->getSucursal();
			if ($suc['esNueva'] == 'false') {
				$sucursal = Factory::getInstance()->getSucursal($cliente->id, $suc['id']);
				if ($suc['borrar'] == 'true')
					$sucursal = Factory::getInstance()->marcarParaBorrar($sucursal);
			}
			if ($suc['borrar'] == 'false') {
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
				$sucursal->sucursalEntrega = Factory::getInstance()->getSucursal($cliente->id, $suc['sucursalEntrega']);
				$sucursal->reparto = $suc['reparto'];
				$sucursal->transporte = Factory::getInstance()->getTransporte($suc['transporte']);
				$sucursal->vendedor = Factory::getInstance()->getVendedor($suc['vendedor']);
				$sucursal->observaciones = $suc['observaciones'];
			}
			Factory::getInstance()->persistir($sucursal);
		}
	}

	Html::jsonSuccess('El cliente fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el cliente');
}
?>
<?php } ?>