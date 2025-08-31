<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/documentos_proveedor/documento_proveedor/editar/')) { ?>
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
		$idSerializado = null;
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

		if ($detalle['tipo'] == 'R') {
			$remitoPorOrdenDeCompra = Factory::getInstance()->getRemitoPorOrdenDeCompra($detalle['id']);

			if ($remitoPorOrdenDeCompra->ordenDeCompraItem->material->usaRango()) {
				$pendienteParaDescontar = 0;
				$importeTotal = 0;
				for ($i = 1; $i < 16; $i++) {
					$item->cantidades[$i] = Funciones::toInt($detalle['cantidades'][$i]);
					$item->precios[$i] = Funciones::toFloat($detalle['precios'][$i]);

					if ($item->cantidades[$i] <= 0) {
						$item->cantidades[$i] = 0;
						$item->precios[$i] = 0;
					}

					if ($item->cantidades[$i] > 0 && $item->precios[$i] <= 0) {
						throw new FactoryExceptionCustomException('Los precios no pueden ser menores o iguales a 0');
					}

					if ($item->cantidades[$i] > $remitoPorOrdenDeCompra->cantidadesPendientes[$i]) {
						throw new FactoryExceptionCustomException('La cantidad ingresada excede la cantidad que figura en remito');
					}
					$importeTotal += $item->cantidades[$i] * $item->precios[$i];
					$pendienteParaDescontar += $item->cantidades[$i];
					$remitoPorOrdenDeCompra->cantidadesPendientes[$i] -= $item->cantidades[$i];
				}
				$item->precioUnitario = null;
				$item->importe = $detalle['total'];
				$item->usaRango = 'S';
			} else {
				if ($item->cantidad > $remitoPorOrdenDeCompra->cantidadPendiente) {
					throw new FactoryExceptionCustomException('La cantidad ingresada excede la cantidad que figura en remito');
				}

				$pendienteParaDescontar = $item->cantidad;
				$item->usaRango = 'N';
				$item->importe = $cantidad * $precioUnitario;
			}

			$remitoPorOrdenDeCompra->cantidadPendiente -= $pendienteParaDescontar;
			$item->cantidad = $pendienteParaDescontar;
			$remitoPorOrdenDeCompra->guardar();
			$item->remitoPorOrdenDeCompra = $remitoPorOrdenDeCompra;
		}

		if ($gravado == 'S') {
			$totalNetoGravado += $item->importe;
		} else {
			$totalNetoNoGravado += $item->importe;
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
$idDocumentoProveedor = Funciones::post('idDocumentoProveedor');
$diferenciaAdmisible = 0.05; //Centavos que puede diferir el impuesto de la factura con respecto al calculado.
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
$detallesNormalizados = array();
$impuestosNormalizados = array();
$totalDocumento = 0;
$totalNetoGravado = 0;
$totalNetoNoGravado = 0;
$totalImpuestos = 0;
$jsonSuccess = array();

try {
	Factory::getInstance()->beginTransaction();

	$documento = Factory::getInstance()->getDocumentoProveedor($idDocumentoProveedor);
	$tipoDocumento = $documento->tipoDocum;
	switch ($documento->tipoDocum) {
		case TiposDocumento::notaDeCredito:
			$documento = Factory::getInstance()->getNotaDeCreditoProveedor($idDocumentoProveedor);
			break;
		case TiposDocumento::notaDeDebito:
			$documento = Factory::getInstance()->getNotaDeDebitoProveedor($idDocumentoProveedor);
			break;
		case TiposDocumento::factura:
			$documento = Factory::getInstance()->getFacturaProveedor($idDocumentoProveedor);
			break;
		default:
			break;
	}

	if ($documento->importePendiente != $documento->importeTotal) {
		throw new FactoryExceptionCustomException('No puede editar un documento que ya fue aplicado');
	}

	if ($documento->facturaGastos == 'S') {
		throw new FactoryExceptionCustomException('El documento que se intenta editar es un documento de gastos');
	}

	$documentoNuevo = $documento;
	$documento->borrar();

	$documentoNuevo->id = null;
	$documentoNuevo->idAsientoContable = null;
	$documentoNuevo->asientoContable = null;
	$documentoNuevo->letra = $documentoNuevo->proveedor->condicionIva->letraFacturaProveedor;
	$documentoNuevo->condicionPlazoPago = $documentoNuevo->proveedor->plazoPago;
	$documentoNuevo->puntoVenta = $ptoVenta;
	$documentoNuevo->nroDocumento = $numero;
	$documentoNuevo->fecha = $fechaDocumento;
	$documentoNuevo->fechaVencimiento = $fechaVencimiento;
	$documentoNuevo->fechaPeriodoFiscal = $fechaPeriodoFiscal;
	$documentoNuevo->netoGravado = $netoGravado;
	$documentoNuevo->netoNoGravado = $netoNoGravado;
	$documentoNuevo->importeTotal = $importeTotal;
	$documentoNuevo->observaciones = $observaciones;
	$documentoNuevo->documentoEnConflicto = $documentoEnConflicto;
	Factory::getInstance()->marcarParaInsertar($documentoNuevo);

	if (is_null($ptoVenta) || is_null($numero) || is_null($fechaDocumento) || is_null($fechaVencimiento) ||
		is_null($fechaPeriodoFiscal) || is_null($netoGravado) || is_null($netoNoGravado) || is_null($importeTotal)) {
		throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó.');
	}

	if ($importeTotal < 0 || $netoGravado < 0 || $netoNoGravado < 0) {
		throw new FactoryExceptionCustomException('Error en el formato de importes en efectivo.');
	}

	if ($netoGravado == 0 && count($impuestos) > 0) {
		throw new FactoryExceptionCustomException('No puede agregar impuestos sobre un documento sin neto gravado.');
	}

	if ($tipoDocumento == 'FAC') {
		if (is_null($detallesRemitos) && is_null($detallesComunes)) {
			throw new FactoryExceptionCustomException('El documento que intenta confeccionar no puede no tener detalles.');
		}
		agregarValidarDetalles($detallesRemitos);
	} else if ($tipoDocumento == 'NDB' || $tipoDocumento == 'NCR') {
		if (is_null($detallesComunes)) {
			throw new FactoryExceptionCustomException('El documento que intenta confeccionar no puede no tener detalles.');
		}
	} else {
		throw new FactoryExceptionCustomException('El tipo de documento especificado no existe.');
	}

	if ($empresa == '1') {
		$where = 'punto_venta = ' . Datos::objectToDB($ptoVenta) . ' AND ';
		$where .= 'tipo_docum = ' . Datos::objectToDB($tipoDocumento) . ' AND ';
		$where .= 'nro_documento = ' . Datos::objectToDB($numero) . ' AND ';
		$where .= 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ';
		$where .= 'empresa = ' . Datos::objectToDB('1') . ' AND ';
		$where .= 'anulado = ' . Datos::objectToDB('N');

		$lista = Factory::getInstance()->getListObject('DocumentoProveedor', $where);

		if (count($lista) > 0) {
			throw new FactoryExceptionCustomException('El documento ingresado ya existe en el sistema');
		}
	}

	$arrayUnicidadImpuestos = array();
	foreach ($impuestos as $impuesto) {
		if ($arrayUnicidadImpuestos[$impuesto['idImpuesto']]) {
			throw new FactoryExceptionCustomException('No puede ingresar dos veces el mismo impuesto.');
		}

		if ($impuesto['porcentaje'] >= 100 || $impuesto['porcentaje'] <= 0) {
			throw new FactoryExceptionCustomException('Alguno de los porcentajes de impuestos ingresados son inconsistentes.');
		}

		if (is_null($impuesto['idImpuesto']) || is_null($impuesto['importe']) || is_null($impuesto['porcentaje'])) {
			throw new FactoryExceptionCustomException('Todos los campos de los impuestos son obligatorios.');
		}

		$arrayUnicidadImpuestos[$impuesto['idImpuesto']] = $impuesto['idImpuesto'];
		$impuestoQuery = Factory::getInstance()->getImpuesto($impuesto['idImpuesto']);
		$impuestoPorDocProveedor = Factory::getInstance()->getImpuestoPorDocumentoProveedor();
		$impuestoPorDocProveedor->impuesto = $impuestoQuery;
		$impuestoPorDocProveedor->porcentaje = $impuesto['porcentaje'];
		$impuestoPorDocProveedor->importe = Funciones::toFloat($impuesto['importe'], 2);
		$impuestosNormalizados[] = $impuestoPorDocProveedor;

		$totalImpuestos += Funciones::toFloat($impuesto['importe'], 2);
	}

	agregarValidarDetalles($detallesComunes);

	if (Funciones::toFloat($totalDocumento, 2) != (Funciones::toFloat($netoGravado + $netoNoGravado, 2))) {
		throw new FactoryExceptionCustomException('El importe total del documento no coincide con la suma de los importes de los detalles.');
	}

	$diferencia = $importeTotal - (Funciones::toFloat($totalNetoGravado + $totalNetoNoGravado + $totalImpuestos, 2));
	if (abs($diferencia) > $diferenciaAdmisibleDocumento) {
		throw new FactoryExceptionCustomException('El importe total ingresado difiere del importe calculado a partir de los parciales');
	}

	$documentoNuevo->detalle = $detallesNormalizados;
	$documentoNuevo->impuestos = $impuestosNormalizados;
	$documentoNuevo->guardar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El documento se editó correctamente.', $jsonSuccess);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar agregar el documento.');
}

?>
<?php } ?>