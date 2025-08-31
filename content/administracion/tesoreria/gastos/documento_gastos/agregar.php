<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/documento_gastos/agregar/')) { ?>
<?php

function agregarValidarDetalles($detalles) {
	global $detallesNormalizados;
	global $totalDocumento;
	global $totalNetoGravado;
	global $totalNetoNoGravado;
	foreach ($detalles as $detalle) {
		$precioUnitario = $detalle['precioUnitario'];
		$cantidad = $detalle['cantidad'];
		$imputacion = $detalle['idImputacion'];
		$descripcion = $detalle['descripcion'];
		$gravado = $detalle['gravado'];
		$item = Factory::getInstance()->getDocumentoProveedorItem();

		if (is_null($precioUnitario) || is_null($imputacion) || is_null($descripcion) || is_null($cantidad)) {
			throw new FactoryExceptionCustomException('Todos los campos de los detalles son obligatorios.');
		}

		$item->cantidad = $cantidad;
		$item->importe = $precioUnitario * $cantidad;
		$item->precioUnitario = $precioUnitario;

		if ($cantidad < 0 || $precioUnitario < 0) {
			throw new FactoryExceptionCustomException('Error en el formato de importes en efectivo.');
		}

		if ($gravado == 'S') {
			$totalNetoGravado += $item->cantidad * $item->precioUnitario;
		} else {
			$totalNetoNoGravado += $item->cantidad * $item->precioUnitario;
		}

		$item->documentoProveedor = $docuemnto;
		$item->gravado = $gravado;
		$item->imputacion = Factory::getInstance()->getImputacion($imputacion);
		$item->descripcion = $descripcion;

		$detallesNormalizados[] = $item;
		$totalDocumento += $item->importe;
	}
}

$empresa = Funciones::session('empresa');
$diferenciaAdmisibleDocumento = 0.05; //Centavos que puede diferir el importe total con respecto a la suma de impuestos, gravado y no gravado.
$diferenciaAdmisibleMaterial = 0.2; //Centavos que puede diferir el precio del material.
$tipoDocumento = Funciones::post('tipoDocumento');
$idFacturaCancelatoria = Funciones::post('facturaCancelatoria');
$idProveedor = Funciones::post('idProveedor');
$ptoVenta = Funciones::post('puntoDeVenta');
$numero = Funciones::post('numero');
$fechaDocumento = Funciones::post('fechaDocumento');
$fechaVencimiento = Funciones::post('fechaVencimiento');
$fechaPeriodoFiscal = Funciones::post('fechaPeriodoFiscal');
$netoGravado = Funciones::toFloat(Funciones::post('netoGravado'), 2);
$netoNoGravado = Funciones::toFloat(Funciones::post('netoNoGravado'), 2);
$importeTotal = Funciones::toFloat(Funciones::post('importeTotal'), 2);
$observaciones = Funciones::post('observaciones');
$documentoEnConflicto = Funciones::post('documentoEnConflicto');
$detallesComunes = Funciones::post('detallesComunes');
$impuestos = Funciones::post('impuestos');
$nroAutorizacion = Funciones::post('nroAutorizacion');
$esProveedor = (Funciones::post('esProveedor') == 'S' ? true : false);
$razonSocial = Funciones::post('razonSocial');
$cuit = Funciones::post('cuit');
$condicionIva = Funciones::post('condicionIva');
$idImputacion = Funciones::post('idImputacion');
$letra = Funciones::post('letra');
$calle = Funciones::post('calle');
$numeroCalle = Funciones::post('numeroCalle');
$piso = Funciones::post('piso');
$dpto = Funciones::post('dpto');
$pais = Funciones::post('pais');
$provincia = Funciones::post('provincia');
$localidad = Funciones::post('localidad');
$codPostal = Funciones::post('codPostal');
$detallesNormalizados = array();
$ImpuestosNormalizados = array();
$totalDocumento = 0;
$totalNetoGravado = 0;
$totalNetoNoGravado = 0;
$totalImpuestos = 0;
$jsonSuccess = array();

