CREATE VIEW [dbo].[movimientos_caja_v_noanul] AS


CREATE VIEW movimientos_caja_v_noanul AS

	SELECT 'I' tipo, 'REC' tipo_documento, r.nro_recibo numero, (case when r.cod_cliente is null then r.recibido_de else c.razon_social end) de, 'SPIRAL SHOES S.A.' para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE r.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE r.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		(SELECT ISNULL(SUM(t1.importe), 0)
		FROM importe_por_operacion_c ipoc3
		INNER JOIN importe_por_operacion_d ipod3 ON ipoc3.cod_importe_operacion = ipod3.cod_importe_operacion AND ipod3.tipo_importe = 'T'
		INNER JOIN transferencia_bancaria_importe t1 ON ipod3.cod_importe = t1.cod_transferencia_ban
		WHERE r.cod_importe_operacion = ipoc3.cod_importe_operacion) transferencias,
		(SELECT ISNULL(SUM(r1.importe), 0)
		FROM importe_por_operacion_c ipoc4
		INNER JOIN importe_por_operacion_d ipod4 ON ipoc4.cod_importe_operacion = ipod4.cod_importe_operacion AND ipod4.tipo_importe = 'S'
		INNER JOIN retencion_sufrida r1 ON ipod4.cod_importe = r1.cod_retencion
		WHERE r.cod_importe_operacion = ipoc4.cod_importe_operacion) retenciones,
		r.importe_total total, ipoc.cod_caja, dbo.relativeDate(r.fecha_documento,'today',0) fecha,
		r.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		r.anulado
	FROM recibo r
	LEFT OUTER JOIN clientes c ON c.cod_cliente = r.cod_cliente
	INNER JOIN importe_por_operacion_c ipoc ON r.cod_importe_operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT 'E' tipo, 'OP' tipo_documento, op.nro_orden_de_pago numero, 'SPIRAL SHOES S.A.' de, (case when op.cod_proveedor is null then op.beneficiario else p.razon_social end) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE op.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE op.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		(SELECT ISNULL(SUM(t1.importe), 0)
		FROM importe_por_operacion_c ipoc3
		INNER JOIN importe_por_operacion_d ipod3 ON ipoc3.cod_importe_operacion = ipod3.cod_importe_operacion AND ipod3.tipo_importe = 'T'
		INNER JOIN transferencia_bancaria_importe t1 ON ipod3.cod_importe = t1.cod_transferencia_ban
		WHERE op.cod_importe_operacion = ipoc3.cod_importe_operacion) transferencias,
		(SELECT ISNULL(SUM(r1.importe), 0)
		FROM importe_por_operacion_c ipoc4
		INNER JOIN importe_por_operacion_d ipod4 ON ipoc4.cod_importe_operacion = ipod4.cod_importe_operacion AND ipod4.tipo_importe = 'R'
		INNER JOIN retencion_efectuada r1 ON ipod4.cod_importe = r1.cod_retencion
		WHERE op.cod_importe_operacion = ipoc4.cod_importe_operacion) retenciones,
		op.importe_total total, ipoc.cod_caja, dbo.relativeDate(op.fecha_documento,'today',0) fecha,
		op.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		op.anulado
	FROM orden_de_pago op
	LEFT OUTER JOIN proveedores_datos p ON p.cod_prov = op.cod_proveedor
	INNER JOIN importe_por_operacion_c ipoc ON op.cod_importe_
operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT 'I' tipo, 'ICP' tipo_documento, icp.cod_ingreso_cheque_propio numero, '-' de, '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre para,
		0 efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE icp.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(icp.fecha_alta,'today',0) fecha,
		icp.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM ingreso_cheque_propio icp
	INNER JOIN importe_por_operacion_c ipoc ON icp.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja

	UNION ALL

	SELECT 'E' tipo, 'DC' tipo_documento, dc.cod_acreditar_debitar_cheque numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d dc1
		INNER JOIN importe_por_operacion_c ipoc1 ON dc1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE dc1.entrada_salida = 'S' AND dcc.cod_acreditar_debitar_cheque = dc1.cod_acreditar_debitar_cheque AND dcc.empresa = dc1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d dc2
		INNER JOIN importe_por_operacion_c ipoc2 ON dc2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE dc2.entrada_salida = 'E' AND dcc.cod_acreditar_debitar_cheque = dc2.cod_acreditar_debitar_cheque AND dcc.empresa = dc2.empresa) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE dc.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE dc.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(dcc.fecha,'today',0) fecha,
		dc.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM acreditar_debitar_cheque_d dc
	INNER JOIN acreditar_debitar_cheque_c dcc ON dc.cod_acreditar_debitar_cheque = dcc.cod_acreditar_debitar_cheque AND dc.empresa = dcc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON dc.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE dc.entrada_salida = 'S' AND dcc.tipo = 'D'

	UNION ALL

	SELECT (case when ac.entrada_salida = 'E' then 'I' else 'E' end) tipo, 'AC' tipo_documento, ac.cod_acreditar_debitar_cheque numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d ac1
		INNER JOIN importe_por_operacion_c ipoc1 ON ac1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE ac1.entrada_salida = 'S' AND acc.cod_acreditar_debitar_cheque = ac1.cod_acreditar_debitar_cheque AND acc.empresa = ac1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d ac2
		INNER JOIN importe_por_operacion_c ipoc2 ON ac2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE ac2.entrada_salida = 'E
