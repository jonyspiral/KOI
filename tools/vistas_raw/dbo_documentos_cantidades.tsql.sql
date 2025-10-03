CREATE VIEW [dbo].[documentos_cantidades] AS

CREATE VIEW documentos_cantidades AS 
SELECT c.fecha fecha, c.cod_cliente cod_cliente, a.*
FROM (
	(
		SELECT
			empresa empresa, punto_venta punto_venta, tipo_docum tipo_docum, nro_documento nro_documento, letra letra_documento,
			NULL nro_remito, NULL letra, cod_almacen cod_almacen, cod_articulo cod_articulo, cod_color_articulo cod_color_articulo,
			
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(precio_unitario_final, 0) precio_unitario_final,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cantidad, 0) cantidad,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_1, 0) cant_1,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_2, 0) cant_2,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_3, 0) cant_3,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_4, 0) cant_4,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_5, 0) cant_5,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_6, 0) cant_6,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_7, 0) cant_7,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_8, 0) cant_8,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_9, 0) cant_9,
			(CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(cant_10, 0) cant_10
		FROM documentos_d
		WHERE
			cod_articulo IS NOT NULL AND
			(tipo_docum = 'FAC' OR tipo_docum = 'NCR')
	) UNION ALL (
		SELECT
			r.empresa empresa, r.punto_venta_factura punto_venta, r.tipo_docum_factura tipo_docum, r.nro_factura nro_documento, r.letra_factura letra_documento,
			r.nro_remito nro_remito, r.letra letra, dd.cod_almacen cod_almacen, dd.cod_articulo cod_articulo, dd.cod_color_articulo cod_color_articulo,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.precio_unitario_final, 0) precio_unitario_final,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cantidad, 0) cantidad,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_1, 0) cant_1,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_2, 0) cant_2,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_3, 0) cant_3,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_4, 0) cant_4,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_5, 0) cant_5,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_6, 0) cant_6,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_7, 0) cant_7,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_8, 0) cant_8,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_9, 0) cant_9,
			(CASE r.tipo_docum_factura WHEN 'NCR' THEN (-1) ELSE 1 END) * ISNULL(dd.cant_10, 0) cant_10
		FROM despachos_d dd
		INNER JOIN remitos_c r ON dd.empresa = r.empresa AND dd.nro_remito = r.nro_remito AND dd.letra_remito = r.letra
		WHERE
			dd.anulado = 'N' AND r.anulado = 'N' AND
			r.nro_factura IS NOT NULL AND r.punto_venta_factura IS NOT NULL AND r.tipo_docum_factura IS NOT NULL AND r.letra_factura IS NOT NULL
	)
) a
INNER JOIN documentos c ON c.empresa = a.empresa AND c.punto_venta = a.punto_venta AND c.tipo_docum = a.tipo_docum AND
							c.numero = a.nro_documento AND c.letra = a.letra_documento

GO
