CREATE VIEW [dbo].[reporte_facturacion_v] AS

CREATE VIEW [dbo].[reporte_facturacion_v] AS
	SELECT
		c.empresa, c.fecha_documento fecha, c.tipo_docum tipo_documento,
		(CASE WHEN c.nro_comprobante IS NULL THEN c.nro_documento ELSE c.nro_comprobante END) numero,
		c.letra letra, c.cod_cliente, cli.razon_social, p.denom_provincia provincia, p.cod_provincia,
		SUM(CASE c.tipo_docum WHEN 'NDB' THEN 0 ELSE t.cantidad END) pares,
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * (ISNULL(c.importe_neto, 0) - ISNULL(c.importe_no_gravado, 0)) neto,
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(c.importe_no_gravado, 0) neto_ng,
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * (ISNULL(c.iva_importe_1, 0) + ISNULL(c.iva_importe_2, 0) + ISNULL(c.iva_importe_3, 0)) iva,
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * (ISNULL(c.descuento_comercial_importe, 0) + ISNULL(c.descuento_despacho_importe, 0)) descuento,
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * c.importe_total total
	FROM documentos_c c
	LEFT JOIN documentos_cantidades t ON c.empresa = t.empresa AND c.punto_venta = t.punto_venta AND c.tipo_docum = t.tipo_docum AND
						c.nro_documento = t.nro_documento AND c.letra = t.letra_documento
	LEFT JOIN clientes cli ON c.cod_cliente = cli.cod_cliente
	LEFT JOIN provincias p ON cli.cod_provincia = p.cod_provincia
	WHERE c.anulado = 'N'
	GROUP BY 
		c.empresa, c.fecha_documento, c.tipo_docum, c.nro_documento, c.letra, c.cod_cliente, cli.razon_social, p.denom_provincia, p.cod_provincia, c.nro_comprobante,
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * (ISNULL(c.importe_neto, 0) - ISNULL(c.importe_no_gravado, 0)),
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(c.importe_no_gravado, 0),
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * (ISNULL(c.iva_importe_1, 0) + ISNULL(c.iva_importe_2, 0) + ISNULL(c.iva_importe_3, 0)),
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * (ISNULL(c.descuento_comercial_importe, 0) + ISNULL(c.descuento_despacho_importe, 0)),
		(CASE c.tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * c.importe_total


GO