' AND acc.cod_acreditar_debitar_cheque = ac2.cod_acreditar_debitar_cheque AND acc.empresa = ac2.empresa) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE ac.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE ac.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(acc.fecha,'today',0) fecha,
		ac.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM acreditar_debitar_cheque_d ac
	INNER JOIN acreditar_debitar_cheque_c acc ON acc.cod_acreditar_debitar_cheque = ac.cod_acreditar_debitar_cheque AND ac.empresa = acc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ac.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE acc.tipo = 'C'

	UNION ALL

	SELECT (case when rc.entrada_salida = 'S' then 'E' else 'I' end) tipo, 'RC' tipo_documento, rc.cod_rechazo_cheque numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM rechazo_de_cheque_d rc1
		INNER JOIN importe_por_operacion_c ipoc1 ON rc1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE rc1.entrada_salida = 'S' AND rcc.cod_rechazo_cheque = rc1.cod_rechazo_cheque AND rcc.empresa = rc1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM rechazo_de_cheque_d rc2
		INNER JOIN importe_por_operacion_c ipoc2 ON rc2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE rc2.entrada_salida = 'E' AND rcc.cod_rechazo_cheque = rc2.cod_rechazo_cheque AND rcc.empresa = rc2.empresa) para,
		0 efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE rc.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(rcc.fecha_documento,'today',0) fecha,
		rc.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM rechazo_de_cheque_d rc
	INNER JOIN rechazo_de_cheque_c rcc ON rcc.cod_rechazo_cheque = rc.cod_rechazo_cheque AND rcc.empresa = rc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON rc.cod_importe_operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT (case when db.entrada_salida = 'S' then 'E' else 'I' end) tipo, 'DB' tipo_documento, db.cod_deposito_bancario numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM deposito_bancario_d ti1
		INNER JOIN importe_por_operacion_c ipoc1 ON ti1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE ti1.entrada_salida = 'S' AND dbc.cod_deposito_bancario = ti1.cod_deposito_bancario AND dbc.empresa = ti1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM deposito_bancario_d ti2
		INNER JOIN importe_por_operacion_c ipoc2 ON ti2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE ti2.entrada_salida = 'E' AND dbc.cod_deposito_bancario = ti2.cod_deposito_bancario AND dbc.empresa = ti2.empresa) para,
		(SELECT I
SNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE db.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE db.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(dbc.fecha_documento,'today',0) fecha,
		db.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM deposito_bancario_d db
	INNER JOIN deposito_bancario_c dbc ON dbc.cod_deposito_bancario = db.cod_deposito_bancario AND dbc.empresa = db.empresa
	INNER JOIN importe_por_operacion_c ipoc ON db.cod_importe_operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT (case when ti.entrada_salida = 'S' then 'E' else 'I' end) tipo, 'TI' tipo_documento, ti.cod_transferencia_int numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM transferencia_interna_d ti1
		INNER JOIN importe_por_operacion_c ipoc1 ON ti1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE ti1.entrada_salida = 'S' AND tic.cod_transferencia_int = ti1.cod_transferencia_int AND tic.empresa = ti1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM transferencia_interna_d ti2
		INNER JOIN importe_por_operacion_c ipoc2 ON ti2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE ti2.entrada_salida = 'E' AND tic.cod_transferencia_int = ti2.cod_transferencia_int AND tic.empresa = ti2.empresa) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE ti.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE ti.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(tic.fecha_documento,'today',0) fecha,
		ti.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM transferencia_interna_d ti
	INNER JOIN transferencia_interna_c tic ON tic.cod_transferencia_int = ti.cod_transferencia_int AND tic.empresa = ti.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ti.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja

	UNION ALL

	SELECT (case when tbo.entrada_salida = 'E' then 'I' else 'E' end) tipo, 'TB' tipo_documento, tbo.cod_transferencia_ban numero,
		(case when tbo.entrada_salida = 'E' then 'Bancos de 3ros' else '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre end) de,
		(case when tbo.entrada_salida = 'S' then 'Bancos de 3ros' else '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre end) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe =
 e1.cod_efectivo
		WHERE tbo.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		tbo.importe_total total, ipoc.cod_caja, dbo.relativeDate(tbo.fecha,'today',0) fecha,
		tbo.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		tbo.anulado
	FROM transferencia_bancaria_operacion tbo
	INNER JOIN cuenta_bancaria cb ON tbo.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
	INNER JOIN caja caja ON caja.cod_caja = cb.cod_caja
	INNER JOIN importe_por_operacion_c ipoc ON tbo.cod_importe_operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT 'E' tipo, 'RG' tipo_documento, rg.cod_rendicion_gastos numero, '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre de, '-' para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE rg.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		rg.importe_total total,
		ipoc.cod_caja, dbo.relativeDate(rg.fecha_documento,'today',0) fecha,
		rg.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		rg.anulado
	FROM rendicion_de_gastos rg
	INNER JOIN importe_por_operacion_c ipoc ON rg.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja

	UNION ALL

	SELECT 'I' tipo, 'AS' tipo_documento, aps.nro_aporte_socio numero, s.nombre de, 'SPIRAL SHOES S.A.' para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE aps.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE aps.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		(SELECT ISNULL(SUM(t1.importe), 0)
		FROM importe_por_operacion_c ipoc3
		INNER JOIN importe_por_operacion_d ipod3 ON ipoc3.cod_importe_operacion = ipod3.cod_importe_operacion AND ipod3.tipo_importe = 'T'
		INNER JOIN transferencia_bancaria_importe t1 ON ipod3.cod_importe = t1.cod_transferencia_ban
		WHERE aps.cod_importe_operacion = ipoc3.cod_importe_operacion) transferencias,
		(SELECT ISNULL(SUM(r1.importe), 0)
		FROM importe_por_operacion_c ipoc4
		INNER JOIN importe_por_operacion_d ipod4 ON ipoc4.cod_importe_operacion = ipod4.cod_importe_operacion AND ipod4.tipo_importe = 'S'
		INNER JOIN retencion_sufrida r1 ON ipod4.cod_importe = r1.cod_retencion
		WHERE aps.cod_importe_operacion = ipoc4.cod_importe_operacion) retenciones,
		aps.importe_total total, ipoc.cod_caja, dbo.relativeDate(aps.fecha_documento,'today',0) fecha,
		aps.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		aps.anulado
	FROM aporte_socio aps
	LEFT OUTER JOIN socio s ON s.cod_socio = aps.cod_socio
	INNER JOIN importe_por_operacion_c ipoc ON aps.cod_importe_operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT 'E' tipo, 'RS' tipo_documento, aps.nro_retiro_socio numero, 'SPIRAL SHOES S.A.' de, s.nombre para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE aps.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0
)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 's'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE aps.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		(SELECT ISNULL(SUM(t1.importe), 0)
		FROM importe_por_operacion_c ipoc3
		INNER JOIN importe_por_operacion_d ipod3 ON ipoc3.cod_importe_operacion = ipod3.cod_importe_operacion AND ipod3.tipo_importe = 'T'
		INNER JOIN transferencia_bancaria_importe t1 ON ipod3.cod_importe = t1.cod_transferencia_ban
		WHERE aps.cod_importe_operacion = ipoc3.cod_importe_operacion) transferencias,
		(SELECT ISNULL(SUM(r1.importe), 0)
		FROM importe_por_operacion_c ipoc4
		INNER JOIN importe_por_operacion_d ipod4 ON ipoc4.cod_importe_operacion = ipod4.cod_importe_operacion AND ipod4.tipo_importe = 'S'
		INNER JOIN retencion_sufrida r1 ON ipod4.cod_importe = r1.cod_retencion
		WHERE aps.cod_importe_operacion = ipoc4.cod_importe_operacion) retenciones,
		aps.importe_total total, ipoc.cod_caja, dbo.relativeDate(aps.fecha_documento,'today',0) fecha,
		aps.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		aps.anulado
	FROM retiro_socio aps
	LEFT OUTER JOIN socio s ON s.cod_socio = aps.cod_socio
	INNER JOIN importe_por_operacion_c ipoc ON aps.cod_importe_operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT (case when ti.entrada_salida = 'S' then 'E' else 'I' end) tipo, 'VC' tipo_documento, ti.cod_venta_cheques numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM venta_cheques_d ti1
		INNER JOIN importe_por_operacion_c ipoc1 ON ti1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE ti1.entrada_salida = 'S' AND tic.cod_venta_cheques = ti1.cod_venta_cheques AND tic.empresa = ti1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM venta_cheques_d ti2
		INNER JOIN importe_por_operacion_c ipoc2 ON ti2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE ti2.entrada_salida = 'E' AND tic.cod_venta_cheques = ti2.cod_venta_cheques AND tic.empresa = ti2.empresa) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE ti.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE ti.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(tic.fecha_documento,'today',0) fecha,
		ti.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM venta_cheques_d ti
	INNER JOIN venta_cheques_c tic ON tic.cod_venta_cheques = ti.cod_venta_cheques AND tic.empresa = ti.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ti.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja

	UNION ALL

	SELECT 'I' tipo, 'PRE' tipo_documento, r.nro_prestamo numero, cb.nombre_cuenta de, 'SPIRAL SHOES S.A.' para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE r.cod_importe_operacion = ipoc1.cod_importe_oper
