CREATE VIEW [dbo].[subdiario_de_ingresos_v] AS
CREATE VIEW [dbo].[subdiario_de_ingresos_v] AS
	SELECT *, total - efectivo - transferencias - retenciones AS cheques FROM ( --Calculamos los cheques así para que la consulta sea más rápida, 10 segundos contra 13
		SELECT r.nro_recibo numero, r.empresa, (case when r.cod_cliente is null then r.recibido_de else ('[' + CAST(c.cod_cli AS VARCHAR)  + '] ' + c.razon_social) end) de_para, c.cod_vendedor,
			(SELECT ISNULL(SUM(e1.importe), 0)
			FROM importe_por_operacion_c ipoc1
			INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E' AND ipod1.anulado = 'N'
			INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
			WHERE r.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
			(SELECT ISNULL(SUM(r1.importe), 0)
			FROM importe_por_operacion_c ipoc4
			INNER JOIN importe_por_operacion_d ipod4 ON ipoc4.cod_importe_operacion = ipod4.cod_importe_operacion AND ipod4.tipo_importe = 'S' AND ipod4.anulado = 'N'
			INNER JOIN retencion_sufrida r1 ON ipod4.cod_importe = r1.cod_retencion
			WHERE r.cod_importe_operacion = ipoc4.cod_importe_operacion) retenciones,
			(SELECT ISNULL(SUM(t1.importe), 0)
			FROM importe_por_operacion_c ipoc3
			INNER JOIN importe_por_operacion_d ipod3 ON ipoc3.cod_importe_operacion = ipod3.cod_importe_operacion AND ipod3.tipo_importe = 'T' AND ipod3.anulado = 'N'
			INNER JOIN transferencia_bancaria_importe t1 ON ipod3.cod_importe = t1.cod_transferencia_ban
			WHERE r.cod_importe_operacion = ipoc3.cod_importe_operacion) transferencias,
			r.importe_total total, ipoc.cod_caja, r.fecha_documento fecha, r.cod_cliente, r.imputacion
		FROM recibo r
		LEFT OUTER JOIN clientes c ON c.cod_cliente = r.cod_cliente
		INNER JOIN importe_por_operacion_c ipoc ON r.cod_importe_operacion = ipoc.cod_importe_operacion
		WHERE r.anulado = 'N'
	) a

GO
