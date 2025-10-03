

CREATE VIEW [dbo].[importes_op_acumulado_mes] AS
	SELECT p.cod_prov cod_proveedor, SUM(ISNULL(op.importe_sujeto_ret, 0)) importe_acumulado_mes, SUM(ISNULL(re.importe_retenido, 0)) importe_retenido_mes
	FROM proveedores_datos p
		LEFT JOIN orden_de_