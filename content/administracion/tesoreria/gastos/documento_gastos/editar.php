<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/documento_gastos/editar/')) { ?>
<?php

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
$detallesRemitos = Funciones::post('detallesRemitos');
$detallesComunes = Funciones::post('detallesComunes');
$impuestos = Funciones::post('impuestos');
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
$arrayRemitos = array();

try {
	$documento = Factory::getInstance()->getDocumentoProveedor($idDocumentoProveedor);

	if ($documento->empresa != $empresa) {
		throw new FactoryExceptionCustomException('El documento no corresponde a la sesión activa');
	}

	if ($documento->importePendiente != $documento->importeTotal) {
		throw new FactoryExceptionCustomException('No puede editar un documento que ya fue aplicado');
	}

	if (is_null($fechaDocumento) || is_null($fechaVencimiento) || is_null($fechaPeriodoFiscal) ||
		is_null($netoGravado) || is_null($netoNoGravado) || is_null($importeTotal)
	) {
		throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó');
	}

	if ($documento->empresa == '1') {
		if (is_null($ptoVenta) || is_null($numero)) {
			throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó');
		}
	}

	if ($documento->documentoGastoDatos->id) {
		if ($documento->empresa == '1') {
			if (is_null($razonSocial) || is_null($cuit) || is_null($condicionIva) || is_null($idImputacion)) {
				throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó');
			}
		}

		$documento->esProveedor = false;
	} else {
		if (is_null($idProveedor)) {
			throw new FactoryExceptionCustomException('Alguno de los campos obligatorios no se completó');
		}

		if($empresa == '1'){
			$where = 'punto_venta = ' . Datos::objectToDB($ptoVenta) . ' AND ';
			$where .= 'nro_documento = ' . Datos::objectToDB($numero) . ' AND ';
			$where .= 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ';
			$where .= 'empresa = ' . Datos::objectToDB('1') . ' AND ';
			$where .= 'anulado = ' . Datos::objectToDB('N');

			$lista = Factory::getInstance()->getListObject('DocumentoProveedor', $where);

			if(count($lista) > 1)
				throw new FactoryExceptionCustomException('El documento ingresado ya existe en el sistema');
		}

		$documento->esProveedor = true;
	}

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

	if (!is_null($documento->idDocumentoGastoDatos)) {
		$documento->documentoGastoDatos->direccion->calle = $calle;
		$documento->documentoGastoDatos->direccion->numero = $numeroCalle;
		$documento->documentoGastoDatos->direccion->piso = $piso;
		$documento->documentoGastoDatos->direccion->departamento = $dpto;
		$documento->documentoGastoDatos->direccion->pais = Factory::getInstance()->getPais($pais);
		$documento->documentoGastoDatos->direccion->provincia = Factory::getInstance()->getProvincia($pais, $provincia);
		$documento->documentoGastoDatos->direccion->localidad = Factory::getInstance()->getLocalidad($pais, $provincia, $localidad);
		$documento->documentoGastoDatos->direccion->codigoPostal = $codPostal;
		$documento->documentoGastoDatos->razonSocial = $razonSocial;
		$documento->documentoGastoDatos->cuit = $cuit;
		$documento->documentoGastoDatos->imputacion = Factory::getInstance()->getImputacion($idImputacion);
		$documento->documentoGastoDatos->condicionIva = Factory::getInstance()->getCondicionIva($condicionIva);
		$documento->letra = $letra;
	}

	if ($importeTotal < 0 || $netoGravado < 0 || $netoNoGravado < 0) {
		throw new FactoryExceptionCustomException('Error en el formato de importes en efectivo.');
	}

	if ($netoGravado == 0 && count($impuestos) > 0) {
		throw new FactoryExceptionCustomException('No puede agregar impuestos sobre un documento sin neto gravado.');
	}

	if (is_null($detallesRemitos) && is_null($detallesComunes)) {
		throw new FactoryExceptionCustomException('El documento que intenta confeccionar no puede no tener detalles.');
	}

	if (!($tipoDocumento != 'FAC' || $tipoDocumento != 'NDC' || $tipoDocumento != 'NDB')) {
		throw new FactoryExceptionCustomException('El tipo de documento especificado no existe.');
	}

	$detallesEditados = array();
	foreach ($detallesComunes as $item) {
		if ($item['idDocumentoProveedor']) {
			$detalle = Factory::getInstance()->getDocumentoProveedorItem($item['idDocumentoProveedor'], $item['nroItem']);
			if ($item['borrar'] == 'S') {
				Factory::getInstance()->marcarParaBorrar($detalle);
			}
		} else {
			$detalle = Factory::getInstance()->getDocumentoProveedorItem();
		}

		if ($item['cantidad'] < 0 || $item['precioUnitario'] < 0) {
			throw new FactoryExceptionCustomException('Error en el formato de importes en efectivo.');
		}

		$importe = $item['precioUnitario'] * $item['cantidad'];
		$detalle->descripcion = $item['descripcion'];
		$detalle->precioUnitario = $item['precioUnitario'];
		$detalle->cantidad = $item['cantidad'];
		$detalle->imputacion = Factory::getInstance()->getImputacion($item['idImputacion']);
		$detalle->importe = $importe;
		$detalle->gravado = $item['gravado'];

		$detallesEditados[] = $detalle;

		if ($item['borrar'] != 'S') {
			if ($item['gravado'] == 'S') {
				$totalNetoGravado += Funciones::toFloat($importe, 2);
			} else {
				$totalNetoNoGravado += Funciones::toFloat($importe, 2);
			}
			$totalDocumento += $importe;
		}
	}

	$arrayUnicidadImpuestos = array();
	$impuestosEditados = array();
	foreach ($impuestos as $item) {
		if ($arrayUnicidadImpuestos[$item['idImpuesto']]) {
			throw new FactoryExceptionCustomException('No puede ingresar dos veces el mismo impuesto.');
		}

		if (is_null($item['idImpuesto']) || is_null($item['importe'])) {
			throw new FactoryExceptionCustomException('Todos los campos de los impuestos son obligatorios.');
		}

		$arrayUnicidadImpuestos[$item['idImpuesto']] = $item['idImpuesto'];

		if ($item['idDocumentoProveedor']) {
			$impuesto = Factory::getInstance()->getImpuestoPorDocumentoProveedor($item['idImpuesto'], $item['idDocumentoProveedor']);
			if ($item['borrar'] == 'S') {
				Factory::getInstance()->marcarParaBorrar($impuesto);
			}
		} else {
			$impuesto = Factory::getInstance()->getImpuestoPorDocumentoProveedor();
		}
		$impuesto->impuesto = Factory::getInstance()->getImpuesto($item['idImpuesto']);
		$impuesto->porcentaje = $item['porcentaje'];
		$impuesto->importe = Funciones::toFloat($item['importe']);

		$impuestosEditados[] = $impuesto;

		if ($item['borrar'] != 'S') {
			$totalImpuestos += $item['importe'];
		}
	}

	if (Funciones::formatearDecimales($totalDocumento, 2, '.') != ($netoGravado + $netoNoGravado)) {
		throw new FactoryExceptionCustomException('El importe total del documento no coincide con la suma de los importes de los detalles.');
	}

	$diferencia = $importeTotal - ($totalNetoGravado + $totalNetoNoGravado + $totalImpuestos);
	if (abs($diferencia) > $diferenciaAdmisible) {
		throw new FactoryExceptionCustomException('El importe total ingresado difiere del importe calculado a partir de los parciales');
	}

	$documento->detalle = $detallesEditados;
	$documento->impuestos = $impuestosEditados;
	$documento->guardar();

	Html::jsonSuccess('El documento se editó correctamente', $jsonSuccess);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar editar el documento');
}

?>
<?php } ?>