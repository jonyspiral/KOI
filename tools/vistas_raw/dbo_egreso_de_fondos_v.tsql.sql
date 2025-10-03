CREATE VIEW [dbo].[egreso_de_fondos_v] AS
CREATE VIEW [dbo].[egreso_de_fondos_v] AS
	SELECT op.nro_orden_de_pago numero, op.empresa, (case when op.cod_proveedor is null then op.beneficiario else ('[' + cast(p.cod_prov AS VARCHAR) + '] ' + p.razon_social) end) de_para,
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

		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc4
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc4.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE op.cod_importe_operacion = ipoc4.cod_importe_operacion AND c1.cod_cuenta_bancaria IS NOT NULL) cheques_propios,

		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc5
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc5.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE op.cod_importe_operacion = ipoc5.cod_importe_operacion AND c1.cod_cuenta_bancaria IS NULL) cheques_terceros,

		(SELECT ISNULL(SUM(t1.importe), 0)
		FROM importe_por_operacion_c ipoc3
		INNER JOIN importe_por_operacion_d ipod3 ON ipoc3.cod_importe_operacion = ipod3.cod_importe_operacion AND ipod3.tipo_importe = 'T'
		INNER JOIN transferencia_bancaria_importe t1 ON ipod3.cod_importe = t1.cod_transferencia_ban
		WHERE op.cod_importe_operacion = ipoc3.cod_importe_operacion) transferencias,
		op.importe_total total, ipoc.cod_caja, op.fecha_documento fecha, p.imputacion_general, p.imputacion_especifica, pc.denominacion denom_especifica, pc.denominacion denom_general
	FROM orden_de_pago op
	LEFT OUTER JOIN proveedores_datos p ON p.cod_prov = op.cod_proveedor
	LEFT OUTER JOIN plan_cuentas pc ON pc.cuenta = p.imputacion_especifica
	INNER JOIN importe_por_operacion_c ipoc ON op.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE op.anulado = 'N'
GO
