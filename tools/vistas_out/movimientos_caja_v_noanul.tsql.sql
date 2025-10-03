

CREATE VIEW movimientos_caja_v_noanul AS

	SELECT 'I' tipo, 'REC' tipo_documento, r.nro_recibo numero, (case when r.cod_cliente is null then r.recibido_de else c.razon_social end) de, 'SPIRAL SHOES S.A.' para,
		(SELECT ISNULL(SUM(e1.importe), 0)
operacion = ipoc.cod_importe_operacion

	UNION ALL

	SELECT 'I' tipo, 'ICP' tipo_documento, icp.cod_ingreso_cheque_propio numero, '-' de, '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre para,
		0 efectivo,
		(SELECT ISNULL(SUM(c1.importe),' AND acc.cod_acreditar_debitar_cheque = ac2.cod_acreditar_debitar_cheque AND acc.empresa = ac2.empresa) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_opeSNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_e e1.cod_efectivo
		WHERE tbo.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		tbo.importe_total total, ipoc.cod_caja, dbo.relativeDate(tbo.fecha,'today',0) fecha,
		tbo.empresa empres)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 's'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE aps.cod_impacion) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		r.importe_total total, ipoc.cod_caja, dbo.relativeDate(r.fecha_documento,'today',0) fecha,
		r.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		r.anulado
	F