<?php require_once('premaster.php'); ?>
<?php

define('COD_CAJA_TESORERIA', 100);
define('COD_CAJA_COBRANZA', 100);
define('IMPUTACION_RECIBOS', '1111300');
define('FECHA_DOCUMENTOS', '01/09/2013');
define('NRO_DOCUMENTO_PROVEEDOR', 0);
define('EFECTIVO_CAJA_COBRANZA', 0);
define('COD_BANCO_CREDICOOP', 7);
define('COD_CUENTA_CREDICOOP', 1);

try {
	/*
	fix_recibos();
	generar_recibos();
	generar_documentos_proveedores();
	generar_cheques_terceros();
	generar_cheques_propios();
	poner_efectivo_caja();
	*/
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

function fix_recibos() {
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'nro_documento > 0 AND ';
	$where .= 'importe_total > 0 AND ';
	$where .= 'cod_cliente IS NOT NULL AND ';
	$where .= 'cod_cliente > ' . Datos::objectToDB(0) . ' ';
	$recibos = Factory::getInstance()->getListObject('ReciboOLD', $where . ' ORDER BY cod_cliente ASC');
	foreach ($recibos as $r) {
		/** @var ReciboOLD $r */
		$rec = Factory::getInstance()->getRecibo();
		$rec->empresa = $r->empresa;
		$rec->datosSinValidar = array(
			'tipoRecibo'		=> 'CD',
			'idCliente'			=> $r->idCliente,
			'idImputacion'		=> IMPUTACION_RECIBOS,
			'idCaja_E'			=> COD_CAJA_COBRANZA,
			'fechaDocumento'	=> $r->fecha,
			'observaciones'		=> $r->observaciones,
			'usuario'			=> Usuario::logueado()
		);
		$importes = array('E' => array(array('importe' => $r->importeTotal)));
		$rec->importesSinValidar['E'] = $importes;
		$rec->guardar();
	}
}

function generar_recibos() {
	$arr = array();
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'nro_documento > 0 AND ';
	$where .= 'cod_cliente IS NOT NULL AND ';
	$where .= 'cod_cliente > ' . Datos::objectToDB(0) . ' ';
	//$where .= 'operacion_tipo = ' . Datos::objectToDB('CD');
	$recibos = Factory::getInstance()->getListObject('ReciboOLD', $where . ' ORDER BY cod_cliente ASC');
	foreach ($recibos as $r) {
		/** @var ReciboOLD $r */
		if (!isset($arr[$r->idCliente])) {
			$arr[$r->idCliente] = array();
		}
		if (!isset($arr[$r->idCliente][$r->empresa])) {
			$arr[$r->idCliente][$r->empresa] = 0;
		}
		$arr[$r->idCliente][$r->empresa] += $r->importeTotal;
	}
	foreach ($arr as $idCliente => $empresas) {
		foreach ($empresas as $empresa => $importeTotal) {
			$rec = Factory::getInstance()->getRecibo();
			$rec->empresa = $empresa;
			$rec->datosSinValidar = array(
				'tipoRecibo'		=> 'CD',
				'idCliente'			=> $idCliente,
				//'idSucursal'		=> 'P',
				//'recibidoDe'		=> $r->,
				'idImputacion'		=> IMPUTACION_RECIBOS,
				'idCaja_E'			=> COD_CAJA_COBRANZA,
				'fechaDocumento'	=> FECHA_DOCUMENTOS,
				'usuario'			=> Usuario::logueado()
			);
			$importes = array('E' => array(array('importe' => $importeTotal)));
			$rec->importesSinValidar['E'] = $importes;
			$rec->guardar();
		}
	}
}

function generar_documentos_proveedores() {
	$letras = array();
	$arr = array();
	$documentos = Factory::getInstance()->getArrayFromView('cuenta_corriente_historica_proveedor', 'cod_prov IS NOT NULL ORDER BY cod_prov ASC');
	foreach ($documentos as $d) {
		$idp = $d['cod_prov'];
		if (!isset($arr[$idp])) {
			$arr[$idp] = array();
		}
		$emp = $d['empresa'];
		if (!isset($arr[$idp][$emp])) {
			$arr[$idp][$emp] = 0;
		}
		$debe = $d['tipo_docum'] == 'FAC' || $d['tipo_docum'] == 'NDB';
		$arr[$idp][$emp] += ($debe ? 1 : -1) * $d[($debe ? 'importe_debe' : 'importe_haber')];
		if (!is_null($d['letra'])) {
			$letras[$idp] = $d['letra'];
		}
	}
	foreach ($arr as $idProveedor => $empresas) {
		foreach ($empresas as $empresa => $saldo) {
			if ($saldo == 0) {
				continue;
			}
			$documento = ($saldo < 0 ? Factory::getInstance()->getNotaDeCreditoProveedor() : Factory::getInstance()->getNotaDeDebitoProveedor());
			$absSaldo = abs($saldo);

			$documento->empresa = $empresa;
			try {
				$documento->proveedor = Factory::getInstance()->getProveedor($idProveedor);
			} catch (FactoryExceptionRegistroNoExistente $ex) {
				continue;
			}
			try {
				$documento->letra = $documento->getLetra();
			} catch (FactoryExceptionRegistroNoExistente $ex) {
				$documento->letra = (isset($letras[$idProveedor])) ? $letras[$idProveedor] : 'A';
			}
			$documento->condicionPlazoPago = $documento->proveedor->plazoPago;
			$documento->puntoVenta = 0;
			$documento->nroDocumento = NRO_DOCUMENTO_PROVEEDOR;
			$documento->fecha = FECHA_DOCUMENTOS;
			$documento->fechaVencimiento = FECHA_DOCUMENTOS;
			$documento->fechaPeriodoFiscal = FECHA_DOCUMENTOS;
			$documento->netoGravado = 0;
			$documento->netoNoGravado = $absSaldo;
			$documento->importeTotal = $absSaldo;
			$documento->observaciones = 'Documento inicial por migración';
			$documento->documentoEnConflicto = 'N';

			//Meto el detalle
			$item = Factory::getInstance()->getDocumentoProveedorItem();
			$item->cantidad = 1;
			$item->precioUnitario = $absSaldo;
			$item->importe = $item->precioUnitario * $item->cantidad;
			$item->documentoProveedor = $docuemnto;
			$item->gravado = 'N';
			$item->imputacion = Factory::getInstance()->getImputacion('1112010');
			$item->descripcion = 'Documento inicial por migración';
			$item->origenDetalle = null;
			//

			$documento->detalle = array($item);
			$documento->guardar();
		}
	}
}

function generar_cheques_terceros() {
	$cheques = Factory::getInstance()->getArrayFromView('valores_recibo_cartera_entrega', 'operac_tipo_egreso IS NULL ORDER BY fecha_vencimiento ASC');
	foreach ($cheques as $c) {
		$cheque = Factory::getInstance()->getCheque();
		$cheque->empresa = $c['empresa'];
		$cheque->cliente = Factory::getInstance()->getCliente($c['cod_cli']);
		$bancos = Factory::getInstance()->getListObject('Banco', 'numero_banco = ' . Datos::objectToDB($c['cod_banco']));
		if (!count($bancos)) {
			throw new Exception('No existe el banco número ' . $c['cod_banco']);
		}
		$cheque->banco = $bancos[0];
		$cheque->numero = $c['nro_cheq_o_pagare'];
		$cheque->libradorNombre = $c['librador'];
		$cheque->libradorCuit = $c['cuit_librador'];
		$cheque->importe = $c['importe'];
		$cheque->noALaOrden = $c['no_a_la_orden'];
		$cheque->cruzado = 'S';
		$cheque->cajaActual = Factory::getInstance()->getCaja(COD_CAJA_TESORERIA);
		$cheque->usuario = Usuario::logueado();
		$cheque->fechaEmision = $c['fecha_recibo'];
		$cheque->fechaVencimiento = $c['fecha_vencimiento'];

		Factory::getInstance()->persistir($cheque);
	}
}

function generar_cheques_propios() {
	$cheques = Factory::getInstance()->getArrayFromView('cheques_propios_emision_anul', 'vencimiento_fecha >= dbo.relativeDate(GETDATE(), ' . Datos::objectToDB('tomorrow') . ', 0) ORDER BY vencimiento_fecha ASC');
	foreach ($cheques as $c) {
		$cheque = Factory::getInstance()->getCheque();
		$cheque->empresa = $c['empresa'];
		$cheque->banco = Factory::getInstance()->getBanco(COD_BANCO_CREDICOOP);
		$cheque->cuentaBancaria = Factory::getInstance()->getCuentaBancaria(COD_CUENTA_CREDICOOP);
		$cheque->numero = $c['chq_propio_nro'];
		$cheque->libradorNombre = Config::RAZON_SPIRAL;
		$cheque->libradorCuit = Config::CUIT_SPIRAL;
		$cheque->importe = $c['importe_cheque_propio'];
		$cheque->noALaOrden = 'N';
		$cheque->cruzado = 'S';
		$cheque->cajaActual = Factory::getInstance()->getCaja(COD_CAJA_TESORERIA);
		$cheque->usuario = Usuario::logueado();
		$cheque->fechaEmision = $c['fecha_orden_pago'];
		$cheque->fechaVencimiento = $c['vencimiento_fecha'];

		Factory::getInstance()->persistir($cheque);
	}

	Datos::EjecutarSQLsinQuery('UPDATE cheque SET esperando_en_banco = ' . Datos::objectToDB('D') . ', concluido = ' . Datos::objectToDB('S') . ' WHERE cod_cuenta_bancaria IS NOT NULL');
}

function poner_efectivo_caja() {
	$caja = Factory::getInstance()->getCaja(COD_CAJA_COBRANZA);
	$caja->importeEfectivo = EFECTIVO_CAJA_COBRANZA;
	$caja->guardar();
}

?>