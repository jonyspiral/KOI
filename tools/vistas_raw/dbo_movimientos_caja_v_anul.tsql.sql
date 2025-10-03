CREATE VIEW [dbo].[movimientos_caja_v_anul] AS
CREATE VIEW movimientos_caja_v_anul AS

	SELECT 'E' tipo, 'REC' tipo_documento, r.nro_recibo numero, (case when r.cod_cliente is null then r.recibido_de else c.razon_social end) de, 'SPIRAL SHOES S.A.' para,
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
		r.importe_total total, ipoc.cod_caja, dbo.relativeDate(r.fecha_baja,'today',0) fecha,
		r.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		r.anulado
	FROM recibo r
	LEFT OUTER JOIN clientes c ON c.cod_cliente = r.cod_cliente
	INNER JOIN importe_por_operacion_c ipoc ON r.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE r.anulado = 'S'

	UNION ALL

	SELECT 'I' tipo, 'OP' tipo_documento, op.nro_orden_de_pago numero, 'SPIRAL SHOES S.A.' de, (case when op.cod_proveedor is null then op.beneficiario else p.razon_social end) para,
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
		op.importe_total total, ipoc.cod_caja, dbo.relativeDate(op.fecha_baja,'today',0) fecha,
		op.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM orden_de_pago op
	LEFT OUTER JOIN proveedores_datos p ON p.cod_prov = op.cod_proveedor
	INNER JOIN importe_por_operacion_c ipoc ON op.cod
_importe_operacion = ipoc.cod_importe_operacion
	WHERE op.anulado = 'S'

	UNION ALL

	SELECT (case when tbo.entrada_salida = 'E' then 'E' else 'I' end) tipo, 'TB' tipo_documento, tbo.cod_transferencia_ban numero,
		(case when tbo.entrada_salida = 'E' then 'Bancos de 3ros' else '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre end) de,
		(case when tbo.entrada_salida = 'S' then 'Bancos de 3ros' else '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre end) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE tbo.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		tbo.importe_total total, ipoc.cod_caja, dbo.relativeDate(tbo.fecha_baja,'today',0) fecha,
		tbo.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		tbo.anulado
	FROM transferencia_bancaria_operacion tbo
	INNER JOIN cuenta_bancaria cb ON tbo.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
	INNER JOIN caja caja ON caja.cod_caja = cb.cod_caja
	INNER JOIN importe_por_operacion_c ipoc ON tbo.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE tbo.anulado = 'S'

	UNION ALL

	SELECT 'I' tipo, 'RG' tipo_documento, rg.cod_rendicion_gastos numero, '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre de, '-' para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE rg.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		rg.importe_total total,
		ipoc.cod_caja, dbo.relativeDate(rg.fecha_baja,'today',0) fecha,
		rg.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		rg.anulado
	FROM rendicion_de_gastos rg
	INNER JOIN importe_por_operacion_c ipoc ON rg.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja
	WHERE rg.anulado = 'S'

	UNION ALL

	SELECT 'E' tipo, 'AS' tipo_documento, aps.nro_aporte_socio numero, s.nombre de, 'SPIRAL SHOES S.A.' para,
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
		aps.importe_total total, ipoc.cod_caja, dbo.relativeDate(aps.fecha_baja,'today',0) f
echa,
		aps.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		aps.anulado
	FROM aporte_socio aps
	LEFT OUTER JOIN socio s ON s.cod_socio = aps.cod_socio
	INNER JOIN importe_por_operacion_c ipoc ON aps.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE aps.anulado = 'S'

	UNION ALL

	SELECT 'I' tipo, 'RS' tipo_documento, aps.nro_retiro_socio numero, 'SPIRAL SHOES S.A.' de, s.nombre para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE aps.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
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
		aps.importe_total total, ipoc.cod_caja, dbo.relativeDate(aps.fecha_baja,'today',0) fecha,
		aps.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		aps.anulado
	FROM retiro_socio aps
	LEFT OUTER JOIN socio s ON s.cod_socio = aps.cod_socio
	INNER JOIN importe_por_operacion_c ipoc ON aps.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE aps.anulado = 'S'

	UNION ALL

	SELECT 'E' tipo, 'PRE' tipo_documento, r.nro_prestamo numero, cb.nombre_cuenta de, 'SPIRAL SHOES S.A.' para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE r.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		r.importe_total total, ipoc.cod_caja, dbo.relativeDate(r.fecha_baja,'today',0) fecha,
		r.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		r.anulado
	FROM prestamo r
	LEFT OUTER JOIN cuenta_bancaria cb ON cb.cod_cuenta_bancaria = r.cod_cuenta_bancaria
	INNER JOIN importe_por_operacion_c ipoc ON r.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE r.anulado = 'S'
GO