acion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		r.importe_total total, ipoc.cod_caja, dbo.relativeDate(r.fecha_documento,'today',0) fecha,
		r.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		r.anulado
	FROM prestamo r
	LEFT OUTER JOIN cuenta_bancaria cb ON cb.cod_cuenta_bancaria = r.cod_cuenta_bancaria
	INNER JOIN importe_por_operacion_c ipoc ON r.cod_importe_operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT 'I' tipo, 'CCV' tipo_documento, ccvd.cod_cobro_cheque_ventanilla numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM cobro_cheque_ventanilla_d ccvc1
		INNER JOIN importe_por_operacion_c ipoc1 ON ccvc1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE ccvc1.entrada_salida = 'S' AND ccvc.cod_cobro_cheque_ventanilla = ccvc1.cod_cobro_cheque_ventanilla AND ccvc.empresa = ccvc1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM cobro_cheque_ventanilla_d ccvc2
		INNER JOIN importe_por_operacion_c ipoc2 ON ccvc2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE ccvc2.entrada_salida = 'E' AND ccvc.cod_cobro_cheque_ventanilla = ccvc2.cod_cobro_cheque_ventanilla AND ccvc.empresa = ccvc2.empresa) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE ccvd.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(ccvc.fecha_alta,'today',0) fecha,
		ccvd.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM cobro_cheque_ventanilla_d ccvd
	INNER JOIN cobro_cheque_ventanilla_c ccvc ON ccvc.cod_cobro_cheque_ventanilla = ccvd.cod_cobro_cheque_ventanilla AND ccvc.empresa = ccvd.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ccvd.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja
	WHERE ccvd.entrada_salida = 'E'
	
	UNION ALL
	
	SELECT 'I' tipo, 'RIC' tipo_documento, ric.cod_reingreso_cheques_cartera numero, (case when p.cod_prov IS NULL then 'Otros egresos' else p.razon_social end) de, 'SPIRAL SHOES S.A.' para,
		0 efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE ric.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		ric.importe_total total, ipoc.cod_caja, dbo.relativeDate(ric.fecha_alta,'today',0) fecha,
		ric.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM dbo.reingreso_cheque_cartera ric
	LEFT OUTER JOIN proveedores_datos p ON p.cod_prov = ric.cod_proveedor
	INNER JOIN importe_por_operacion_c ipoc ON ric.cod_importe_operacion = ipoc.cod_importe_operacion

GO