try {
	if (is_null($tipoDocumento) || is_null($fechaDocumento) || is_null($fechaVencimiento) || is_null($fechaPeriodoFiscal) ||
		is_null($netoGravado) || is_null($netoNoGravado) || is_null($importeTotal)
	) {
		throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó.');
	}

	if ($empresa == '1') {
		if (is_null($ptoVenta) || is_null($numero)) {
			throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó.');
		}
	}

	if ($esProveedor) {
		if (is_null($idProveedor)) {
			throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó.');
		}
		if($empresa == '1'){
			$where = 'punto_venta = ' . Datos::objectToDB($ptoVenta) . ' AND ';
			$where .= 'nro_documento = ' . Datos::objectToDB($numero) . ' AND ';
			$where .= 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ';
			$where .= 'empresa = ' . Datos::objectToDB('1') . ' AND ';
			$where .= 'anulado = ' . Datos::objectToDB('N');

			$lista = Factory::getInstance()->getListObject('DocumentoProveedor', $where);

			if(count($lista) > 0)
				throw new FactoryExceptionCustomException('El documento ingresado ya existe en el sistema');
		}
	} elseif ($empresa == '1') {
		if (is_null($razonSocial) || is_null($cuit) || is_null($condicionIva) || is_null($idImputacion)) {
			throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó.');
		}
	}

	if ($importeTotal < 0 || $netoGravado < 0 || $netoNoGravado < 0) {
		throw new FactoryExceptionCustomException('Error en el formato de importes en efectivo.');
	}

	if ($netoGravado == 0 && count($impuestos) > 0) {
		throw new FactoryExceptionCustomException('No puede agregar impuestos sobre un documento sin neto gravado.');
	}

	if (is_null($detallesComunes)) {
		throw new FactoryExceptionCustomException('El documento que intenta confeccionar no puede no tener detalles.');
	}

	if ($tipoDocumento == 'FAC') {
		$documento = Factory::getInstance()->getFacturaProveedor();
	} elseif ($tipoDocumento == 'NCR') {
		$documento = Factory::getInstance()->getNotaDeCreditoProveedor();
		$documento->facturaCancelatoria = Factory::getInstance()->getFacturaProveedor($idFacturaCancelatoria);
	} elseif ($tipoDocumento == 'NDB') {
		$documento = Factory::getInstance()->getNotaDeDebitoProveedor();
	} else {
		throw new FactoryExceptionCustomException('El tipo de documento especificado no existe.');
	}

	$documento->empresa = $empresa;
	$documento->puntoVenta = $ptoVenta;
	$documento->nroDocumento = $numero;
	$documento->fecha = $fechaDocumento;
	$documento->fechaVencimiento = $fechaVencimiento;
	$documento->fechaPeriodoFiscal = $fechaPeriodoFiscal;
	$documento->netoGravado = $netoGravado;
	$documento->netoNoGravado = $netoNoGravado;
	$documento->importeTotal = $importeTotal;
	$documento->observaciones = $observaciones;
	$documento->documentoEnConflicto = $documentoEnConflicto;
	$documento->facturaGastos = 'S';
	$documento->esProveedor = $esProveedor;

	if ($esProveedor) {
		$documento->proveedor = Factory::getInstance()->getProveedor($idProveedor);
		$documento->letra = $documento->proveedor->condicionIva->letraFactura;
		$documento->condicionPlazoPago = $documento->proveedor->plazoPago;
	} else {
		$documento->letra = $letra;

		$direccion = new Direccion();
		$direccion->calle = $calle;
		$direccion->numero = $numeroCalle;
		$direccion->piso = $piso;
		$direccion->departamento = $dpto;
		$direccion->pais = Factory::getInstance()->getPais($pais);
		$direccion->provincia = Factory::getInstance()->getProvincia($pais, $provincia);
		$direccion->localidad = Factory::getInstance()->getLocalidad($pais, $provincia, $localidad);
		$direccion->codigoPostal = $codPostal;

		$documentoGastosDatos = Factory::getInstance()->getDocumentoGastoDatos();
		$documentoGastosDatos->razonSocial = $razonSocial;
		$documentoGastosDatos->cuit = $cuit;
		$documentoGastosDatos->condicionIva = Factory::getInstance()->getCondicionIva($condicionIva);
		$documentoGastosDatos->imputacion = Factory::getInstance()->getImputacion($idImputacion);
		$documentoGastosDatos->direccion = $direccion;

		$documento->documentoGastoDatos = $documentoGastosDatos;
	}

	$arrayUnicidadImpuestos = array();
	foreach ($impuestos as $impuesto) {
		if ($arrayUnicidadImpuestos[$impuesto['idImpuesto']]) {
			throw new FactoryExceptionCustomException('No puede ingresar dos veces el mismo impuesto.');
		}

		if (is_null($impuesto['idImpuesto']) || is_null($impuesto['importe'])) {
			throw new FactoryExceptionCustomException('Todos los campos de los impuestos son obligatorios.');
		}

		$arrayUnicidadImpuestos[$impuesto['idImpuesto']] = $impuesto['idImpuesto'];
		$impuestoQuery = Factory::getInstance()->getImpuesto($impuesto['idImpuesto']);
		$impuestoPorDocProveedor = Factory::getInstance()->getImpuestoPorDocumentoProveedor();
		$impuestoPorDocProveedor->impuesto = $impuestoQuery;
		$impuestoPorDocProveedor->porcentaje = $impuestoQuery->porcentaje;
		$impuestoPorDocProveedor->importe = Funciones::toFloat($impuesto['importe'], 2);
		$ImpuestosNormalizados[] = $impuestoPorDocProveedor;

		$totalImpuestos += Funciones::toFloat($impuesto['importe'], 2);
	}

	agregarValidarDetalles($detallesComunes);

	if (Funciones::toFloat($totalDocumento, 2) != Funciones::toFloat($netoGravado + $netoNoGravado, 2)) {
		throw new FactoryExceptionCustomException('El importe total del documento no coincide con la suma de los importes de los detalles.');
	}

	$diferencia = $importeTotal - (Funciones::toFloat($totalNetoGravado + $totalNetoNoGravado + $totalImpuestos, 2));
	if (abs($diferencia) > $diferenciaAdmisibleDocumento) {
		throw new FactoryExceptionCustomException('El importe total ingresado difiere del importe calculado a partir de los parciales');
	}

	$documento->detalle = $detallesNormalizados;
	$documento->impuestos = $ImpuestosNormalizados;
	$documento->guardar();

	Html::jsonSuccess('El documento se agregó correctamente.', $jsonSuccess);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar agregar el documento.');
}

?>
<?php } ?>