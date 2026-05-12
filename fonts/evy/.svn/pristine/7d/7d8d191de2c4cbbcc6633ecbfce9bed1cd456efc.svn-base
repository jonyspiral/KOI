<?php
# Ejemplo de Uso de Interface COM con Web Services AFIP (PyAfipWs)
# 2009 (C) Mariano Reingart <mariano@nsis.com.ar>
/*
function getMotivo($id) {
	switch ($id) {
		case '00': case 'NULL': return 'No hay error (solo como referencia)';
		case '01': return 'CUIT informada no es R.I.';
		case '02': return 'CUIT no autorizada a facturar electrónicamente';
		case '03': return 'CUIT registra inconvenientes con domicilio fiscal';
		case '04': return 'Punto de venta no se encuentra declarado';
		case '05': return 'Fecha del comprobante incorrecta';
		case '06': return 'CUIT no puede emitir comprobantes clase A';
		case '07': return 'Para comprobantes clase A debe indicar CUIT';
		case '08': return 'CUIT informada es inválida';
		case '09': return 'CUIT informada no existe en el padrón';
		case '10': return 'CUIT informada no corresponde a un R.I.';
		case '11': return 'El Nº de comprobante no es correlativo o la fecha no corresponde';
		case '12': return 'El rango informado se encuentra autorizado';
		case '13': return 'CUIT indicada se encuentra comprendida en el régimen establecido por la resolución general N° 2177 y/o en el título I de la resolución general N° 1361 art. 24 de la RG N° 2177-';
		default: return 'Hubo un warning que no se pudo parsear';
	}
}

try {

	# Crear objeto interface Web Service Autenticación y Autorización
	/** @noinspection PhpUndefinedClassInspection *//*
	$WSAA = new COM('WSAA');
	# Generar un Ticket de Requerimiento de Acceso (TRA)
	$tra = $WSAA->CreateTRA() ;
	
	# Especificar la ubicacion de los archivos certificado y clave privada
	$path = Config::pathBase . '\\includes\\fe\\';
	# Certificado: certificado es el firmado por la AFIP
	# ClavePrivada: la clave privada usada para crear el certificado
	$Certificado = "desarrollo.crt"; // certificado de prueba
	$ClavePrivada = "desarrollo.key"; // clave privada de prueba
	# Generar el mensaje firmado (CMS) ;
	$cms = $WSAA->SignTRA($tra, $path . $Certificado, $path . $ClavePrivada);
	
	# Llamar al web service para autenticar
	$ta = $WSAA->CallWSAA($cms); // homologación, desarrollo
	#$ta = $WSAA->CallWSAA($cms, "https://wsaa.afip.gov.ar/ws/services/LoginCms") # producción
	
	echo "Token de Acceso: " . $WSAA->Token . "<br>";
	echo "Sing de Acceso: " . $WSAA->Sign . "<br>";
	
	# Crear objeto interface Web Service de Factura Electrónica
	/** @noinspection PhpUndefinedClassInspection *//*
	$WSFE = new COM('WSFE') ;
	# Setear token y sign de autorización (pasos previos) Y CUIT del emisor
	$WSFE->Token = $WSAA->Token;
	$WSFE->Sign = $WSAA->Sign; 
	$WSFE->Cuit = "33710051459";
	
	# Conectar al Servicio Web de Facturación
	$ok = $WSFE->Conectar(); // pruebas
	#$ok = WSFE.Conectar("https://wsw.afip.gov.ar/wsfe/service.asmx") ' producción # producción
	
	# Llamo a un servicio nulo, para obtener el estado del servidor (opcional)
	$WSFE->Dummy();
	echo "appserver status "  . $WSFE->AppServerStatus . "<br>";
	echo "dbserver status " . $WSFE->DbServerStatus . "<br>";
	echo "authserver status " . $WSFE->AuthServerStatus . "<br>";
	
	# Recupera cantidad máxima de registros (opcional)
	$qty = $WSFE->RecuperarQty();
	
	# Recupera último número de secuencia ID
	$LastId = $WSFE->UltNro();
	
	# (opcional) Recupero último número de comprobante para un tipo de comprobante y un punto de venta (opcional)
	$tipo_cbte = 1;
	$punto_vta = 1;
	$LastCBTE = $WSFE->RecuperaLastCMP($punto_vta, $tipo_cbte);
	
	# Establezco los valores de la factura o lote a autorizar:
	$Fecha = date("Ymd");
	echo "Fecha " . $Fecha . "<br>";
	$id = $LastId + 1;
	$presta_serv = 1;
	$tipo_doc = 80;
	$nro_doc = "30708331615";
	$cbt_desde = $LastCBTE + 1;
	$cbt_hasta = $LastCBTE + 1;
	$imp_neto = "100.00";
	$impto_liq = "21.00";
	$impto_liq_rni = "0.00";
	$imp_op_ex = "0.00";
	$imp_total = "121.00";
	$imp_tot_conc = "0.00";
	$fecha_cbte = $Fecha;
	$fecha_venc_pago = $Fecha;
	
	# Fechas del período del servicio facturado (solo si presta_serv = 1)
	$fecha_serv_desde = $Fecha;
	$fecha_serv_hasta = $Fecha;
	
	/*
		id: Número de identificación secuencial (debe almacenarse en el sistema local) No es obligatorio llamar a UltNro, puede utilizarse un dato local secuencial.
		presta_serv: 0 o 1 para indicar si es una factura de servicios
		tipo_doc, nro_doc: Tipo (80 CUIT, 96 DNI, etc.) y número de Documento
		tipo_cbte: Tipo de comprobante (1 Factura A , 6 Factura B, etc.)
		punto_vta: Nº de punto de venta (debe estar autorizado)
		cbt_desde, cbt_hasta: Nº de comprobante (si es un solo comprobante, repetir Nº)
		imp_total: Importe total de la factura
		imp_tot_conc: Importe total de conceptos no gravados por el IVA
		imp_neto: Importe neto (gravado por el IVA) de la factura
		impto_liq: Importe del IVA liquidado (incluyendo percepciones de IVA)
		impto_liq_rni: Importe IVA RNI (no se utiliza mas, dejar 0.00)
		imp_op_ex: Importe de operaciones exentas
		fecha_cbte: Fecha del comprobante (no puede ser mayor o menor a 5 días)
		fecha_venc_pago: Fecha límite de vencimiento para el pago de la factura
		fecha_serv_desde, fecha_serv_hasta: Fechas del período de servicios prestado
	*//*

	# Llamo al WebService de Autorización para obtener el CAE
	$cae = $WSFE->Aut($id, $presta_serv, $tipo_doc, $nro_doc, 
		$tipo_cbte, $punto_vta, $cbt_desde, $cbt_hasta, 
		$imp_total, $imp_tot_conc, $imp_neto, $impto_liq, $impto_liq_rni, $imp_op_ex, 
		$fecha_cbte, $fecha_venc_pago, $fecha_serv_desde, $fecha_serv_hasta);

	echo "Qty=" . $qty . "<br>";
	echo "LastId=" . $LastId . "<br>";
	echo "LastCBTE=" . $LastCBTE . "<br>";
	echo "CAE=" . $cae . "<br>";
	echo "Vencimiento " . $WSFE->Vencimiento . "<br>"; # Fecha de vencimiento o vencimiento de la autorización
	
	# Verifico que no haya rechazo o advertencia al generar el CAE
	if ($cae == "") {
		echo "La página esta caida o la respuesta es inválida<br>";
	} elseif ($cae == "NULL" || $WSFE->Resultado != "A") {
		echo "No se asignó CAE (Rechazado). Motivos: " . getMotivo($WSFE->Motivo) . "<br>";
	} elseif ($WSFE->Motivo!="NULL" && $WSFE->Motivo!="00") {
		echo "Se asignó CAE pero con advertencias. Motivos: " . getMotivo($WSFE->Motivo) . "<br>";
	} 

} catch (Exception $e) {
	echo 'Excepción: ',  $e->getMessage(), "<br>";
}
*/
?>
