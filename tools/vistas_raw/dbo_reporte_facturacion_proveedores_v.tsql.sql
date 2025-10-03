CREATE VIEW [dbo].[reporte_facturacion_proveedores_v] AS
CREATE VIEW [dbo].[reporte_facturacion_proveedores_v] AS
	SELECT
		dpc.fecha, dpc.fecha_periodo_fiscal, dpc.tipo_docum, dpc.punto_venta, dpc.nro_documento, dpc.letra,
		p.cod_prov, p.razon_social, p.imputacion_general, pc.denominacion denominacion_imp_general,
		p.cuit, (CASE WHEN dpc.tipo_docum = 'NCR' THEN -1 ELSE 1 END) * dpc.neto_gravado neto_gravado,
		(CASE WHEN dpc.tipo_docum = 'NCR' THEN -1 ELSE 1 END) * dpc.neto_no_gravado neto_no_gravado,
		(SELECT (CASE WHEN dpc.tipo_docum = 'NCR' THEN -1 ELSE 1 END) * SUM(ISNULL(idp.importe, 0))
		FROM impuesto_por_documento_proveedor idp
		INNER JOIN impuesto i ON i.cod_impuesto = idp.cod_impuesto
		WHERE idp.cod_documento_proveedor = dpc.cod_documento_proveedor AND i.tipo = 1) iva,
		(SELECT (CASE WHEN dpc.tipo_docum = 'NCR' THEN -1 ELSE 1 END) * SUM(ISNULL(idp.importe, 0))
		FROM impuesto_por_documento_proveedor idp
		INNER JOIN impuesto i ON i.cod_impuesto = idp.cod_impuesto
		WHERE idp.cod_documento_proveedor = dpc.cod_documento_proveedor AND i.tipo = 3) percepcion_ganancias,
		(SELECT (CASE WHEN dpc.tipo_docum = 'NCR' THEN -1 ELSE 1 END) * SUM(ISNULL(idp.importe, 0))
		FROM impuesto_por_documento_proveedor idp
		INNER JOIN impuesto i ON i.cod_impuesto = idp.cod_impuesto
		WHERE idp.cod_documento_proveedor = dpc.cod_documento_proveedor AND i.tipo = 2) percepcion_iibb,
		dpc.factura_gastos, (CASE WHEN dpc.tipo_docum = 'NCR' THEN -1 ELSE 1 END) * dpc.importe_total importe_total, dpc.empresa
	FROM documento_proveedor_c dpc
	LEFT OUTER JOIN proveedores_datos p ON p.cod_prov = dpc.cod_proveedor
	LEFT OUTER JOIN plan_cuentas pc ON pc.cuenta = p.imputacion_general
	WHERE dpc.anulado = 'N'
GO
