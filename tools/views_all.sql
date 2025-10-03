locale is "en_US.UTF-8"
locale charset is "UTF-8"
using default charset "UTF-8"
1> 2> 1> 2> 3> 4> 5> 6> 7> 8> 9> 10> 11> 12> 13> 14> 15> 16> 17> 18> 19> 20> 21> 22> 23> X
---VIEW:dbo.ecommerce_orders_v---
CREATE VIEW [dbo].[ecommerce_orders_v] AS
CREATE VIEW ecommerce_orders_v AS

SELECT o.*, c.firstname, c.lastname, c.cod_usergroup cod_usergroup, u.nombre nombre_usergroup
FROM ecommerce_orders o
LEFT JOIN ecommerce_customers c ON o.cod_customer = c.cod_customer
LEFT JOIN ecommerce_usergroups u ON c.cod_usergroup = u.cod_usergroup
WHERE o.anulado = 'N'
GO
---VIEW:demian.view_prueba_v---
CREATE VIEW [demian].[view_prueba_v] AS

CREATE VIEW view_prueba_v AS
SELECT     *
                       FROM          mp_mov_extraor_vw
                       UNION ALL
                       SELECT     *
                       FROM         mp_remitos_vw
                       UNION ALL
                       SELECT     *
                       FROM         tranferencias_materias_primas_v
GO
---VIEW:dbo.stock_mp---
CREATE VIEW [dbo].[stock_mp] AS
CREATE VIEW dbo.stock_mp
AS
SELECT     TOP 100 PERCENT dbo.Materias_primas.cod_material, dbo.materiales.denom_material, dbo.Materias_primas.cod_color, a.cod_almacen, SUM(a.cant) 
                      AS cant, SUM(a.c1) AS c1, SUM(a.c2) AS c2, SUM(a.c3) AS c3, SUM(a.c4) AS c4, SUM(a.c5) AS c5, SUM(a.c6) AS c6, SUM(a.c7) AS c7, SUM(a.c8) AS c8, 
                      SUM(a.c9) AS c9, SUM(a.c10) AS c10, dbo.rubros_materias_primas.denom_rubro, dbo.rango_talles.posic_1, 
                      dbo.Materias_primas.precio_unitario / dbo.materiales.factor_conversion AS precio_unitario, SUM(ISNULL(a.cant, 0) 
                      * ISNULL(dbo.Materias_primas.precio_unitario, 0) / dbo.materiales.factor_conversion) AS valor, dbo.materiales.unidad_medida AS UMS, 
                      dbo.materiales.factor_conversion
FROM         dbo.Materias_primas INNER JOIN
                      dbo.materiales ON dbo.Materias_primas.cod_material = dbo.materiales.cod_material LEFT OUTER JOIN
                          (SELECT     cod_material, cod_color, cant, c1, c2, c3, c4, c5, c6, c7, c8, c9, c10, cod_almacen
                            FROM          (SELECT     cod_material, cod_color, ISNULL(cantidad, 0) AS cant, ISNULL(c1, 0) AS c1, ISNULL(c2, 0) AS c2, ISNULL(c3, 0) AS c3, 
                                                                           ISNULL(c4, 0) AS c4, ISNULL(c5, 0) AS c5, ISNULL(c6, 0) AS c6, ISNULL(c7, 0) AS c7, ISNULL(c8, 0) AS c8, ISNULL(c9, 0) AS c9, 
                                                                           ISNULL(c10, 0) AS c10, cod_almacen
                                                    FROM          dbo.mp_mov_extraor_vw
                                                    UNION ALL
                                                    SELECT     cod_material, cod_color, cantidad AS cant, c1, c2, c3, c4, c5, c6, c7, c8, c9, c10, cod_almacen
                                                    FROM         dbo.mp_remitos_vw
                                                    UNION ALL
                                                    SELECT     cod_material, cod_color, ISNULL(cantidad, 0) AS cant, ISNULL(cant_1, 0) AS c1, ISNULL(cant_2, 0) AS c2, ISNULL(cant_3, 0) 
                                                                          AS c3, ISNULL(cant_4, 0) AS c4, ISNULL(cant_5, 0) AS c5, ISNULL(cant_6, 0) AS c6, ISNULL(cant_7, 0) AS c7, ISNULL(cant_8, 0) 
                                                                          AS c8, ISNULL(cant_9, 0) AS c9, ISNULL(cant_10, 0) AS c10, cod_almacen
                                                    FROM         dbo.tranferencias_materias_primas_v) conconsumo
                            WHERE      (cod_almacen <> '16')) a ON dbo.Materias_primas.cod_material = a.cod_material COLLATE Modern_Spanish_CI_AS AND 
                      dbo.Materias_primas.cod_color = a.cod_color COLLATE Modern_Spanish_CI_AS LEFT OUTER JOIN
                      dbo.rubros_materias_primas ON dbo.materiales.cod_rubro = dbo.rubros_materias_primas.cod_rubro LEFT OUTER JOIN
                      dbo.rango_talles ON dbo.materiales.cod_rango = dbo.rango_talles.cod_rango
WHERE     (dbo.Materias_primas.anulado = 'N') AND (dbo.materiales.anulado = 'N')
GROUP BY dbo.Materias_primas.cod_material, dbo.Materias_primas.cod_color, a.cod_almacen, dbo.materiales.denom_material, 
                      dbo.rubros_materias_primas.denom_rubro, dbo.rango_talles.posic_1, dbo.materiales.unidad_medida, 
                      dbo.Materias_primas.precio_unitario / dbo.materiales.factor_conversion, dbo.materiales.factor_conversion
ORDER BY dbo.materiales.denom_material, dbo.Materias_primas.cod_color, a.cod_almacen

GO
---VIEW:dbo.stock_mp_detalle---
CREATE VIEW [dbo].[stock_mp_detalle] AS
CREATE VIEW dbo.stock_mp_detalle
AS
SELECT     TOP 100 PERCENT fecha_movimiento, cod_material, cod_color, cantidad, c1, c2, c3, c4, c5, c6, c7, c8, c9, c10, cod_almacen, Motivo, nro_operacion, 
                      efecto_movimiento
FROM         (SELECT     *
                       FROM          mp_mov_extraor_vw
                       UNION ALL
                       SELECT     *
                       FROM         mp_remitos_vw
                       UNION ALL
                       SELECT     *
                       FROM         tranferencias_materias_primas_v) smp
ORDER BY fecha_movimiento

GO
---VIEW:dbo.movimientos_stock_v---
CREATE VIEW [dbo].[movimientos_stock_v] AS

CREATE VIEW movimientos_stock_v AS
SELECT m.*, a.denom_articulo nombre_articulo FROM movimientos_stock m
LEFT JOIN articulos a ON m.cod_articulo = a.cod_articulo

GO
---VIEW:dbo.stock_mp_sin_rango_ a_eliminar---
CREATE VIEW [dbo].[stock_mp_sin_rango_ a_eliminar] AS
CREATE VIEW [dbo].[stock_mp_sin_rango] AS 
SELECT TOP 100 PERCENT cod_material, cod_color, SUM(cant) AS cant FROM 
(SELECT dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material AS cod_color, SUM(- dbo.materias_primas_movim_extraor.cantidad) AS cant
 FROM dbo.materias_primas_movim_extraor INNER JOIN dbo.materiales ON dbo.materias_primas_movim_extraor.cod_material = dbo.materiales.cod_material
 WHERE (dbo.materias_primas_movim_extraor.efecto_movimiento = 'S') AND (dbo.materiales.anulado = 'N')   
GROUP BY dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material 

UNION
 SELECT dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material AS cod_color, SUM(dbo.materias_primas_movim_extraor.cantidad) AS cant
 FROM dbo.materias_primas_movim_extraor INNER JOIN dbo.materiales ON dbo.materias_primas_movim_extraor.cod_material = dbo.materiales.cod_material
 WHERE (dbo.materias_primas_movim_extraor.efecto_movimiento = 'e') AND (dbo.materiales.anulado = 'N') 
GROUP BY dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material 

UNION
 SELECT remitos_proveedor_detalle.cod_material, remitos_proveedor_detalle.cod_color, SUM([cantidad] * [factor_conversion]) AS Cant 
FROM (Remitos_proveedor_cabecera INNER JOIN remitos_proveedor_detalle ON (Remitos_proveedor_cabecera.cod_proveedor = remitos_proveedor_detalle.cod_proveedor) AND (Remitos_proveedor_cabecera.nro_compuesto_remito = remitos_proveedor_detalle.nro_compuesto_remito)) INNER JOIN materiales ON remitos_proveedor_detalle.cod_material = materiales.cod_material
 WHERE (dbo.materiales.anulado = 'N') 
GROUP BY remitos_proveedor_detalle.cod_material, remitos_proveedor_detalle.cod_color 

UNION 
SELECT 
Consumos_tarea.cod_material, Consumos_tarea.cod_color,sum(- cant_consumo) AS cant 
FROM Consumos_tarea  
  INNER JOIN materiales ON Consumos_tarea.cod_material = materiales.cod_material 
WHERE (dbo.materiales.anulado = 'N')
group by dbo.Consumos_tarea.cod_material, Consumos_tarea.cod_color 

 UNION 
SELECT Stock_mp_fc.COD_MATERIAL, Stock_mp_fc.cod_color, SUM(- cantidad) AS cant
 FROM Stock_mp_fc INNER JOIN materiales ON Stock_mp_fc.cod_material = materiales.cod_material WHERE (dbo.materiales.anulado = 'N') 
GROUP BY Stock_mp_fc.COD_MATERIAL, Stock_mp_fc.cod_color) a GROUP BY cod_material, cod_color ORDER BY cod_material, cod_color
GO
---VIEW:dbo.ordenes_compra_cabecera_v---
CREATE VIEW [dbo].[ordenes_compra_cabecera_v] AS

CREATE VIEW ordenes_compra_cabecera_v AS
	SELECT		c.*, (SELECT SUM(cantidad_pendiente) FROM ordenes_compra_detalle d WHERE c.cod_orden_de_compra = d.cod_orden_de_compra) pendiente
	FROM		ordenes_compra_cabecera c
GO
---VIEW:dbo.indicadores_por_rol_v---
CREATE VIEW [dbo].[indicadores_por_rol_v] AS

CREATE VIEW indicadores_por_rol_v AS
	SELECT TOP 100 PERCENT ixr.cod_indicador, i.nombre nombre_indicador, r.*
	FROM indicadores_por_rol ixr
	INNER JOIN roles r ON ixr.cod_rol = r.cod_rol
	INNER JOIN indicadores i ON i.cod_indicador = ixr.cod_indicador
	ORDER BY nombre_indicador ASC, ixr.cod_indicador ASC


GO
---VIEW:dbo.stock_mp_rango---
CREATE VIEW [dbo].[stock_mp_rango] AS
CREATE VIEW stock_mp_rango AS SELECT TOP 100 PERCENT cod_material, cod_color, SUM(cant) AS cant, SUM(c1) AS c1, SUM(c2) AS c2, SUM(c3) AS c3, SUM(c4) AS c4, SUM(c5) AS c5, SUM(c6) AS c6, SUM(c7) AS c7, SUM(c8) AS c8, SUM(c9) AS c9, SUM(c10) AS c10 FROM (SELECT dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material AS cod_color, SUM(- dbo.materias_primas_movim_extraor.cantidad) AS cant, SUM(- dbo.materias_primas_movim_extraor.cant_1) AS c1, SUM(- dbo.materias_primas_movim_extraor.cant_2) AS c2, SUM(- dbo.materias_primas_movim_extraor.cant_3) AS c3, SUM(- dbo.materias_primas_movim_extraor.cant_4) AS c4, SUM(- dbo.materias_primas_movim_extraor.cant_5) AS c5, SUM(- dbo.materias_primas_movim_extraor.cant_6) AS c6, SUM(- dbo.materias_primas_movim_extraor.cant_7) AS c7, SUM(- dbo.materias_primas_movim_extraor.cant_8) AS c8, SUM(- dbo.materias_primas_movim_extraor.cant_9) AS c9, SUM(- dbo.materias_primas_movim_extraor.cant_10) AS c10 FROM dbo.materias_primas_movim_extraor INNER JOIN dbo.materiales ON dbo.materias_primas_movim_extraor.cod_material = dbo.materiales.cod_material WHERE (dbo.materias_primas_movim_extraor.efecto_movimiento = 'S') AND (dbo.materiales.anulado = 'N') AND (dbo.materiales.cod_rango IS NOT NULL) GROUP BY dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material UNION SELECT dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material AS cod_color, SUM(dbo.materias_primas_movim_extraor.cantidad) AS cant, SUM(dbo.materias_primas_movim_extraor.cant_1) AS c1, SUM(dbo.materias_primas_movim_extraor.cant_2) AS c2, SUM(dbo.materias_primas_movim_extraor.cant_3) AS c3, SUM(dbo.materias_primas_movim_extraor.cant_4) AS c4, SUM(dbo.materias_primas_movim_extraor.cant_5) AS c5, SUM(dbo.materias_primas_movim_extraor.cant_6) AS c6, SUM(dbo.materias_primas_movim_extraor.cant_7) AS c7, SUM(dbo.materias_primas_movim_extraor.cant_8) AS c8, SUM(dbo.materias_primas_movim_extraor.cant_9) AS c9, SUM(dbo.materias_primas_movim_extraor.cant_10) AS c10 FROM dbo.materias_primas_movim_extraor INNER JOIN dbo.materiales ON dbo.materias_primas_movim_extraor.cod_material = dbo.materiales.cod_material WHERE (dbo.materias_primas_movim_extraor.efecto_movimiento = 'e') AND (dbo.materiales.anulado = 'N') AND (dbo.materiales.cod_rango IS NOT NULL) GROUP BY dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material UNION SELECT remitos_proveedor_detalle.cod_material, remitos_proveedor_detalle.cod_color, SUM([cantidad] * [factor_conversion]) AS Cant, SUM(cant_1 * [factor_conversion]) AS c1, SUM(cant_2 * [factor_conversion]) AS c2, SUM(cant_3 * [factor_conversion]) AS c3, SUM(cant_4 * [factor_conversion]) AS c4, SUM(cant_5 * [factor_conversion]) AS c5, SUM(cant_6 * [factor_conversion]) AS c6, SUM(cant_7 * [factor_conversion]) AS c7, SUM(cant_8 * [factor_conversion]) AS c8, SUM(cant_9 * [factor_conversion]) AS c9, SUM(cant_10 * [factor_conversion]) AS c10 FROM (Remitos_proveedor_cabecera INNER JOIN remitos_proveedor_detalle ON (Remitos_proveedor_cabecera.cod_proveedor = remitos_proveedor_detalle.cod_proveedor) AND (Remitos_proveedor_cabecera.nro_compuesto_remito = remitos_proveedor_detalle.nro_compuesto_remito)) INNER JOIN materiales ON remitos_proveedor_detalle.cod_material = materiales.cod_material WHERE (dbo.materiales.anulado = 'N') AND (dbo.materiales.cod_rango IS NOT NULL) GROUP BY remitos_proveedor_detalle.cod_material, remitos_proveedor_detalle.cod_color UNION SELECT Consumos_tarea.cod_material, Consumos_tarea.cod_color, SUM(- [tareas_detalle].[cantidad_salida]) AS cant, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_1) ELSE 0 END) AS c1, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_2) ELSE 0 END) AS c2, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_3) ELSE 0 END) AS c3, SUM(CASE WHEN materiales.
cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_4) ELSE 0 END) AS c4, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_5) ELSE 0 END) AS c5, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_6) ELSE 0 END) AS c6, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_7) ELSE 0 END) AS c7, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_8) ELSE 0 END) AS c8, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_9) ELSE 0 END) AS c9, SUM(CASE WHEN materiales.cod_rango IS NOT NULL THEN (- Consumos_tarea.cant_10) ELSE 0 END) AS c10 FROM Consumos_tarea INNER JOIN Tareas_detalle ON (consumos_tarea.nro_orden_fabricacion = Tareas_detalle.nro_orden_fabricacion) AND (Consumos_tarea.nro_tarea = Tareas_detalle.nro_tarea) AND (Consumos_tarea.cod_seccion = Tareas_detalle.cod_seccion) INNER JOIN materiales ON Consumos_tarea.cod_material = materiales.cod_material WHERE (dbo.materiales.anulado = 'N') AND (dbo.materiales.cod_rango IS NOT NULL) GROUP BY Consumos_tarea.cod_material, Consumos_tarea.cod_color UNION SELECT Stock_mp_fc.COD_MATERIAL, Stock_mp_fc.cod_color, SUM(- cantidad) AS cant, SUM(- cant_1) AS c1, SUM(- cant_2) AS c2, SUM(- cant_3) AS c3, SUM(- cant_4) AS c4, SUM(- cant_5) AS c5, SUM(- cant_6) AS c6, SUM(- cant_7) AS c7, SUM(- cant_8) AS c8, SUM(- cant_9) AS c9, SUM(- cant_10) AS c10 FROM Stock_mp_fc INNER JOIN materiales ON Stock_mp_fc.cod_material = materiales.cod_material WHERE (dbo.materiales.anulado = 'N') AND (dbo.materiales.cod_rango IS NOT NULL) GROUP BY Stock_mp_fc.COD_MATERIAL, Stock_mp_fc.cod_color) AS a GROUP BY a.cod_material, a.cod_color ORDER BY a.cod_material, a.cod_color
GO
---VIEW:dbo.listado_clientes_v---
CREATE VIEW [dbo].[listado_clientes_v] AS
CREATE VIEW listado_clientes_v AS
	SELECT	c.cod_cli cod_cliente, c.razon_social, c.denom_fantasia, c.cuit, c.cod_vendedor,
			sc.telefono_1, sc.telefono_2, c.email email_cliente, sc.email email_sucursal,
			pa.cod_pais, pr.cod_provincia, pr.denom_provincia, l.cod_localidad, l.denom_localidad,
			sc.calle, sc.numero, sc.piso, sc.oficina_depto
	FROM	clientes c
			LEFT OUTER JOIN sucursales_clientes sc ON c.cod_casa_central = sc.cod_suc AND c.cod_cli = sc.cod_cli
			LEFT OUTER JOIN paises pa ON pa.cod_pais = sc.cod_pais
			LEFT OUTER JOIN provincias pr ON pr.cod_provincia = sc.cod_provincia
			LEFT OUTER JOIN localidades l ON l.cod_localidad = sc.cod_localidad
	WHERE	c.anulado = 'N'
GO
---VIEW:dbo.documento_proveedor---
CREATE VIEW [dbo].[documento_proveedor] AS

CREATE VIEW [dbo].[documento_proveedor] AS
	SELECT
			/* campos de DocumentoProveedor */
			id, empresa, punto_venta, tipo_docum, nro_documento, letra, cod_proveedor, operacion_tipo, fecha,
			neto_gravado, neto_no_gravado, importe_total, importe_pendiente, condicion_plazo_pago, factura_gastos, fecha_vencimiento, fecha_periodo_fiscal,
			CAST(observaciones AS VARCHAR(8000)) observaciones, documento_en_conflicto, cod_usuario, anulado, fecha_alta, fecha_baja, fecha_ultima_mod,
			/* campos de OP */
			cod_importe_operacion, imputacion, importe_sujeto_ret, beneficiario, retiene_ganancias
	FROM
	(
		SELECT
			/* campos de DocumentoProveedor */
			cod_documento_proveedor id, empresa, punto_venta, tipo_docum, nro_documento, letra, cod_proveedor, operacion_tipo, fecha,
			neto_gravado, neto_no_gravado, importe_total, importe_pendiente, condicion_plazo_pago, factura_gastos, fecha_vencimiento, fecha_periodo_fiscal,
			CAST(observaciones AS VARCHAR(8000)) observaciones, documento_en_conflicto, cod_usuario, anulado, fecha_alta, fecha_baja, fecha_ultima_mod,
			/* campos de OP */
			NULL cod_importe_operacion, NULL imputacion, NULL importe_sujeto_ret, NULL beneficiario, NULL retiene_ganancias
		FROM documento_proveedor_c
		WHERE anulado = 'N'
		UNION ALL
		SELECT
			nro_orden_de_pago id, empresa, '1' punto_venta, 'OP' tipo_docum, nro_orden_de_pago nro_documento, 'P' letra, cod_proveedor, operacion_tipo, fecha_documento fecha,
			NULL neto_gravado, NULL neto_no_gravado, importe_total, importe_pendiente, NULL condicion_plazo_pago, 'N' AS factura_gastos, NULL fecha_vencimiento, NULL fecha_periodo_fiscal,
			CAST(observaciones AS VARCHAR(8000)) observaciones, NULL documento_en_conflicto, cod_usuario, anulado, fecha_alta, fecha_baja, fecha_ultima_mod,
			/* campos de OP */
			cod_importe_operacion, imputacion, importe_sujeto_ret, beneficiario, retiene_ganancias
		FROM orden_de_pago
		WHERE anulado = 'N' AND nro_orden_de_pago > 0
		UNION ALL
		SELECT
			cod_rendicion_gastos id, empresa, '1' punto_venta, 'REN' tipo_docum, cod_rendicion_gastos nro_documento, 'R' letra, NULL cod_proveedor, 'RE' operacion_tipo, fecha_documento fecha,
			NULL neto_gravado, NULL neto_no_gravado, importe_total, importe_pendiente, NULL condicion_plazo_pago, 'S' AS factura_gastos, NULL fecha_vencimiento, NULL fecha_periodo_fiscal,
			CAST(observaciones AS VARCHAR(8000)) observaciones, NULL documento_en_conflicto, cod_usuario, anulado, fecha_alta, fecha_baja, fecha_ultima_mod,
			/* campos de REN */
			cod_importe_operacion, NULL imputacion, NULL importe_sujeto_ret, NULL beneficiario, NULL retiene_ganancias
		FROM rendicion_de_gastos
		WHERE anulado = 'N' AND cod_rendicion_gastos > 0
	) a



GO
---VIEW:dbo.reporte_facturacion_v---
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
---VIEW:dbo.reporte_articulos_v---
CREATE VIEW [dbo].[reporte_articulos_v] AS
CREATE VIEW [dbo].[reporte_articulos_v] AS
SELECT     dbo.documentos_c.cod_cliente, dbo.Clientes.razon_social, dbo.articulos.denom_articulo, dbo.documentos_cantidades.cod_articulo, 
                      dbo.documentos_cantidades.cod_color_articulo, dbo.documentos_cantidades.cantidad AS pares, dbo.documentos_cantidades.precio_unitario_final, 
                      dbo.documentos_c.fecha_documento AS fecha, dbo.documentos_c.empresa
FROM         dbo.documentos_cantidades LEFT OUTER JOIN
                      dbo.articulos ON dbo.documentos_cantidades.cod_articulo = dbo.articulos.cod_articulo LEFT OUTER JOIN
                      dbo.documentos_c ON dbo.documentos_c.empresa = dbo.documentos_cantidades.empresa AND 
                      dbo.documentos_c.punto_venta = dbo.documentos_cantidades.punto_venta AND dbo.documentos_c.tipo_docum = dbo.documentos_cantidades.tipo_docum AND 
                      dbo.documentos_c.nro_documento = dbo.documentos_cantidades.nro_documento AND 
                      dbo.documentos_c.letra = dbo.documentos_cantidades.letra_documento LEFT OUTER JOIN
                      dbo.Clientes ON dbo.documentos_c.cod_cliente = dbo.Clientes.cod_cliente
WHERE		dbo.documentos_c.anulado = 'N'
GO
---VIEW:dbo.materias_primas_v---
CREATE VIEW [dbo].[materias_primas_v] AS

CREATE VIEW materias_primas_v AS
	SELECT mp.*, cmp.denom_color, cmp.abrev_color
	FROM Materias_primas mp
	INNER JOIN Colores_materias_primas cmp ON mp.cod_color = cmp.cod_color
GO
---VIEW:dbo.usuarios_por_almacen_v---
CREATE VIEW [dbo].[usuarios_por_almacen_v] AS



--Es porque la clase UsuarioPorAlmacen hereda de Usuario y necesita todos sus campos para el fill

CREATE VIEW usuarios_por_almacen_v AS
SELECT a.cod_almacen, b.*
FROM usuarios_por_almacen a
INNER JOIN users b ON a.cod_usuario = b.cod_usuario
GO
---VIEW:dbo.stock14_por_talle_v---
CREATE VIEW [dbo].[stock14_por_talle_v] AS
CREATE VIEW dbo.stock14_por_talle_v
AS
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_2 AS Talle, 
                      ISNULL(stock14.cant_2, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_2
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_3 AS Talle, 
                      ISNULL(stock14.cant_3, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_3
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_4 AS Talle, 
                      ISNULL(stock14.cant_4, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_4
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION 
ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_5 AS Talle, 
                      ISNULL(stock14.cant_5, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_5
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_6 AS Talle, 
                      ISNULL(stock14.cant_6, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_6
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_7 AS Talle, 
                      ISNULL(stock14.cant_7, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_7
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_8 AS Talle, 
                      ISNULL(stock14.cant_8, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_8
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo

GO
---VIEW:dbo.compras_pendientes---
CREATE VIEW [dbo].[compras_pendientes] AS
CREATE VIEW dbo.compras_pendientes
AS
SELECT     TOP 100 PERCENT dbo.Ordenes_compra_detalle.cod_material, dbo.Ordenes_compra_detalle.cod_color, 
                      SUM(dbo.Ordenes_compra_detalle.cantidad_pendiente) AS pendiente
FROM         dbo.Ordenes_compra_detalle INNER JOIN
                      dbo.Ordenes_compra_cabecera ON 
                      dbo.Ordenes_compra_detalle.cod_orden_de_compra = dbo.Ordenes_compra_cabecera.cod_orden_de_compra
WHERE     (dbo.Ordenes_compra_detalle.cantidad_pendiente > 0.001) AND (dbo.Ordenes_compra_cabecera.anulado = 'N')
GROUP BY dbo.Ordenes_compra_detalle.cod_material, dbo.Ordenes_compra_detalle.cod_color, dbo.Ordenes_compra_cabecera.es_hexagono
HAVING      (dbo.Ordenes_compra_cabecera.es_hexagono = 'N')
ORDER BY dbo.Ordenes_compra_detalle.cod_material, dbo.Ordenes_compra_detalle.cod_color

GO
---VIEW:dbo.pedidos_d_v---
CREATE VIEW [dbo].[pedidos_d_v] AS
CREATE VIEW dbo.pedidos_d_v
AS
SELECT     a.*, b.cod_cliente AS cod_cliente, cl.cod_vendedor AS cod_vendedor, b.aprobado AS aprobado, b.fecha_alta AS fecha_pedido, 
                      p.predespachados AS predespachados, p.pred_1 AS pred_1, p.pred_2 AS pred_2, p.pred_3 AS pred_3, p.pred_4 AS pred_4, p.pred_5 AS pred_5, 
                      p.pred_6 AS pred_6, p.pred_7 AS pred_7, p.pred_8 AS pred_8, p.pred_9 AS pred_9, p.pred_10 AS pred_10, p.tickeados AS tickeados, 
                      p.tick_1 AS Expr12, p.tick_2 AS Expr13, p.tick_3 AS Expr14, p.tick_4 AS Expr15, p.tick_5 AS Expr16, p.tick_6 AS Expr17, p.tick_7 AS Expr18, 
                      p.tick_8 AS Expr19, p.tick_9 AS Expr20, p.tick_10 AS Expr21, c.id_tipo_producto_stock AS Expr22, b.id_estado_pedido AS Expr1
FROM         dbo.pedidos_d a LEFT OUTER JOIN
                      dbo.predespachos p ON a.empresa = p.empresa AND a.nro_pedido = p.nro_pedido AND a.nro_item = p.nro_item INNER JOIN
                      dbo.pedidos_c b ON a.nro_pedido = b.nro_pedido INNER JOIN
                      dbo.Clientes cl ON b.cod_cliente = cl.cod_cli INNER JOIN
                      dbo.colores_por_articulo c ON a.cod_articulo = c.cod_articulo AND a.cod_color_articulo = c.cod_color_articulo
WHERE     (b.id_estado_pedido IS NULL) OR
                      (b.id_estado_pedido = 1) OR
                      (b.id_estado_pedido = 2)

GO
---VIEW:dbo.koi_ticket_v---
CREATE VIEW [dbo].[koi_ticket_v] AS

CREATE VIEW koi_ticket_v AS
	SELECT TOP 100 PERCENT * FROM (
		-- 1. Los que tienen fecha de resolución (con o sin responsable)
		SELECT *, 1 orden1, fecha_estimada_resolucion orden2, NULL orden3 FROM koi_ticket
		WHERE anulado = 'N' AND fecha_estimada_resolucion IS NOT NULL AND fecha_cierre IS NULL
	UNION ALL
		-- 2. Los que tienen sólo responsable
		SELECT *, 2 orden1, fecha_estimada_resolucion orden2, NULL orden3 FROM koi_ticket
		WHERE anulado = 'N' AND fecha_estimada_resolucion IS NULL AND cod_responsable IS NOT NULL AND fecha_cierre IS NULL
	UNION ALL
		-- 3. Los que tienen no tienen fecha de resolución ni responsable
		SELECT *, 3 orden1, NULL orden2, prioridad orden3 FROM koi_ticket
		WHERE anulado = 'N' AND (fecha_estimada_resolucion IS NULL AND cod_responsable IS NULL) AND fecha_cierre IS NULL
	UNION ALL
		-- 4. Los tickets cerrados
		SELECT *, 4 orden1, NULL orden2, fecha_cierre orden3 FROM koi_ticket
		WHERE anulado = 'N' AND fecha_cierre IS NOT NULL
	) a
	ORDER BY orden1 ASC, orden2 ASC, orden3 DESC
GO
---VIEW:dbo.remitos_c_v---
CREATE VIEW [dbo].[remitos_c_v] AS
CREATE VIEW remitos_c_v AS 
SELECT
	r.*,
	c.razon_social
FROM
	remitos_c r
INNER JOIN clientes c ON c.cod_cli = r.cod_cliente

GO
---VIEW:dbo.proveedor_v---
CREATE VIEW [dbo].[proveedor_v] AS


CREATE VIEW [dbo].[proveedor_v] AS
	SELECT p.cod_prov, p.tipo_proveedor, p.fecha_baja, p.vivo, p.razon_social, p.denom_fantasia, p.cuit, p.condicion_iva, p.rubro, p.imputacion_en_compra, p.calle, p.numero, 
		p.piso, p.oficina_depto, p.cod_postal, p.localidad, p.partido_departamento, p.provincia, p.pais, p.provincia_vieja, p.PAIS_viejo, p.telefono_1, p.telefono_2, p.fax, 
		p.e_mail, p.limite_credito, p.cuit_viejo, p.iva_viejo, p.vta_viejo, p.plazo_pago, p.vendedor, p.retener_imp_ganancias, p.concepto_reten_ganancias, p.retener_iva, 
		p.retener_ingr_brutos, p.jurisd_1_ingr_brutos, p.jurisd_2_ingr_brutos, p.retencion_especial, p.cuenta_acumuladora, p.denominacion_cta_acum, 
		p.FECHA_ULTima_modificacion, p.autor_ultima_modificacion, p.anulado, p.margen, p.pagina_web, p.horarios_atencion, p.persona_en_fca, p.lista_precios_imprime, 
		p.NombreContacto, p.CargoContacto, p.NumTeléfono3, p.NumCelular, p.Notas, p.DireccionComercial, p.plazo_pago_primera_entrega, p.primera_entrega, 
		p.codigo_sist_anterior, p.r_social_en_doc, p.cod_transporte, p.lugar_de_retiro, p.horario_de_retiro, p.observaciones, p.plazo_pago_real, p.comentario, 
		p.cod_localidad, p.cod_localidad_nro, ISNULL(ia.importe_acumulado_mes, 0) importe_acumulado_mes, ISNULL(ia.importe_retenido_mes, 0) importe_retenido_mes,
		p.imputacion_general,p.imputacion_especifica, p.cod_imputacion_haber, p.autorizado,
		gp1.saldo saldo_1, gp2.saldo saldo_2, (gp1.saldo + gp2.saldo) saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN dbo.importes_op_acumulado_mes AS ia ON p.cod_prov = ia.cod_proveedor
	LEFT JOIN gestion_proveedores_1 AS gp1 ON p.cod_prov = gp1.cod_prov
	LEFT JOIN gestion_proveedores_2 AS gp2 ON p.cod_prov = gp2.cod_prov



GO
---VIEW:dbo.cheques_propios_v---
CREATE VIEW [dbo].[cheques_propios_v] AS
CREATE VIEW cheques_propios_v AS
	SELECT		c.cod_cheque, c.empresa, c.numero, banco.nombre AS banco_nombre, c.importe, c.fecha_vencimiento, p.cod_prov, p.razon_social, 
				c.librador_nombre, c.dias_vencimiento AS dias, c.esperando_en_banco, c.fecha_credito_debito, c.concluido
	FROM		dbo.cheque_v AS c
				LEFT OUTER JOIN dbo.proveedores_datos AS p ON c.cod_proveedor = p.cod_prov
				INNER JOIN dbo.banco AS banco ON banco.cod_banco = c.cod_banco
	WHERE		c.cod_cuenta_bancaria IS NOT NULL AND (c.concluido = 'N' OR
				(c.concluido = 'S' AND c.esperando_en_banco = 'D' AND c.fecha_credito_debito IS NULL)) AND
				c.cod_rechazo_cheque IS NULL AND c.anulado = 'N'
GO
---VIEW:dbo.clientes_v---
CREATE VIEW [dbo].[clientes_v] AS



CREATE VIEW clientes_v AS
	SELECT (o.nombres + ' ' + o.apellido) nombre_vendedor, debe.fecha_debe, debe.importe_pendiente_debe, haber.fecha_haber, haber.importe_pendiente_haber, a.saldo,
			plazos.dias_promedio_pago, ISNULL(total_cheques.total_cheques, 0) total_cheques, ISNULL(pagos_ingresados_mes.pagos_ingresados_mes, 0) pagos_ingresados_mes, c.*
	FROM clientes c
	LEFT JOIN operadores_v o ON c.cod_vendedor = o.cod_operador
	LEFT JOIN (
		SELECT c.cod_cli, ISNULL(SUM((CASE WHEN d.tipo_docum = 'NDB' OR d.tipo_docum = 'FAC' THEN 1 ELSE -1 END) * d.importe_pendiente), 0) saldo
		FROM Clientes c
		LEFT JOIN documentos d ON c.cod_cli = d.cod_cliente AND d.importe_pendiente > 0
		GROUP BY c.cod_cli
	) a ON a.cod_cli = c.cod_cli
	LEFT JOIN (
		SELECT a.cod_cliente, debe.fecha fecha_debe, SUM(a.importe_pendiente) importe_pendiente_debe
		FROM documentos a
		INNER JOIN (
			SELECT cod_cliente, MIN(fecha) fecha
			FROM documentos
			WHERE importe_pendiente > 0 AND (tipo_docum = 'FAC' OR tipo_docum = 'NDB')
			GROUP BY cod_cliente
		) debe ON a.cod_cliente = debe.cod_cliente AND a.fecha = debe.fecha
		WHERE a.cod_cliente > 0
		GROUP BY a.cod_cliente, debe.fecha
	) debe ON c.cod_cli = debe.cod_cliente
	LEFT JOIN (
		SELECT a.cod_cliente, haber.fecha fecha_haber, SUM(a.importe_pendiente) importe_pendiente_haber
		FROM documentos a
		INNER JOIN (
			SELECT cod_cliente, MIN(fecha) fecha
			FROM documentos
			WHERE importe_pendiente > 0 AND (tipo_docum = 'NCR' OR tipo_docum = 'REC')
			GROUP BY cod_cliente
		) haber ON a.cod_cliente = haber.cod_cliente AND a.fecha = haber.fecha
		WHERE a.cod_cliente > 0
		GROUP BY a.cod_cliente, haber.fecha
	) haber ON c.cod_cli = haber.cod_cliente
	LEFT JOIN (
		SELECT cod_cliente, AVG(ISNULL(dias_promedio_pago, 0)) dias_promedio_pago
		FROM documentos a
		WHERE fecha >= dbo.relativeDate(GETDATE(), 'first', -6) AND fecha > dbo.toDate('01/09/2013') AND dias_promedio_pago IS NOT NULL
		GROUP BY cod_cliente
	) plazos ON c.cod_cli = plazos.cod_cliente
	LEFT JOIN (
		SELECT cod_cliente, SUM(importe) total_cheques
		FROM cheque
		WHERE fecha_vencimiento >= dbo.relativeDate(GETDATE(), 'today', 0) AND anulado = 'N' AND cod_cliente IS NOT NULL
		GROUP BY cod_cliente
	) total_cheques ON c.cod_cli = total_cheques.cod_cliente
	LEFT JOIN (
		SELECT cod_cliente, SUM(importe_total) pagos_ingresados_mes
		FROM recibo
		WHERE month(fecha_documento) = month(GETDATE()) AND year(fecha_documento) = year(GETDATE()) AND anulado = 'N' AND cod_cliente IS NOT NULL
		GROUP BY cod_cliente
	) pagos_ingresados_mes ON c.cod_cli = pagos_ingresados_mes.cod_cliente


GO
---VIEW:dbo.programacion_empaque_v---
CREATE VIEW [dbo].[programacion_empaque_v] AS
CREATE VIEW dbo.programacion_empaque_v
AS
SELECT     dbo.Orden_fabricacion.Confirmada, dbo.Orden_fabricacion.fecha_inicio, dbo.Tareas_cabecera.fecha_corte, dbo.Tareas_cabecera.fecha_aparado, 
                      dbo.Tareas_cabecera.fecha_armado, dbo.Tareas_cabecera.fecha_programacion, dbo.Orden_fabricacion.nro_plan, 
                      dbo.Tareas_cabecera.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, dbo.Tareas_cabecera.anulado, dbo.Orden_fabricacion.cod_articulo, 
                      dbo.articulos.denom_articulo, dbo.Orden_fabricacion.cod_color_articulo, dbo.Tareas_detalle.cod_seccion, 
                      dbo.Tareas_detalle.fecha_salida_programada, dbo.Tareas_cabecera.cantidad, dbo.Tareas_cabecera.cantidad_ultimo_paso_cumplido, 
                      dbo.Tareas_detalle.cumplido_paso, dbo.Tareas_cabecera.tipo_tarea, dbo.Tareas_cabecera.situacion, dbo.Tareas_cabecera.pos_1_cant, 
                      dbo.Tareas_cabecera.pos_2_cant, dbo.Tareas_cabecera.pos_3_cant, dbo.Tareas_cabecera.pos_4_cant, dbo.Tareas_cabecera.pos_5_cant, 
                      dbo.Tareas_cabecera.pos_6_cant, dbo.Tareas_cabecera.pos_7_cant, dbo.Tareas_cabecera.pos_8_cant, dbo.rango_talles.posic_1, 
                      dbo.colores_por_articulo.cod_material, dbo.colores_por_articulo.cod_color, dbo.Tareas_cabecera.ultima_seccion_cumplida, 
                      dbo.Tareas_cabecera.operador_entregado, dbo.Tareas_cabecera.observacion, dbo.Tareas_cabecera.ultimo_paso_cumplido, 
                      dbo.Tareas_cabecera.seleccion, dbo.colores_por_articulo.cod_mp_critico_1, dbo.colores_por_articulo.cod_mp_critico_2, 
                      dbo.colores_por_articulo.cod_mp_critico_3, dbo.colores_por_articulo.cod_color_mp_critico_1, dbo.colores_por_articulo.cod_color_mp_critico_2, 
                      dbo.colores_por_articulo.cod_color_mp_critico_3, dbo.Orden_fabricacion.version, dbo.articulos.cod_ruta
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango INNER JOIN
                      dbo.colores_por_articulo INNER JOIN
                      dbo.Orden_fabricacion INNER JOIN
                      dbo.Tareas_cabecera ON dbo.Orden_fabricacion.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion INNER JOIN
                      dbo.Tareas_detalle ON dbo.Tareas_cabecera.nro_tarea = dbo.Tareas_detalle.nro_tarea AND 
                      dbo.Tareas_cabecera.nro_orden_fabricacion = dbo.Tareas_detalle.nro_orden_fabricacion ON 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.Orden_fabricacion.cod_color_articulo AND 
                      dbo.colores_por_articulo.cod_articulo = dbo.Orden_fabricacion.cod_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
WHERE     (dbo.Tareas_detalle.cod_seccion = 60) OR
                      (dbo.Tareas_detalle.cod_seccion = 62)

GO
---VIEW:dbo.patrones_vigentes_v---
CREATE VIEW [dbo].[patrones_vigentes_v] AS
CREATE VIEW dbo.patrones_vigentes_v
AS
SELECT     TOP 100 PERCENT dbo.Patrones_mp_detalle.cod_seccion, dbo.Patrones_mp_cabecera.cod_articulo, dbo.articulos.denom_articulo, 
                      dbo.Patrones_mp_detalle.cod_color_articulo, dbo.Patrones_mp_detalle.nro_item, dbo.Patrones_mp_detalle.version, 
                      dbo.Patrones_mp_detalle.conjunto, dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material, 
                      dbo.Patrones_mp_detalle.fecha_alta, dbo.Patrones_mp_detalle.consumo_par, dbo.Patrones_mp_cabecera.tipo_patron, 
                      dbo.materiales.factor_conversion, dbo.Patrones_mp_cabecera.borrador, dbo.materiales.unidad_medida AS UM, dbo.materiales.produccion_interna, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.articulos.naturaleza
FROM         dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo INNER JOIN
                      dbo.Patrones_mp_cabecera INNER JOIN
                      dbo.Patrones_mp_detalle ON dbo.Patrones_mp_cabecera.version = dbo.Patrones_mp_detalle.version AND 
                      dbo.Patrones_mp_cabecera.cod_color_articulo = dbo.Patrones_mp_detalle.cod_color_articulo AND 
                      dbo.Patrones_mp_cabecera.cod_articulo = dbo.Patrones_mp_detalle.cod_articulo INNER JOIN
                      dbo.materiales ON dbo.Patrones_mp_detalle.cod_material = dbo.materiales.cod_material ON 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.Patrones_mp_cabecera.cod_color_articulo AND 
                      dbo.colores_por_articulo.cod_articulo = dbo.Patrones_mp_cabecera.cod_articulo
WHERE     (dbo.articulos.vigente = 'S') AND (dbo.colores_por_articulo.aprob_produccion = 'S') AND (dbo.colores_por_articulo.vigente = 'S') AND 
                      (dbo.Patrones_mp_cabecera.version_actual = N'S') AND (dbo.colores_por_articulo.id_tipo_producto_stock = '01' OR
                      dbo.colores_por_articulo.id_tipo_producto_stock = '02' OR
                      dbo.colores_por_articulo.id_tipo_producto_stock = '04' OR
                      dbo.colores_por_articulo.id_tipo_producto_stock = '08' OR
                      dbo.colores_por_articulo.id_tipo_producto_stock = '05') AND (dbo.articulos.naturaleza = N'pt')
ORDER BY dbo.Patrones_mp_cabecera.cod_articulo, dbo.Patrones_mp_detalle.nro_item

GO
---VIEW:dbo.materiales_preferentes_v---
CREATE VIEW [dbo].[materiales_preferentes_v] AS
CREATE VIEW dbo.materiales_preferentes_v
AS
SELECT     TOP 100 PERCENT dbo.materiales.cod_material, dbo.Materias_primas.cod_color, dbo.materiales.denom_material AS Material, 
                      dbo.Proveedores_materias_primas.cod_proveedor, dbo.proveedores_datos.razon_social, dbo.rubros_materias_primas.denom_rubro AS Rubro, 
                      dbo.materiales.cod_rango AS Rango, dbo.materiales.unidad_medida AS UMS, dbo.materiales.unidad_medida_compra AS UMC, 
                      dbo.materiales.factor_conversion AS FC, dbo.materiales.lote_minimo, dbo.materiales.lote_multiplo, dbo.materiales.fecha_ultima_modificacion, 
                      dbo.materiales.produccion_interna, dbo.Materias_primas.precio_unitario AS PU, dbo.Proveedores_materias_primas.precio_compra AS PC, 
                      dbo.materiales.cod_subrubro, ISNULL(dbo.Proveedores_materias_primas.precio_compra, dbo.Materias_primas.precio_unitario) AS Precio
FROM         dbo.Materias_primas LEFT OUTER JOIN
                      dbo.Proveedores_materias_primas ON dbo.Materias_primas.cod_material = dbo.Proveedores_materias_primas.cod_material AND 
                      dbo.Materias_primas.cod_color = dbo.Proveedores_materias_primas.cod_color RIGHT OUTER JOIN
                      dbo.materiales INNER JOIN
                      dbo.rubros_materias_primas ON dbo.materiales.cod_rubro = dbo.rubros_materias_primas.cod_rubro ON 
                      dbo.Materias_primas.cod_material = dbo.materiales.cod_material LEFT OUTER JOIN
                      dbo.proveedores_datos ON dbo.Proveedores_materias_primas.cod_proveedor = dbo.proveedores_datos.cod_prov
WHERE     (dbo.Proveedores_materias_primas.preferente_costo = 'S')
ORDER BY dbo.materiales.denom_material

GO
---VIEW:dbo.costo_mp_producto_detalle_v---
CREATE VIEW [dbo].[costo_mp_producto_detalle_v] AS
CREATE VIEW dbo.costo_mp_producto_detalle_v
AS
SELECT     dbo.patrones_vigentes_v.cod_articulo, dbo.patrones_vigentes_v.denom_articulo, dbo.patrones_vigentes_v.cod_color_articulo, 
                      dbo.patrones_vigentes_v.tipo_patron, dbo.patrones_vigentes_v.cod_material, dbo.Materias_primas.precio_unitario AS precio, 
                      dbo.materiales.denom_material AS material, dbo.patrones_vigentes_v.cod_color_material, dbo.patrones_vigentes_v.consumo_par, 
                      dbo.conjuntos.denom_conjunto, dbo.conjuntos.conjunto, dbo.patrones_vigentes_v.factor_conversion, 
                      dbo.Materias_primas.precio_unitario * dbo.patrones_vigentes_v.consumo_par / dbo.patrones_vigentes_v.factor_conversion AS Costo, 
                      dbo.patrones_vigentes_v.cod_seccion, dbo.subrubros_materias_primas.denom_subrubro, dbo.rubros_materias_primas.denom_rubro, 
                      dbo.articulos.cod_linea
FROM         dbo.rubros_materias_primas INNER JOIN
                      dbo.materiales ON dbo.rubros_materias_primas.cod_rubro = dbo.materiales.cod_rubro INNER JOIN
                      dbo.subrubros_materias_primas ON dbo.materiales.cod_rubro = dbo.subrubros_materias_primas.cod_rubro AND 
                      dbo.materiales.cod_subrubro = dbo.subrubros_materias_primas.cod_subrubro INNER JOIN
                      dbo.Materias_primas ON dbo.materiales.cod_material = dbo.Materias_primas.cod_material INNER JOIN
                      dbo.patrones_vigentes_v INNER JOIN
                      dbo.conjuntos ON dbo.patrones_vigentes_v.conjunto = dbo.conjuntos.conjunto ON 
                      dbo.Materias_primas.cod_material = dbo.patrones_vigentes_v.cod_material AND 
                      dbo.Materias_primas.cod_color = dbo.patrones_vigentes_v.cod_color_material INNER JOIN
                      dbo.articulos ON dbo.patrones_vigentes_v.cod_articulo = dbo.articulos.cod_articulo
GROUP BY dbo.conjuntos.denom_conjunto, dbo.patrones_vigentes_v.cod_color_articulo, dbo.patrones_vigentes_v.tipo_patron, 
                      dbo.patrones_vigentes_v.denom_articulo, dbo.patrones_vigentes_v.consumo_par, dbo.conjuntos.conjunto, dbo.materiales.denom_material, 
                      dbo.patrones_vigentes_v.cod_color_material, dbo.patrones_vigentes_v.cod_articulo, dbo.patrones_vigentes_v.cod_material, dbo.conjuntos.conjunto, 
                      dbo.patrones_vigentes_v.factor_conversion, 
                      dbo.Materias_primas.precio_unitario * dbo.patrones_vigentes_v.consumo_par / dbo.patrones_vigentes_v.factor_conversion, 
                      dbo.patrones_vigentes_v.cod_seccion, dbo.materiales.cod_rubro, dbo.materiales.cod_subrubro, dbo.subrubros_materias_primas.denom_subrubro, 
                      dbo.rubros_materias_primas.denom_rubro, dbo.Materias_primas.precio_unitario, dbo.articulos.cod_linea

GO
---VIEW:dbo.cheques_rechazados_v---
CREATE VIEW [dbo].[cheques_rechazados_v] AS


CREATE VIEW cheques_rechazados_v AS
	SELECT fecha_documento fecha, c.fecha_vencimiento, c.empresa, cli.cod_cli, cli.razon_social cliente_razon_social, p.cod_prov, p.razon_social proveedor_razon_social,
	c.librador_nombre, b.nombre banco_nombre, c.numero, c.importe, m.nombre_motivo, rc.observaciones, o.cod_operador cod_vendedor, (pe.nombres + ' ' + pe.apellido) nombre_vendedor
	FROM cheque c
	INNER JOIN rechazo_de_cheque_c rc ON c.cod_rechazo_cheque = rc.cod_rechazo_cheque AND c.empresa = rc.empresa
	LEFT OUTER JOIN clientes cli ON cli.cod_cli = c.cod_cliente
	LEFT OUTER JOIN operadores o ON cli.cod_vendedor = o.cod_operador
	LEFT OUTER JOIN personal pe ON pe.cod_personal = o.cod_personal
	LEFT OUTER JOIN proveedores_datos p ON p.cod_prov = c.cod_proveedor
	INNER JOIN banco b ON b.cod_banco = c.cod_banco
	INNER JOIN motivo m ON m.cod_motivo = rc.motivo
	WHERE c.cod_rechazo_cheque IS NOT NULL
GO
---VIEW:dbo.costo_mp_producto_V---
CREATE VIEW [dbo].[costo_mp_producto_V] AS
CREATE VIEW dbo.costo_mp_producto_V
AS
SELECT     cod_articulo, denom_articulo, cod_color_articulo, SUM(Costo) AS Costo, cod_linea
FROM         dbo.costo_mp_producto_detalle_v
GROUP BY cod_articulo, denom_articulo, cod_color_articulo, cod_linea

GO
---VIEW:dbo.costo_mp_factura_v---
CREATE VIEW [dbo].[costo_mp_factura_v] AS
CREATE VIEW dbo.costo_mp_factura_v
AS
SELECT     TOP 100 PERCENT dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.punto_venta, 
                      dbo.documentos_cantidades.tipo_docum, dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, 
                      SUM((dbo.costo_producto_total_V.costo + dbo.costo_producto_total_V.costo_linea) * dbo.documentos_cantidades.cantidad) AS costo, 
                      SUM(dbo.documentos_cantidades.cantidad * dbo.documentos_cantidades.precio_unitario_final) AS importe_articulos
FROM         dbo.documentos_cantidades INNER JOIN
                      dbo.costo_producto_total_V ON dbo.documentos_cantidades.cod_articulo = dbo.costo_producto_total_V.cod_articulo AND 
                      dbo.documentos_cantidades.cod_color_articulo = dbo.costo_producto_total_V.cod_color_articulo
GROUP BY dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.tipo_docum, 
                      dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, dbo.documentos_cantidades.punto_venta
HAVING      (dbo.documentos_cantidades.fecha > dbo.relativeDate(GETDATE(), 'first', - 3))
ORDER BY dbo.documentos_cantidades.fecha

GO
---VIEW:dbo.materiales_v---
CREATE VIEW [dbo].[materiales_v] AS
CREATE VIEW materiales_v AS
	SELECT m.*, a.cod_articulo, a.naturaleza
	FROM materiales m
	LEFT OUTER JOIN articulos a ON m.cod_material = a.cod_material

GO
---VIEW:dbo.filas_asientos_contables_v---
CREATE VIEW [dbo].[filas_asientos_contables_v] AS
CREATE VIEW dbo.filas_asientos_contables_v
AS
SELECT     TOP 100 PERCENT c.cod_asiento, c.empresa, c.nombre AS asunto, dbo.round(c.importe) AS importe, c.cod_ejercicio, c.fecha_asiento, d.numero_fila, 
                      d.cod_imputacion, pc.denominacion AS denominacion_imputacion, dbo.round(d.importe_debe) AS importe_debe, dbo.round(d.importe_haber) 
                      AS importe_haber, d.fecha_vencimiento, d.observaciones, d.anulado
FROM         dbo.asientos_contables c INNER JOIN
                      dbo.filas_asientos_contables d ON c.cod_asiento = d.cod_asiento INNER JOIN
                      dbo.plan_cuentas pc ON pc.cuenta = d.cod_imputacion
ORDER BY c.fecha_asiento DESC

GO
---VIEW:dbo.ficha_tecnica_patrones_d---
CREATE VIEW [dbo].[ficha_tecnica_patrones_d] AS
CREATE VIEW dbo.ficha_tecnica_patrones_d
AS
SELECT     p.cod_articulo, p.cod_color_articulo, p.version, p.nro_item, p.cod_pieza, p.cod_material, p.cod_color_material, p.cod_seccion, p.fecha_alta, p.item_nuevo, 
                      p.consumo_par, p.consumo_batch, p.sckrap_batch, p.sckrap_porcentual, p.conjunto, p.varia, p.escalado, p.escala_desplazada, p.tipo_patron, p.trazable, 
                      p.asignado_lote, p.cant_entregada, p.entregado, m.unidad_medida AS ums, m.denom_material AS denominacion_material, c.denom_conjunto, p.cod_temporada
FROM         dbo.Patrones_mp_detalle AS p LEFT OUTER JOIN
                      dbo.materiales AS m ON p.cod_material = m.cod_material LEFT OUTER JOIN
                      dbo.conjuntos AS c ON p.conjunto = c.conjunto

GO
---VIEW:dbo.importe_por_operacion_d_v---
CREATE VIEW [dbo].[importe_por_operacion_d_v] AS

	CREATE VIEW importe_por_operacion_d_v AS
		SELECT d.*, c.tipo_transferencia, c.cod_caja, c.fecha_caja, c.fecha_alta
		FROM importe_por_operacion_d d
		INNER JOIN importe_por_operacion_c c ON d.cod_importe_operacion = c.cod_importe_operacion
GO
---VIEW:dbo.ingresos_v---
CREATE VIEW [dbo].[ingresos_v] AS
CREATE VIEW dbo.ingresos_v
AS
SELECT     fecha_alta, empresa, tipo_docum, nro_documento, cod_cliente, operacion_tipo, importe_1, importe_2, importe_3, importe_4, importe_5, 
                      ingreso_bancario_importe, importe_6, importe_total, plazo_promedio_pago, motivo, anulado
FROM         dbo.recibos_c
WHERE     (anulado <> 's') AND (fecha_alta > CONVERT(DATETIME, '2012-01-01 00:00:00', 102))

GO
---VIEW:dbo.stock20_por_talle_v---
CREATE VIEW [dbo].[stock20_por_talle_v] AS
CREATE VIEW dbo.stock20_por_talle
AS
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock20.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '20')) stock20 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock20.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock20.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_2 AS Talle, 
                      ISNULL(stock20.cant_2, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_2
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '20')) stock20 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock20.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock20.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_3 AS Talle, 
                      ISNULL(stock20.cant_3, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_3
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '20')) stock20 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock20.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock20.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_4 AS Talle, 
                      ISNULL(stock20.cant_4, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_4
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '20')) stock20 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock20.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock20.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION AL
L
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_5 AS Talle, 
                      ISNULL(stock20.cant_5, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_5
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '20')) stock20 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock20.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock20.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_6 AS Talle, 
                      ISNULL(stock20.cant_6, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_6
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '20')) stock20 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock20.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock20.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_7 AS Talle, 
                      ISNULL(stock20.cant_7, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_7
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '20')) stock20 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock20.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock20.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_8 AS Talle, 
                      ISNULL(stock20.cant_8, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_8
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '20')) stock20 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock20.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock20.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo

GO
---VIEW:dbo.proveedores_plazo_pago---
CREATE VIEW [dbo].[proveedores_plazo_pago] AS

CREATE VIEW proveedores_plazo_pago AS
SELECT
	cod_prov AS Cod,
	razon_social AS Razon_Social,
	denom_fantasia AS Denom_Fantasia,
	plazo_pago AS Plazo_Pago
FROM proveedores_datos
WHERE anulado = 'N'
GO
---VIEW:dbo.chequera_v---
CREATE VIEW [dbo].[chequera_v] AS
CREATE VIEW [dbo].[chequera_v] AS
	SELECT c.*, d.cod_chequera_d, d.numero, d.utilizado, cb.nombre_cuenta
	FROM chequera_c c
	INNER JOIN chequera_d d ON c.cod_chequera = d.cod_chequera
	INNER JOIN cuenta_bancaria cb ON c.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
	WHERE d.utilizado = 'N'
GO
---VIEW:dbo.cheque_v---
CREATE VIEW [dbo].[cheque_v] AS
CREATE VIEW [dbo].[cheque_v] AS
	SELECT c.*, b.nombre as banco_nombre,
	DATEDIFF(dd, GETDATE(), c.fecha_vencimiento) as dias_vencimiento
	FROM cheque c
	LEFT JOIN banco b ON b.cod_banco = c.cod_banco


GO
---VIEW:dbo.stock14y20_por_talle_v---
CREATE VIEW [dbo].[stock14y20_por_talle_v] AS
CREATE VIEW dbo.stock14y20_por_talle_v
AS
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_1) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_2) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_3) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_4) AS
 cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_5) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_6) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_7) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.col
ores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_8) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_9) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_10) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo

GO
---VIEW:dbo.stock_pedidos_pendientes---
CREATE VIEW [dbo].[stock_pedidos_pendientes] AS
CREATE VIEW dbo.stock_pedidos_pendientes
AS
SELECT     TOP 100 PERCENT dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, SUM(ISNULL(dbo.pedidos_d.pend_1, 
                      CONVERT(numeric, 0))) AS pend_1, SUM(ISNULL(dbo.pedidos_d.pend_2, CONVERT(numeric, 0))) AS pend_2, SUM(ISNULL(dbo.pedidos_d.pend_3, 
                      CONVERT(numeric, 0))) AS pend_3, SUM(ISNULL(dbo.pedidos_d.pend_4, CONVERT(numeric, 0))) AS pend_4, SUM(ISNULL(dbo.pedidos_d.pend_5, 
                      CONVERT(numeric, 0))) AS pend_5, SUM(ISNULL(dbo.pedidos_d.pend_6, CONVERT(numeric, 0))) AS pend_6, SUM(ISNULL(dbo.pedidos_d.pend_7, 
                      CONVERT(numeric, 0))) AS pend_7, SUM(ISNULL(dbo.pedidos_d.pend_8, CONVERT(numeric, 0))) AS pend_8, SUM(ISNULL(dbo.pedidos_d.pend_9, 
                      CONVERT(numeric, 0))) AS pend_9, SUM(ISNULL(dbo.pedidos_d.pend_10, CONVERT(numeric, 0))) AS pend_10, SUM(ISNULL(dbo.pedidos_d.pend_1, 
                      CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_2, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_3, CONVERT(numeric, 0)) 
                      + ISNULL(dbo.pedidos_d.pend_4, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_5, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_6, 
                      CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_7, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_8, CONVERT(numeric, 0)) 
                      + ISNULL(dbo.pedidos_d.pend_9, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_10, CONVERT(numeric, 0))) AS cant_pend, 
                      dbo.pedidos_d.anulado, dbo.pedidos_c.id_estado_pedido
FROM         dbo.pedidos_c INNER JOIN
                      dbo.pedidos_d ON dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido
WHERE     (dbo.pedidos_d.pendiente > 0) AND (dbo.pedidos_c.anulado = 'N')
GROUP BY dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, dbo.pedidos_d.anulado, 
                      dbo.pedidos_c.id_estado_pedido
HAVING      (dbo.pedidos_d.anulado = 'n')

GO
---VIEW:dbo.usuarios_por_caja_v---
CREATE VIEW [dbo].[usuarios_por_caja_v] AS



CREATE VIEW [dbo].[usuarios_por_caja_v] AS
	SELECT		c.cod_caja, c.nombre, p.cod_usuario,
				(CASE WHEN (SELECT count(*) FROM cuenta_bancaria cb WHERE cb.cod_caja = c.cod_caja) > 0 THEN 'S' ELSE 'N' END) es_caja_banco
	FROM		usuarios_por_caja p INNER JOIN
				caja c ON p.cod_caja = c.cod_caja
	GROUP BY	c.cod_caja, c.nombre, p.cod_usuario
GO
---VIEW:dbo.stock01_por_talle_v---
CREATE VIEW [dbo].[stock01_por_talle_v] AS
CREATE VIEW dbo.stock01_por_talle_v AS SELECT dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock01.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, ISNULL(stock01.cant_1, 0) AS cant_1 FROM dbo.rango_talles INNER JOIN dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN (SELECT cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_1 FROM dbo.stock WHERE (cod_almacen = '01')) stock01 RIGHT OUTER JOIN dbo.colores_por_articulo ON stock01.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND stock01.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo UNION ALL SELECT dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock01.cantidad, 0) AS cantidad, dbo.rango_talles.posic_2 AS Talle, ISNULL(stock01.cant_2, 0) AS cant_1 FROM dbo.rango_talles INNER JOIN dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN (SELECT cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_2 FROM dbo.stock WHERE (cod_almacen = '01')) stock01 RIGHT OUTER JOIN dbo.colores_por_articulo ON stock01.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND stock01.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo UNION ALL SELECT dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock01.cantidad, 0) AS cantidad, dbo.rango_talles.posic_3 AS Talle, ISNULL(stock01.cant_3, 0) AS cant_1 FROM dbo.rango_talles INNER JOIN dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN (SELECT cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_3 FROM dbo.stock WHERE (cod_almacen = '01')) stock01 RIGHT OUTER JOIN dbo.colores_por_articulo ON stock01.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND stock01.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo UNION ALL SELECT dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock01.cantidad, 0) AS cantidad, dbo.rango_talles.posic_4 AS Talle, ISNULL(stock01.cant_4, 0) AS cant_1 FROM dbo.rango_talles INNER JOIN dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN (SELECT cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_4 FROM dbo.stock WHERE (cod_almacen = '01')) stock01 RIGHT OUTER JOIN dbo.colores_por_articulo ON stock01.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND stock01.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo UNION ALL SELECT dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock01.cantidad, 0) AS cantidad, dbo.rango_talles.posic_5 AS Talle, ISNULL(stock01.cant_5, 0) AS cant_1 FROM dbo.rango_talles INNER JOIN dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN (SELECT cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_5 FROM dbo.stock WHERE (cod_almacen = '01')) stock01 RIGHT OUTER JOIN dbo.colores_por_articulo ON stock01.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND stock01.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo UNION ALL SELECT dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock01.cantidad, 0) AS cantidad, dbo.rango_talles.posic_6 AS Talle, ISNULL(stock01.cant_6, 0) AS c
ant_1 FROM dbo.rango_talles INNER JOIN dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN (SELECT cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_6 FROM dbo.stock WHERE (cod_almacen = '01')) stock01 RIGHT OUTER JOIN dbo.colores_por_articulo ON stock01.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND stock01.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo UNION ALL SELECT dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock01.cantidad, 0) AS cantidad, dbo.rango_talles.posic_7 AS Talle, ISNULL(stock01.cant_7, 0) AS cant_1 FROM dbo.rango_talles INNER JOIN dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN (SELECT cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_7 FROM dbo.stock WHERE (cod_almacen = '01')) stock01 RIGHT OUTER JOIN dbo.colores_por_articulo ON stock01.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND stock01.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo UNION ALL SELECT dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock01.cantidad, 0) AS cantidad, dbo.rango_talles.posic_8 AS Talle, ISNULL(stock01.cant_8, 0) AS cant_1 FROM dbo.rango_talles INNER JOIN dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN (SELECT cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_8 FROM dbo.stock WHERE (cod_almacen = '01')) stock01 RIGHT OUTER JOIN dbo.colores_por_articulo ON stock01.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND stock01.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo 
GO
---VIEW:dbo.stock_menos_pendiente_vw_original---
CREATE VIEW [dbo].[stock_menos_pendiente_vw_original] AS
CREATE VIEW dbo.stock_menos_pendiente_vw_original AS SELECT stock_d.cod_almacen, stock_d.cod_articulo, stock_d.cod_color_articulo, SUM(stock_d.cant_s) AS cant_s, SUM(stock_d.S1) AS S1, SUM(stock_d.S2) AS S2, SUM(stock_d.S3) AS S3, SUM(stock_d.S4) AS S4, SUM(stock_d.S5) AS S5, SUM(stock_d.S6) AS S6, SUM(stock_d.S7) AS S7, SUM(stock_d.S8) AS S8, SUM(stock_d.S9) AS S9, SUM(stock_d.S10) AS S10 FROM (SELECT cod_almacen, cod_articulo, cod_color_articulo, cant_s, S1, S2, S3, S4, S5, S6, S7, S8, S9, S10 FROM dbo.stock_pt UNION SELECT cod_almacen, cod_articulo, cod_color_articulo, SUM(- (ISNULL(pendiente, 0) + ISNULL(predespachados, 0))) AS cant_s, SUM(- (ISNULL(pend_1, 0) + ISNULL(pred_1, 0))) AS S1, SUM(- (ISNULL(pend_2, 0) + ISNULL(pred_2, 0))) AS S2, SUM(- (ISNULL(pend_3, 0) + ISNULL(pred_3, 0))) AS S3, SUM(- (ISNULL(pend_4, 0) + ISNULL(pred_4, 0))) AS S4, SUM(- (ISNULL(pend_5, 0) + ISNULL(pred_5, 0))) AS S5, SUM(- (ISNULL(pend_6, 0) + ISNULL(pred_6, 0))) AS S6, SUM(- (ISNULL(pend_7, 0) + ISNULL(pred_7, 0))) AS S7, SUM(- (ISNULL(pend_8, 0) + ISNULL(pred_8, 0))) AS S8, SUM(- (ISNULL(pend_9, 0) + ISNULL(pred_9, 0))) AS S9, SUM(- (ISNULL(pend_10, 0) + ISNULL(pred_10, 0))) AS S10 FROM dbo.pedidos_d_v WHERE (anulado = 'N') AND (ISNULL(pendiente, 0) + ISNULL(predespachados, 0) > 0) GROUP BY cod_almacen, cod_articulo, cod_color_articulo) stock_d LEFT OUTER JOIN dbo.colores_por_articulo c ON c.cod_articulo = stock_d.cod_articulo AND c.cod_color_articulo = stock_d.cod_color_articulo WHERE (c.comercializacion_libre <> 'A') GROUP BY stock_d.cod_almacen, stock_d.cod_articulo, stock_d.cod_color_articulo 
GO
---VIEW:dbo.fichajes_v---
CREATE VIEW [dbo].[fichajes_v] AS

CREATE VIEW fichajes_v AS
SELECT
	r.clave_tabla, r.legajo_nro, r.movimiento_tipo, r.fecha, r.entrada_horario, r.salida_horario, r.con_anomalias, r.ubicacion_tipo, r.ubicacion_confirmada,
	r.diferencia_entrada, r.diferencia_salida, p.cod_personal, p.seccion seccion_produccion
FROM registro_entradas_salidas r
INNER JOIN personal p ON p.legajo_nro = r.legajo_nro

GO
---VIEW:dbo.proveedores_v---
CREATE VIEW [dbo].[proveedores_v] AS

CREATE VIEW [dbo].[proveedores_v] AS
	SELECT p.*, ia.importe_acumulado_mes, ia.importe_retenido_mes, gp1.saldo saldo_1, gp2.saldo saldo_2, (gp1.saldo + gp2.saldo) saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN dbo.importes_op_acumulado_mes AS ia ON p.cod_prov = ia.cod_proveedor
	LEFT JOIN gestion_proveedores_1 AS gp1 ON p.cod_prov = gp1.cod_prov
	LEFT JOIN gestion_proveedores_2 AS gp2 ON p.cod_prov = gp2.cod_prov
GO
---VIEW:dbo.costo_mp_factura_24meses_v---
CREATE VIEW [dbo].[costo_mp_factura_24meses_v] AS
CREATE VIEW dbo.costo_mp_factura_24meses_v
AS
SELECT     TOP 100 PERCENT dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.punto_venta, 
                      dbo.documentos_cantidades.tipo_docum, dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, 
                      SUM(dbo.costo_mp_producto_V.Costo * dbo.documentos_cantidades.cantidad) AS costo, 
                      SUM(dbo.documentos_cantidades.cantidad * dbo.documentos_cantidades.precio_unitario_final) AS importe_articulos
FROM         dbo.documentos_cantidades LEFT OUTER JOIN
                      dbo.costo_mp_producto_V ON dbo.documentos_cantidades.cod_articulo = dbo.costo_mp_producto_V.cod_articulo AND 
                      dbo.documentos_cantidades.cod_color_articulo = dbo.costo_mp_producto_V.cod_color_articulo
GROUP BY dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.tipo_docum, 
                      dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, dbo.documentos_cantidades.punto_venta
HAVING      (dbo.documentos_cantidades.fecha > dbo.relativeDate(GETDATE(), 'first', - 24))
ORDER BY dbo.documentos_cantidades.fecha

GO
---VIEW:dbo.retenciones_efectuadas_v---
CREATE VIEW [dbo].[retenciones_efectuadas_v] AS
CREATE VIEW retenciones_efectuadas_v AS
SELECT
	r.fecha, r.nombre razon_social, r.cuit, r.importe importe_retencion, o.nro_orden_de_pago, o.importe_total importe_orden_de_pago
FROM retencion_efectuada r
LEFT JOIN importe_por_operacion_d ixo ON ixo.tipo_importe = 'R' AND ixo.cod_importe = r.cod_retencion
LEFT JOIN orden_de_pago o ON o.cod_importe_operacion = ixo.cod_importe_operacion
WHERE r.anulado = 'N' AND r.importe > 0 AND o.anulado = 'N'
GO
---VIEW:dbo.importes_op_acumulado_mes---
CREATE VIEW [dbo].[importes_op_acumulado_mes] AS


CREATE VIEW [dbo].[importes_op_acumulado_mes] AS
	SELECT p.cod_prov cod_proveedor, SUM(ISNULL(op.importe_sujeto_ret, 0)) importe_acumulado_mes, SUM(ISNULL(re.importe_retenido, 0)) importe_retenido_mes
	FROM proveedores_datos p
		LEFT JOIN orden_de_pago op ON
			p.cod_prov = op.cod_proveedor AND
			op.anulado = 'N' AND
			op.fecha_documento > dbo.relativeDate(GETDATE(), 'first', 0) AND
			op.fecha_documento < dbo.relativeDate(GETDATE(), 'last', 0)
		LEFT JOIN retenciones_impositivas_efectuad re ON
			re.cod_prov = p.cod_prov AND
			re.fecha_retencion > dbo.relativeDate(GETDATE(), 'first', 0) AND
			re.fecha_retencion < dbo.relativeDate(GETDATE(), 'last', 0)
	WHERE p.anulado = 'N' AND op.anulado = 'N' AND op.empresa = '1'
	GROUP BY p.cod_prov
GO
---VIEW:dbo.stock_menos_asignado_vw---
CREATE VIEW [dbo].[stock_menos_asignado_vw] AS
CREATE VIEW dbo.stock_menos_asignado_vw
AS
SELECT     cod_almacen, cod_articulo, cod_color_articulo, SUM(cant_s) AS cant_s, SUM(S1) AS S1, SUM(S2) AS S2, SUM(S3) AS S3, SUM(S4) AS S4, SUM(S5) 
                      AS S5, SUM(S6) AS S6, SUM(S7) AS S7, SUM(S8) AS S8, SUM(S9) AS S9, SUM(S10) AS S10
FROM         (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cant_s, S1, S2, S3, S4, S5, S6, S7, S8, S9, S10
                       FROM          dbo.stock_pt
                       UNION
                       SELECT     cod_almacen, cod_articulo, cod_color_articulo, SUM(- (ISNULL(predespachados, 0))) AS cant_s, SUM(- (ISNULL(pred_1, 0))) AS S1, 
                                             SUM(- (ISNULL(pred_2, 0))) AS S2, SUM(- (ISNULL(pred_3, 0))) AS S3, SUM(- (ISNULL(pred_4, 0))) AS S4, SUM(- (ISNULL(pred_5, 0))) AS S5, 
                                             SUM(- (ISNULL(pred_6, 0))) AS S6, SUM(- (ISNULL(pred_7, 0))) AS S7, SUM(- (ISNULL(pred_8, 0))) AS S8, SUM(- (ISNULL(pred_9, 0))) AS S9, 
                                             SUM(- (ISNULL(pred_10, 0))) AS S10
                       FROM         dbo.pedidos_d_v
                       WHERE     (anulado = 'N') AND (ISNULL(predespachados, 0) > 0)
                       GROUP BY cod_almacen, cod_articulo, cod_color_articulo) stock_d
GROUP BY cod_almacen, cod_articulo, cod_color_articulo

GO
---VIEW:dbo.articulos_imagenes_v---
CREATE VIEW [dbo].[articulos_imagenes_v] AS
CREATE VIEW dbo.articulos_imagenes_v
AS
SELECT     TOP 100 PERCENT *
FROM         dbo.articulos_imagenes ai
WHERE     (tipo = N'imagen')
ORDER BY producto, orden

GO
---VIEW:dbo.resumen_bancario_v---
CREATE VIEW [dbo].[resumen_bancario_v] AS
CREATE VIEW resumen_bancario_v AS
	SELECT 'E' tipo, 'DC' tipo_documento, dc.cod_acreditar_debitar_cheque numero,
		'Cheque Nº: ' + cast((SELECT TOP 1 c.numero
		FROM cheque c
		INNER JOIN importe_por_operacion_d ipod2 ON ipod2.cod_importe_operacion = ipoc.cod_importe_operacion AND ipod2.tipo_importe = 'C' AND ipod2.cod_importe = c.cod_cheque
		) AS VARCHAR) detalle,
		importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(dcc.fecha,'today',0) fecha,
		dc.empresa empresa,
		cast(dcc.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM acreditar_debitar_cheque_d dc
	INNER JOIN acreditar_debitar_cheque_c dcc ON dc.cod_acreditar_debitar_cheque = dcc.cod_acreditar_debitar_cheque AND dc.empresa = dcc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON dc.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE dc.entrada_salida = 'E' AND dcc.tipo = 'D'

	UNION ALL

	SELECT 'I' tipo, 'AC' tipo_documento, ac.cod_acreditar_debitar_cheque numero,
		'Cheque Nº: ' + cast((SELECT TOP 1 c.numero
		FROM cheque c
		INNER JOIN importe_por_operacion_d ipod2 ON ipod2.cod_importe_operacion = ipoc.cod_importe_operacion AND ipod2.tipo_importe = 'C' AND ipod2.cod_importe = c.cod_cheque
		) AS VARCHAR) detalle,
		importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(acc.fecha,'today',0) fecha,
		ac.empresa empresa,
		cast(acc.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM acreditar_debitar_cheque_d ac
	INNER JOIN acreditar_debitar_cheque_c acc ON acc.cod_acreditar_debitar_cheque = ac.cod_acreditar_debitar_cheque AND ac.empresa = acc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ac.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE ac.entrada_salida = 'S' AND acc.tipo = 'C'

	UNION ALL

	SELECT 'I' tipo, 'DB' tipo_documento, db.cod_deposito_bancario numero,
		'-' detalle,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE db.cod_importe_operacion = ipoc1.cod_importe_operacion) importe,
		ipoc.cod_caja, dbo.relativeDate(dbc.fecha_documento,'today',0) fecha,
		db.empresa empresa,
		cast(dbc.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM deposito_bancario_d db
	INNER JOIN deposito_bancario_c dbc ON dbc.cod_deposito_bancario = db.cod_deposito_bancario AND dbc.empresa = db.empresa
	INNER JOIN importe_por_operacion_c ipoc ON db.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE db.entrada_salida = 'E' AND
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE db.cod_importe_operacion = ipoc1.cod_importe_operacion) > 0

	UNION ALL

	SELECT (case when ti.entrada_salida = 'S' then 'E' else 'I' end) tipo, 'TI' tipo_documento, ti.cod_transferencia_int numero,
		(
		'Desde: ' + cast((SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM transferencia_interna_d ti1
		INNER JOIN importe_por_operacion_c ipoc1 ON ti1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE ti1.entrada_salida = 'S' AND tic.cod_transferencia_int = ti1.cod_transferencia_int AND tic.empresa = ti1.empresa) AS VARCHAR)
		+ ' - ' +
		'Hacia: ' + cast((SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM transferencia_interna_d ti2
		INNER JOIN importe_por_operacion_c ipoc2 ON ti2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE ti2.entrada_salida 
= 'E' AND tic.cod_transferencia_int = ti2.cod_transferencia_int AND tic.empresa = ti2.empresa) AS VARCHAR)
		) detalle,
		importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(tic.fecha_documento,'today',0) fecha,
		ti.empresa empresa,
		cast(tic.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM transferencia_interna_d ti
	INNER JOIN transferencia_interna_c tic ON tic.cod_transferencia_int = ti.cod_transferencia_int AND tic.empresa = ti.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ti.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja

	UNION ALL

	SELECT (case when tbo.entrada_salida = 'E' then 'I' else 'E' end) tipo, 'TB' tipo_documento, tbo.cod_transferencia_ban numero,
		(case when tbo.entrada_salida = 'S' then 'Número transferencia: ' + cast(tbo.numero_transferencia AS VARCHAR) + ' ' else '' end)
		+ (case when tbo.hacia_desde IS NULL then '' else ((case when tbo.entrada_salida = 'S' then 'Receptor: ' else 'Emisor: ' end) + cast(tbo.hacia_desde AS VARCHAR)) end) detalle,
		tbo.importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(tbo.fecha,'today',0) fecha,
		tbo.empresa empresa,
		cast(tbo.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM transferencia_bancaria_operacion tbo
	INNER JOIN cuenta_bancaria cb ON tbo.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
	INNER JOIN caja caja ON caja.cod_caja = cb.cod_caja
	INNER JOIN importe_por_operacion_c ipoc ON tbo.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE tbo.anulado = 'N'

	UNION ALL

	SELECT 'I' tipo, 'VC' tipo_documento, ti.cod_venta_cheques numero,
		(SELECT 'Cantidad de cheques: ' + cast(count(*) AS VARCHAR)
		FROM venta_cheques_d ti2
		INNER JOIN importe_por_operacion_d ipod3 ON ti2.cod_importe_operacion = ipod3.cod_importe_operacion
		INNER JOIN cheque c ON c.cod_cheque = ipod3.cod_importe
		WHERE ti2.entrada_salida = 'S' AND ti2.cod_venta_cheques = ti.cod_venta_cheques AND ti2.empresa = ti.empresa
		) detalle,
		ti.importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(tic.fecha_documento,'today',0) fecha,
		ti.empresa empresa,
		cast(tic.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM venta_cheques_d ti
	INNER JOIN venta_cheques_c tic ON tic.cod_venta_cheques = ti.cod_venta_cheques AND tic.empresa = ti.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ti.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja
	WHERE ti.entrada_salida = 'E'
	
	UNION ALL

	SELECT 'I' tipo, 'PB' tipo_documento, p.nro_prestamo numero,
		'Importe pendiente: ' + cast(cast(p.importe_pendiente AS NUMERIC(10,2)) AS VARCHAR) detalle,
		p.importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(p.fecha_documento,'today',0) fecha,
		p.empresa empresa,
		cast(p.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM prestamo p
	INNER JOIN importe_por_operacion_c ipoc ON p.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja
	WHERE p.anulado = 'N'
GO
---VIEW:dbo.documentos---
CREATE VIEW [dbo].[documentos] AS


CREATE VIEW [dbo].[documentos] AS
	SELECT
			empresa, punto_venta, tipo_docum, numero, letra, nro_comprobante, anulado, tipo_docum_2, cod_cliente, cod_sucursal, cod_usuario,
			cancel_nro_documento, causa, CAST(observaciones AS VARCHAR(8000)) observaciones, fecha, fecha_alta, fecha_baja, fecha_ultima_mod,
			importe_total, importe_pendiente,
			importe_neto, importe_no_gravado, iva_importe_1, iva_porc_1, iva_importe_2, iva_porc_2, iva_importe_3, iva_porc_3, cotizacion_dolar,
			descuento_comercial_importe, descuento_comercial_porc, descuento_despacho_importe, cod_forma_pago,
			cae, cae_vencimiento, cae_obtencion_fecha, CAST(cae_obtencion_observaciones AS VARCHAR(8000)) cae_obtencion_observaciones, cae_obtencion_usuario, mail_enviado,
			tiene_detalle, dias_promedio_pago, cod_asiento_contable, cod_ecommerce_order,
			nro_recibo, cod_importe_operacion, operacion_tipo, imputacion, recibido_de, fecha_documento, fecha_ponderada_pago
	FROM
	(
		SELECT
			empresa, punto_venta, tipo_docum, nro_documento numero, letra, nro_comprobante, anulado, tipo_docum_2, cod_cliente, cod_sucursal, cod_usuario,
			cancel_nro_documento, causa, CAST(observaciones AS VARCHAR(8000)) observaciones, (CASE WHEN cae_obtencion_fecha IS NULL THEN fecha_documento ELSE cae_obtencion_fecha END) fecha, fecha_alta, fecha_baja, fecha_ultima_mod,
			importe_total, importe_pendiente,
			importe_neto, importe_no_gravado, iva_importe_1, iva_porc_1, iva_importe_2, iva_porc_2, iva_importe_3, iva_porc_3, cotizacion_dolar,
			descuento_comercial_importe, descuento_comercial_porc, descuento_despacho_importe, cod_forma_pago,
			cae, cae_vencimiento, cae_obtencion_fecha, CAST(cae_obtencion_observaciones AS VARCHAR(8000)) cae_obtencion_observaciones, cae_obtencion_usuario, mail_enviado,
			tiene_detalle, dias_promedio_pago, cod_asiento_contable, cod_ecommerce_order,
			NULL nro_recibo, NULL cod_importe_operacion, NULL operacion_tipo, NULL imputacion, NULL recibido_de, NULL fecha_documento, fecha_documento fecha_ponderada_pago
			
		FROM documentos_c
		WHERE anulado = 'N' AND cod_cliente > 0
		UNION ALL
		SELECT 
			empresa, 1 punto_venta, 'REC' tipo_docum, nro_recibo numero, 'R' letra, nro_recibo nro_comprobante, anulado, NULL tipo_docum_2, dbo.IfNullZero(cod_cliente) cod_cliente, NULL, cod_usuario,
			NULL cancel_nro_documento, NULL causa, CAST(observaciones AS VARCHAR(8000)) observaciones, fecha_documento fecha, fecha_alta, fecha_baja, fecha_ultima_mod,
			importe_total, importe_pendiente,
			NULL importe_neto, NULL importe_no_gravado, NULL iva_importe_1, NULL iva_porc_1, NULL iva_importe_2, NULL iva_porc_2, NULL iva_importe_3, NULL iva_porc_3, NULL cotizacion_dolar,
			NULL descuento_comercial_importe, NULL descuento_comercial_porc, NULL descuento_despacho_importe, NULL cod_forma_pago,
			NULL cae, NULL cae_vencimiento, NULL cae_obtencion_fecha, NULL cae_obtencion_observaciones, NULL cae_obtencion_usuario, NULL mail_enviado,
			NULL tiene_detalle, NULL dias_promedio_pago, cod_asiento_contable, cod_ecommerce_order,
			nro_recibo, cod_importe_operacion, operacion_tipo, imputacion, recibido_de, fecha_documento, fecha_ponderada_pago
		FROM recibo
		WHERE anulado = 'N' AND nro_recibo > 0
	) a
GO
---VIEW:dbo.lineas_productos---
CREATE VIEW [dbo].[lineas_productos] AS

-- Crear la vista llamada 'lineas' en la base de datos 'desarrollo'
CREATE VIEW lineas_productos AS
SELECT 
    cod_linea,
    denom_linea,
    origen,
    lanzamiento_inicial,
    estado_de_linea,
    fecha_de_baja,
    anulado,
    material,
    fecha_ultima_modificacion,
    autor_ultima_modificacion,
    cod_linea_nro,
    fechaAlta,
    titulo_catalogo
FROM 
    spiral.dbo.lineas_productos

GO
---VIEW:dbo.cuenta_corriente_historica_proveedor_v---
CREATE VIEW [dbo].[cuenta_corriente_historica_proveedor_v] AS
CREATE VIEW [dbo].[cuenta_corriente_historica_proveedor_v] AS
	SELECT
		dp.empresa, dp.punto_venta, dp.tipo_docum, dp.nro_documento, dp.letra, dp.cod_proveedor, dbo.relativeDate(dp.fecha,'today',0) fecha, dp.observaciones, (case when dp.tipo_docum = 'NCR' then -dp.importe_total else dp.importe_total end) as importe_total
	FROM documento_proveedor_c dp
	WHERE dp.anulado = 'N' AND dp.factura_gastos = 'N'
	UNION ALL
	SELECT
		op.empresa, NULL punto_venta, 'OP' tipo_docum, op.nro_orden_de_pago nro_documento, '-' letra, op.cod_proveedor, dbo.relativeDate(op.fecha_documento,'today',0) fecha, op.observaciones, -op.importe_total
	FROM orden_de_pago op
	WHERE op.anulado = 'N'



GO
---VIEW:dbo.cuenta_corriente_historica---
CREATE VIEW [dbo].[cuenta_corriente_historica] AS

CREATE VIEW [dbo].[cuenta_corriente_historica] AS
	SELECT TOP 100 PERCENT r.empresa empresa, 1 punto_venta, 'REC' tipo_docum, '' tipo_docum_2, r.nro_recibo numero, 'R' letra, NULL nro_comprobante,
						   r.cod_cliente cod_cliente, r.fecha_documento fecha, cast(r.observaciones AS VARCHAR(300)) observaciones, -r.importe_total importe_total,
						   NULL dias_promedio_pago, NULL cae_vencimiento, 0 importe_neto
	FROM recibo r
	WHERE r.anulado = 'N'

	UNION ALL

	SELECT TOP 100 PERCENT
		empresa empresa, punto_venta punto_venta, tipo_docum tipo_docum, tipo_docum_2 tipo_docum_2, numero numero, letra letra,
		nro_comprobante nro_comprobante, cod_cliente cod_cliente, fecha fecha, observaciones observaciones,
		((CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE (1) END) * importe_total) importe_total, dias_promedio_pago, cae_vencimiento,
		((CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE (1) END) * importe_neto)
	FROM documentos
	WHERE (anulado = 'N' OR anulado IS NULL) AND tipo_docum != 'REC'
GO
---VIEW:dbo.sucursales_v---
CREATE VIEW [dbo].[sucursales_v] AS
CREATE VIEW sucursales_v AS 
SELECT s.*, l.cod_zona_geo
FROM sucursales_clientes s
LEFT JOIN localidades l ON s.cod_pais = l.cod_pais AND s.cod_provincia = l.cod_provincia AND s.cod_localidad_nro = l.cod_localidad_nro


GO
---VIEW:dbo.Marcas---
CREATE VIEW [dbo].[Marcas] AS

-- Crear la vista llamada 'marcas' en la base de datos 'encinitas', que apunta a la tabla de 'spiral'
CREATE VIEW Marcas AS
SELECT 
    cod_marca,
    cod_cliente,
    denom_marca,
    anulado,
    fecha_ultima_modificacion,
    autor_ultima_modificacion,
    cod_prov,
    logo,
    fechaAlta,
    fechaBaja
FROM 
    spiral.dbo.Marcas

GO
---VIEW:dbo.documento_proveedor_aplicacion_v---
CREATE VIEW [dbo].[documento_proveedor_aplicacion_v] AS

CREATE VIEW [dbo].[documento_proveedor_aplicacion_v] AS
	SELECT cod_documento_proveedor AS id, empresa, punto_venta, tipo_docum, nro_documento, letra,
			factura_gastos, cod_proveedor, fecha, importe_total, importe_pendiente
	FROM dbo.documento_proveedor_c
	WHERE anulado = 'N'
UNION ALL
	SELECT cod_rendicion_gastos AS id, empresa, NULL AS punto_venta, 'REN' AS tipo_docum, cod_rendicion_gastos AS nro_documento, NULL AS letra,
			'S' factura_gastos, NULL cod_proveedor, fecha_documento AS fecha, importe_total, importe_pendiente
	FROM dbo.rendicion_de_gastos
	WHERE anulado = 'N'
UNION ALL
	SELECT nro_orden_de_pago AS id, empresa, NULL AS punto_venta, 'OP' AS tipo_docum, nro_orden_de_pago AS nro_documento, NULL AS letra,
			'N' factura_gastos, cod_proveedor, fecha_documento AS fecha, importe_total, importe_pendiente
	FROM dbo.orden_de_pago
	WHERE anulado = 'N'

GO
---VIEW:dbo.tareas_incumplidas_v---
CREATE VIEW [dbo].[tareas_incumplidas_v] AS
/*TAREAS INCUMPLIAS*/
CREATE VIEW dbo.tareas_incumplidas_v
AS
SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, 
                      dbo.Tareas_cabecera.cantidad, dbo.Tareas_detalle.cod_seccion, dbo.Orden_fabricacion.cod_articulo, dbo.articulos.denom_articulo, 
                      dbo.Orden_fabricacion.cod_color_articulo, dbo.Orden_fabricacion.version, dbo.Tareas_cabecera.tipo_tarea, dbo.Tareas_cabecera.pos_1_cant, 
                      dbo.Tareas_cabecera.pos_2_cant, dbo.Tareas_cabecera.pos_4_cant, dbo.Tareas_cabecera.pos_3_cant, dbo.Tareas_cabecera.pos_5_cant, 
                      dbo.Tareas_cabecera.pos_6_cant, dbo.Tareas_cabecera.pos_7_cant, dbo.Tareas_cabecera.pos_8_cant, dbo.Tareas_cabecera.situacion
FROM         dbo.Orden_fabricacion INNER JOIN
                      dbo.Tareas_cabecera INNER JOIN
                      dbo.Tareas_detalle ON dbo.Tareas_cabecera.nro_orden_fabricacion = dbo.Tareas_detalle.nro_orden_fabricacion AND 
                      dbo.Tareas_cabecera.nro_tarea = dbo.Tareas_detalle.nro_tarea ON 
                      dbo.Orden_fabricacion.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion INNER JOIN
                      dbo.articulos ON dbo.Orden_fabricacion.cod_articulo = dbo.articulos.cod_articulo
WHERE     (dbo.Tareas_cabecera.anulado = 'n') AND (dbo.Tareas_detalle.cumplido_paso = 'N') AND (dbo.Tareas_cabecera.situacion = 'i' OR
                      dbo.Tareas_cabecera.situacion = 'p') AND (dbo.Orden_fabricacion.anulado = 'n') AND (dbo.Tareas_cabecera.cantidad > 0)
ORDER BY dbo.Orden_fabricacion.nro_plan, dbo.Tareas_detalle.nro_tarea, dbo.Orden_fabricacion.nro_orden_fabricacion

GO
---VIEW:dbo.documento_proveedor_aplicacion_haber_v---
CREATE VIEW [dbo].[documento_proveedor_aplicacion_haber_v] AS

CREATE VIEW [dbo].[documento_proveedor_aplicacion_haber_v] AS
	SELECT * FROM documento_proveedor_aplicacion_v WHERE tipo_docum = 'OP' OR tipo_docum = 'NCR' OR tipo_docum = 'REN'
GO
---VIEW:dbo.stock_comprometido_mp_v---
CREATE VIEW [dbo].[stock_comprometido_mp_v] AS
CREATE VIEW dbo.stock_comprometido_mp_v
AS
SELECT     TOP 100 PERCENT dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material, 
                      SUM(ISNULL(dbo.Patrones_mp_detalle.consumo_par, 0) * ISNULL(dbo.tareas_incumplidas_v.cantidad, 0)) AS comprometido
FROM         dbo.tareas_incumplidas_v INNER JOIN
                      dbo.Patrones_mp_detalle ON dbo.tareas_incumplidas_v.cod_seccion = dbo.Patrones_mp_detalle.cod_seccion AND 
                      dbo.tareas_incumplidas_v.cod_articulo = dbo.Patrones_mp_detalle.cod_articulo AND 
                      dbo.tareas_incumplidas_v.cod_color_articulo = dbo.Patrones_mp_detalle.cod_color_articulo AND 
                      dbo.tareas_incumplidas_v.version = dbo.Patrones_mp_detalle.version
GROUP BY dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material
ORDER BY dbo.Patrones_mp_detalle.cod_material

GO
---VIEW:dbo.despachos_v---
CREATE VIEW [dbo].[despachos_v] AS

CREATE VIEW despachos_v AS 
SELECT
	d.nro_despacho_nro nro_despacho,
	d.nro_item_despacho nro_item,
	d.cod_empresa_despacho empresa,
	d.cod_cli cod_cliente,
	d.cod_suc_cli cod_sucursal,
	d.nro_pedido_nro nro_pedido,
	d.anulado anulado,
	d.pendiente pendiente,
	d.nro_remito nro_remito,
	d.letra_remito letra_remito,
	d.fecha_ultima_modificacion fecha_alta,
	d.cod_almacen cod_almacen,
	d.cod_articulo cod_articulo,
	d.cod_color cod_color,
	(CASE d.precio_al_facturar WHEN 'S' THEN d.precio_unitario ELSE (CASE c.lista_aplicable WHEN 'D' THEN a.precio_distribuidor ELSE a.precio_lista_mayor END) END) precio_unitario,
	d.cant_1 cant_1,
	d.cant_2 cant_2,
	d.cant_3 cant_3,
	d.cant_4 cant_4,
	d.cant_5 cant_5,
	d.cant_6 cant_6,
	d.cant_7 cant_7,
	d.cant_8 cant_8,
	d.cant_9 cant_9,
	d.cant_10 cant_10
FROM
	despachos_detalle d
INNER JOIN clientes c ON c.cod_cli = d.cod_cliente
INNER JOIN articulos a ON a.cod_articulo = d.cod_articulo
GO
---VIEW:dbo.documento_proveedor_aplicacion_debe_v---
CREATE VIEW [dbo].[documento_proveedor_aplicacion_debe_v] AS

CREATE VIEW [dbo].[documento_proveedor_aplicacion_debe_v] AS
	SELECT * FROM documento_proveedor_aplicacion_v WHERE tipo_docum = 'FAC' OR tipo_docum = 'NDB'
GO
---VIEW:dbo.presentismo---
CREATE VIEW [dbo].[presentismo] AS

CREATE VIEW presentismo AS

SELECT TOP 100 PERCENT t1.fecha, t1.legajo_nro, (CASE WHEN t2.legajo_nro IS NULL THEN 'AUSENTE' ELSE 'PRESENTE' END) presencia
FROM (
	SELECT dias.*, p.legajo_nro
	FROM (
		SELECT fecha
		FROM registro_entradas_salidas
		WHERE fecha IS NOT NULL AND fecha > dbo.toDate('01/01/2013')
		GROUP BY fecha
	) dias
	JOIN (
		SELECT legajo_nro
		FROM personal
		WHERE anulado = 'N' AND legajo_nro IS NOT NULL
	) p ON 1 = 1
) t1
LEFT JOIN registro_entradas_salidas t2
	ON t1.fecha = t2.fecha AND t1.legajo_nro = t2.legajo_nro
ORDER BY t1.fecha
GO
---VIEW:dbo.Stock_pt_detalle---
CREATE VIEW [dbo].[Stock_pt_detalle] AS

CREATE VIEW [dbo].[stock_pt_detalle] AS
SELECT	CAST(SUBSTRING(CONVERT(VARCHAR, fecha_alta, 103), 0, 11) AS DATETIME) AS fecha_movimiento,
		fecha_alta fecha_alta, cod_almacen, cod_articulo, cod_color_articulo,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_1 c_1,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_2 c_2,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_3 c_3,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_4 c_4,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_5 c_5,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_6 c_6,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_7 c_7,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_8 c_8,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_9 c_9,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_10 c_10,
		observaciones tipo_movimiento
FROM movimientos_stock
GO
---VIEW:dbo.operadores_v---
CREATE VIEW [dbo].[operadores_v] AS

CREATE VIEW operadores_v AS
	SELECT		p.cod_personal, p.legajo_nro, p.cod_categoria, p.apellido, p.nombres, p.fecha_ultima_modificacion, p.autor_ultima_modificacion, p.anulado, p.funcion, p.cuil, 
				p.fecha_antiguedad_gremio, p.fecha_ingreso, p.centro_costos, p.remun_bruta_mes, p.remun_bruta_hora, p.retrib_unidad_producida, p.costo_rem_y_cs_mes, 
				p.costo_rem_y_cs_hora, p.obra_social, p.produccion_unid_mes, p.produccion_unid_hora, p.restricciones_funcionales, p.inasistencias_acumuladas, 
				p.llegadas_tarde_acumuladas, p.fecha_egreso, p.calle, p.numero, p.piso, p.departamento, p.localidad, p.fecha_nacimiento, p.partido_departamento, p.cod_postal, 
				p.doc_identidad_tipo, p.doc_identidad_nro, p.tel_domicilio, p.tel_celular, p.e_mail, p.fax_domicilio, p.codigo_sist_anterior, p.sanciones_veces, p.provincia, 
				p.casillero_nro, p.funcion_time, p.seccion, p.ingreso_fecha, p.baja_fecha, p.retribucion_modalidad, p.valor_pares, p.valor_hora, p.valor_hora_1, p.valor_quincena, 
				p.valor_mes, p.valor_mes_1, p.dni, p.calle_transv_1, p.calle_transv_2, p.fotografia, p.situacion, p.asignar, p.liquidar_feriados, p.marca_tarjeta, 
				p.valor_hora_merienda, p.fecha_nacimiento1, p.categoria_convenio, p.faja_horaria, p.tarea_1, p.tarea_2, p.cod_faja_horaria, p.tarjeta_impresa, p.objetivo_1, 
				p.objetivo_2, p.objetivo_3, p.premio_1, p.premio_2, p.premio_3, p.cod_pais, p.cod_localidad, p.cod_localidad_nro, p.ficha, o.cod_operador, o.tipo_operador,
				o.comision_variable, o.porc_comision_vtas
	FROM		dbo.Operadores AS o
				INNER JOIN dbo.personal AS p ON o.cod_personal = p.cod_personal

GO
---VIEW:dbo.egreso_de_fondos_v---
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
---VIEW:dbo.subdiario_de_ingresos_v---
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
---VIEW:dbo.Ordenes_compra_detalle_v---
CREATE VIEW [dbo].[Ordenes_compra_detalle_v] AS



CREATE VIEW Ordenes_compra_detalle_v AS
	SELECT d.*, c.cod_proveedor, c.fecha_emision, c.es_hexagono
	FROM Ordenes_compra_cabecera c
	INNER JOIN Ordenes_compra_detalle d ON c.cod_sucursal = d.cod_sucursal AND c.nro_orden_compra = d.nro_orden_compra 


GO
---VIEW:dbo.documentos_aplicacion_v---
CREATE VIEW [dbo].[documentos_aplicacion_v] AS

	CREATE VIEW documentos_aplicacion_v AS
		SELECT empresa, punto_venta, tipo_docum, nro_documento, letra, nro_comprobante, cod_cliente, fecha_documento AS fecha, importe_total, importe_pendiente
		FROM documentos_c
		WHERE anulado = 'N'
		UNION ALL
		SELECT empresa, 1 AS punto_venta, 'REC' AS tipo_docum, nro_recibo nro_documento, 'R' AS letra, nro_recibo AS nro_comprobante, cod_cliente, fecha_documento AS fecha, importe_total, importe_pendiente
		FROM recibo
		WHERE anulado = 'N'
GO
---VIEW:dbo.stock_pedidos_pendientes_cliente_v---
CREATE VIEW [dbo].[stock_pedidos_pendientes_cliente_v] AS
CREATE VIEW dbo.stock_pedidos_pendientes_cliente_v
AS
SELECT     TOP 100 PERCENT dbo.pedidos_c.cod_cliente, dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, 
                      SUM(ISNULL(dbo.pedidos_d.pend_1, CONVERT(numeric, 0))) AS pend_1, SUM(ISNULL(dbo.pedidos_d.pend_2, CONVERT(numeric, 0))) AS pend_2, 
                      SUM(ISNULL(dbo.pedidos_d.pend_3, CONVERT(numeric, 0))) AS pend_3, SUM(ISNULL(dbo.pedidos_d.pend_4, CONVERT(numeric, 0))) AS pend_4, 
                      SUM(ISNULL(dbo.pedidos_d.pend_5, CONVERT(numeric, 0))) AS pend_5, SUM(ISNULL(dbo.pedidos_d.pend_6, CONVERT(numeric, 0))) AS pend_6, 
                      SUM(ISNULL(dbo.pedidos_d.pend_7, CONVERT(numeric, 0))) AS pend_7, SUM(ISNULL(dbo.pedidos_d.pend_8, CONVERT(numeric, 0))) AS pend_8, 
                      SUM(ISNULL(dbo.pedidos_d.pend_9, CONVERT(numeric, 0))) AS pend_9, SUM(ISNULL(dbo.pedidos_d.pend_10, CONVERT(numeric, 0))) AS pend_10, 
                      SUM(ISNULL(dbo.pedidos_d.pend_1, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_2, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_3, 
                      CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_4, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_5, CONVERT(numeric, 0)) 
                      + ISNULL(dbo.pedidos_d.pend_6, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_7, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_8, 
                      CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_9, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_10, CONVERT(numeric, 0))) 
                      AS cant_pend, dbo.pedidos_d.anulado
FROM         dbo.pedidos_c INNER JOIN
                      dbo.pedidos_d ON dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido
WHERE     (dbo.pedidos_d.pendiente > 0) AND (dbo.pedidos_c.anulado = 'N')
GROUP BY dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, dbo.pedidos_d.anulado, 
                      dbo.pedidos_c.cod_cliente
HAVING      (dbo.pedidos_d.anulado = 'n')

GO
---VIEW:dbo.valores_en_cartera_v---
CREATE VIEW [dbo].[valores_en_cartera_v] AS

CREATE VIEW valores_en_cartera_v AS
SELECT TOP 100 PERCENT c.cod_cheque, c.empresa, c.numero, banco.nombre banco_nombre, c.importe, c.fecha_vencimiento, c.cod_cliente, cl.razon_social, c.librador_nombre, c.librador_cuit, c.dias_vencimiento AS dias
FROM dbo.cheque_v AS c
LEFT JOIN Clientes cl ON c.cod_cliente = cl.cod_cli
LEFT JOIN banco ON banco.cod_banco = c.cod_banco
WHERE (c.cod_cuenta_bancaria IS NULL) AND (c.concluido = 'N') AND (c.cod_rechazo_cheque IS NULL) AND (c.anulado = 'N') AND (c.esperando_en_banco IS NULL)
ORDER BY c.fecha_vencimiento


GO
---VIEW:dbo.documentos_aplicacion_debe_v---
CREATE VIEW [dbo].[documentos_aplicacion_debe_v] AS

	CREATE VIEW documentos_aplicacion_debe_v AS
		SELECT empresa, punto_venta, tipo_docum, nro_documento, letra, nro_comprobante, cod_cliente, fecha, importe_total, importe_pendiente
		FROM documentos_aplicacion_v
		WHERE (tipo_docum = 'FAC') OR (tipo_docum = 'NDB')
GO
---VIEW:dbo.ecommerce_colores_por_articulo_v---
CREATE VIEW [dbo].[ecommerce_colores_por_articulo_v] AS

CREATE VIEW ecommerce_colores_por_articulo_v AS

SELECT
	c.cod_articulo cod_articulo,
	c.cod_color_articulo cod_color_articulo,
	c.fechaUltimaMod fecha_ultima_mod,
	c.categoria_usuario categoria_usuario,

	c.ecommerce_existe ecommerce_existe,
	c.ecommerce_fecha_ultima_sinc ecommerce_fecha_ultima_sinc,
	c.ecommerce_nombre nombre,
	c.ecommerce_info info,
	c.ecommerce_forsale forsale,
	c.ecommerce_condition condition,
	c.ecommerce_cod_category cod_category,
	c.ecommerce_exclusive exclusive,
	c.ecommerce_featured featured,
	c.ecommerce_price1 price1,
	c.ecommerce_price2 price2,
	c.ecommerce_price3 price3,
	c.ecommerce_image1 image1,

	r.posic_1 size_id_1, 0 min_stock_1, 0 replacement_stock_1, 0 max_stock_1, s.cant_1 current_stock_1,
	r.posic_2 size_id_2, 0 min_stock_2, 0 replacement_stock_2, 0 max_stock_2, s.cant_2 current_stock_2,
	r.posic_3 size_id_3, 0 min_stock_3, 0 replacement_stock_3, 0 max_stock_3, s.cant_3 current_stock_3,
	r.posic_4 size_id_4, 0 min_stock_4, 0 replacement_stock_4, 0 max_stock_4, s.cant_4 current_stock_4,
	r.posic_5 size_id_5, 0 min_stock_5, 0 replacement_stock_5, 0 max_stock_5, s.cant_5 current_stock_5,
	r.posic_6 size_id_6, 0 min_stock_6, 0 replacement_stock_6, 0 max_stock_6, s.cant_6 current_stock_6,
	r.posic_7 size_id_7, 0 min_stock_7, 0 replacement_stock_7, 0 max_stock_7, s.cant_7 current_stock_7,
	r.posic_8 size_id_8, 0 min_stock_8, 0 replacement_stock_8, 0 max_stock_8, s.cant_8 current_stock_8,
	r.posic_9 size_id_9, 0 min_stock_9, 0 replacement_stock_9, 0 max_stock_9, s.cant_9 current_stock_9,
	r.posic_10 size_id_10, 0 min_stock_10, 0 replacement_stock_10, 0 max_stock_10, s.cant_10 current_stock_10
	
FROM colores_por_articulo c
INNER JOIN articulos a ON a.cod_articulo = c.cod_articulo
INNER JOIN rango_talles r ON a.cod_rango = r.cod_rango
LEFT JOIN stock s ON s.cod_almacen = 14 AND c.cod_articulo = s.cod_articulo AND c.cod_color_articulo = s.cod_color_articulo
WHERE c.vigente = 'S'
GO
---VIEW:dbo.documentos_aplicacion_haber_v---
CREATE VIEW [dbo].[documentos_aplicacion_haber_v] AS

	CREATE VIEW documentos_aplicacion_haber_v AS
		SELECT empresa, punto_venta, tipo_docum, nro_documento, letra, nro_comprobante, cod_cliente, fecha, importe_total, importe_pendiente
		FROM documentos_aplicacion_v
		WHERE (tipo_docum = 'REC') OR (tipo_docum = 'NCR')
GO
---VIEW:dbo.stock_pt---
CREATE VIEW [dbo].[stock_pt] AS
CREATE VIEW dbo.stock_pt
AS
SELECT     s.cod_almacen, s.cod_articulo, s.cod_color_articulo, ISNULL(s.cant_1, 0) AS S1, ISNULL(s.cant_2, 0) AS S2, ISNULL(s.cant_3, 0) AS S3, 
                      ISNULL(s.cant_4, 0) AS S4, ISNULL(s.cant_5, 0) AS S5, ISNULL(s.cant_6, 0) AS S6, ISNULL(s.cant_7, 0) AS S7, ISNULL(s.cant_8, 0) AS S8, 
                      ISNULL(s.cant_9, 0) AS S9, ISNULL(s.cant_10, 0) AS S10, ISNULL(s.cantidad, 0) AS cant_s, al.denom_almacen AS nombre_almacen, 
                      a.denom_articulo AS nombre_articulo, cxa.denom_color AS nombre_color, rt.cod_rango_nro AS cod_rango, rt.denom_rango, rt.posic_1, 
                      cxa.id_tipo_producto_stock, cxa.fecha_validacion_stock, a.cod_linea, a.cod_marca, cxa.vigente, a.naturaleza, cxa.catalogo
FROM         dbo.stock s INNER JOIN
                      dbo.colores_por_articulo cxa ON s.cod_articulo = cxa.cod_articulo AND s.cod_color_articulo = cxa.cod_color_articulo INNER JOIN
                      dbo.articulos a ON s.cod_articulo = a.cod_articulo INNER JOIN
                      dbo.rango_talles rt ON rt.cod_rango = a.cod_rango INNER JOIN
                      dbo.Almacenes al ON al.cod_almacen = s.cod_almacen
WHERE     (cxa.vigente = 'S')

GO
---VIEW:dbo.documentos_h_v---
CREATE VIEW [dbo].[documentos_h_v] AS

	CREATE VIEW documentos_h_v AS
		SELECT h.*, dad.fecha fecha_debe, dah.fecha fecha_haber, dad.cod_cliente
		FROM documentos_h h
		INNER JOIN documentos dad ON
			h.empresa = dad.empresa AND
			h.madre_punto_venta = dad.punto_venta AND
			h.madre_tipo_docum = dad.tipo_docum AND
			h.madre_nro_documento = dad.numero AND
			h.madre_letra = dad.letra
		INNER JOIN documentos dah ON
			h.empresa = dah.empresa AND
			h.cancel_punto_venta = dah.punto_venta AND
			h.cancel_tipo_docum = dah.tipo_docum AND
			h.cancel_nro_documento = dah.numero AND
			h.cancel_letra = dah.letra
GO
---VIEW:dbo.tareas_incumplidas_empaque_v---
CREATE VIEW [dbo].[tareas_incumplidas_empaque_v] AS
/*TAREAS INCUMPLIAS

*/
CREATE VIEW dbo.tareas_incumplidas_empaque_v
AS
SELECT     *, nro_plan AS [plan], nro_orden_fabricacion AS op, nro_tarea AS tarea
FROM         (SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, 
                                              dbo.Tareas_cabecera.cantidad, dbo.Tareas_detalle.cod_seccion, dbo.Orden_fabricacion.cod_articulo, dbo.articulos.denom_articulo, 
                                              dbo.Orden_fabricacion.cod_color_articulo, dbo.Orden_fabricacion.version, dbo.Tareas_cabecera.tipo_tarea, 
                                              dbo.Tareas_cabecera.pos_1_cant, dbo.Tareas_cabecera.pos_2_cant, dbo.Tareas_cabecera.pos_4_cant, dbo.Tareas_cabecera.pos_3_cant, 
                                              dbo.Tareas_cabecera.pos_5_cant, dbo.Tareas_cabecera.pos_6_cant, dbo.Tareas_cabecera.pos_7_cant, 
                                              dbo.Tareas_cabecera.pos_8_cant
                       FROM          dbo.Orden_fabricacion INNER JOIN
                                              dbo.Tareas_cabecera INNER JOIN
                                              dbo.Tareas_detalle ON dbo.Tareas_cabecera.nro_orden_fabricacion = dbo.Tareas_detalle.nro_orden_fabricacion AND 
                                              dbo.Tareas_cabecera.nro_tarea = dbo.Tareas_detalle.nro_tarea ON 
                                              dbo.Orden_fabricacion.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion INNER JOIN
                                              dbo.articulos ON dbo.Orden_fabricacion.cod_articulo = dbo.articulos.cod_articulo
                       WHERE      (dbo.Tareas_cabecera.anulado = 'n') AND (dbo.Tareas_detalle.cumplido_paso = 'N') AND (dbo.Tareas_cabecera.situacion = 'i' OR
                                              dbo.Tareas_cabecera.situacion = 'p') AND (dbo.Orden_fabricacion.anulado = 'n') AND (dbo.Tareas_detalle.cod_seccion = 60)
                       ORDER BY dbo.Orden_fabricacion.nro_plan, dbo.Tareas_detalle.nro_tarea, dbo.Orden_fabricacion.nro_orden_fabricacion) tiE

GO
---VIEW:dbo.gestion_proveedores---
CREATE VIEW [dbo].[gestion_proveedores] AS

CREATE VIEW gestion_proveedores AS
	SELECT p.cod_prov, p.razon_social, p.cuit, p.imputacion_especifica, i.denominacion, p.anulado, p.observaciones_gestion, a.saldo, total_cheques
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN (
		SELECT p.cod_prov, ISNULL(SUM((CASE WHEN a.tipo_docum = 'NDB' OR a.tipo_docum = 'FAC' THEN 1 ELSE -1 END) * a.importe_pendiente), 0) saldo
		FROM proveedores_datos p
		LEFT JOIN documento_proveedor_aplicacion_v a ON p.cod_prov = a.cod_proveedor AND a.importe_pendiente > 0 AND a.factura_gastos = 'N'
		GROUP BY p.cod_prov
	) a ON p.cod_prov = a.cod_prov
	LEFT JOIN plan_cuentas i ON i.cuenta = p.imputacion_especifica
	LEFT JOIN (
		SELECT cod_proveedor, SUM(importe) total_cheques
		FROM cheque
		WHERE fecha_vencimiento >= dbo.relativeDate(GETDATE(), 'today', 0) AND anulado = 'N' AND cod_proveedor IS NOT NULL
		GROUP BY cod_proveedor
	) total_cheques ON p.cod_prov = total_cheques.cod_proveedor


GO
---VIEW:dbo.documento_proveedor_h_v---
CREATE VIEW [dbo].[documento_proveedor_h_v] AS


CREATE VIEW [dbo].[documento_proveedor_h_v] AS
	SELECT
		h.id, h.empresa, h.cod_madre, h.cod_cancel, h.tipo_docum_cancel, h.importe, h.cod_usuario, h.fecha_alta,
		dad.fecha AS fecha_debe, dah.fecha AS fecha_haber, dad.cod_proveedor, dad.factura_gastos
	FROM documento_proveedor_h h
	LEFT JOIN documento_proveedor dad ON
		h.empresa = dad.empresa AND
		h.cod_madre = dad.id AND
		(dad.tipo_docum = 'NDB' OR dad.tipo_docum = 'FAC')
	LEFT JOIN documento_proveedor dah ON
		h.empresa = dah.empresa AND
		h.cod_cancel = dah.id AND
		h.tipo_docum_cancel = dah.tipo_docum

GO
---VIEW:dbo.tareas_sin empaque_v---
CREATE VIEW [dbo].[tareas_sin empaque_v] AS
CREATE VIEW dbo.[tareas_sin empaque_v]
AS
SELECT     TOP 100 PERCENT *
FROM         (SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, 
                                              dbo.Tareas_cabecera.cantidad, MAX(dbo.Tareas_detalle.cod_seccion) AS [max sesion], dbo.Orden_fabricacion.cod_articulo, 
                                              dbo.articulos.denom_articulo, dbo.Orden_fabricacion.cod_color_articulo, dbo.Orden_fabricacion.version, dbo.Tareas_cabecera.tipo_tarea, 
                                              dbo.Tareas_cabecera.pos_1_cant, dbo.Tareas_cabecera.pos_2_cant, dbo.Tareas_cabecera.pos_4_cant, dbo.Tareas_cabecera.pos_3_cant, 
                                              dbo.Tareas_cabecera.pos_5_cant, dbo.Tareas_cabecera.pos_6_cant, dbo.Tareas_cabecera.pos_7_cant, 
                                              dbo.Tareas_cabecera.pos_8_cant
                       FROM          dbo.Orden_fabricacion INNER JOIN
                                              dbo.Tareas_cabecera INNER JOIN
                                              dbo.Tareas_detalle ON dbo.Tareas_cabecera.nro_orden_fabricacion = dbo.Tareas_detalle.nro_orden_fabricacion AND 
                                              dbo.Tareas_cabecera.nro_tarea = dbo.Tareas_detalle.nro_tarea ON 
                                              dbo.Orden_fabricacion.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion INNER JOIN
                                              dbo.articulos ON dbo.Orden_fabricacion.cod_articulo = dbo.articulos.cod_articulo
                       WHERE      (dbo.Tareas_cabecera.anulado = 'n') AND (dbo.Tareas_detalle.cumplido_paso = 'N') AND (dbo.Tareas_cabecera.situacion = 'i' OR
                                              dbo.Tareas_cabecera.situacion = 'p') AND (dbo.Orden_fabricacion.anulado = 'n')
                       GROUP BY dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, 
                                              dbo.Tareas_cabecera.cantidad, dbo.Orden_fabricacion.cod_articulo, dbo.articulos.denom_articulo, 
                                              dbo.Orden_fabricacion.cod_color_articulo, dbo.Orden_fabricacion.version, dbo.Tareas_cabecera.tipo_tarea, 
                                              dbo.Tareas_cabecera.pos_1_cant, dbo.Tareas_cabecera.pos_2_cant, dbo.Tareas_cabecera.pos_4_cant, dbo.Tareas_cabecera.pos_3_cant, 
                                              dbo.Tareas_cabecera.pos_5_cant, dbo.Tareas_cabecera.pos_6_cant, dbo.Tareas_cabecera.pos_7_cant, dbo.Tareas_cabecera.pos_8_cant) 
                      max_seccion
WHERE     ([max sesion] <> 60)
ORDER BY nro_orden_fabricacion, nro_tarea

GO
---VIEW:dbo.gestion_proveedores_1---
CREATE VIEW [dbo].[gestion_proveedores_1] AS

CREATE VIEW gestion_proveedores_1 AS
	SELECT p.cod_prov, p.razon_social, p.cuit, p.imputacion_especifica, i.denominacion, p.anulado, p.observaciones_gestion, a.saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN (
		SELECT p.cod_prov, ISNULL(SUM((CASE WHEN a.tipo_docum = 'NDB' OR a.tipo_docum = 'FAC' THEN 1 ELSE -1 END) * a.importe_pendiente), 0) saldo
		FROM proveedores_datos p
		LEFT JOIN documento_proveedor_aplicacion_v a ON p.cod_prov = a.cod_proveedor AND a.importe_pendiente > 0 AND a.factura_gastos = 'N' AND empresa = 1
		GROUP BY p.cod_prov
	) a ON p.cod_prov = a.cod_prov
	LEFT JOIN plan_cuentas i ON i.cuenta = p.imputacion_especifica

GO
---VIEW:dbo.cajas_posibles_transferencia_interna_v---
CREATE VIEW [dbo].[cajas_posibles_transferencia_interna_v] AS

CREATE VIEW [dbo].[cajas_posibles_transferencia_interna_v] AS
	SELECT cpti.*, c1.nombre nombre_caja_salida, c2.nombre nombre_caja_entrada
	FROM cajas_posibles_transferencia_interna cpti
	INNER JOIN caja c1 ON cpti.cod_caja_salida = c1.cod_caja
	INNER JOIN caja c2 ON cpti.cod_caja_entrada = c2.cod_caja
GO
---VIEW:dbo.gestion_proveedores_2---
CREATE VIEW [dbo].[gestion_proveedores_2] AS

CREATE VIEW gestion_proveedores_2 AS
	SELECT p.cod_prov, p.razon_social, p.cuit, p.imputacion_especifica, i.denominacion, p.anulado, p.observaciones_gestion, a.saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN (
		SELECT p.cod_prov, ISNULL(SUM((CASE WHEN a.tipo_docum = 'NDB' OR a.tipo_docum = 'FAC' THEN 1 ELSE -1 END) * a.importe_pendiente), 0) saldo
		FROM proveedores_datos p
		LEFT JOIN documento_proveedor_aplicacion_v a ON p.cod_prov = a.cod_proveedor AND a.importe_pendiente > 0 AND a.factura_gastos = 'N' AND empresa = 2
		GROUP BY p.cod_prov
	) a ON p.cod_prov = a.cod_prov
	LEFT JOIN plan_cuentas i ON i.cuenta = p.imputacion_especifica

GO
---VIEW:dbo.stock_mp_vw---
CREATE VIEW [dbo].[stock_mp_vw] AS

CREATE VIEW dbo.stock_mp_vw AS
SELECT	s.cod_almacen, s.cod_material, s.cod_color,
		ISNULL(s.cant_1, 0) AS S1,
		ISNULL(s.cant_2, 0) AS S2,
		ISNULL(s.cant_3, 0) AS S3,
		ISNULL(s.cant_4, 0) AS S4,
		ISNULL(s.cant_5, 0) AS S5,
		ISNULL(s.cant_6, 0) AS S6,
		ISNULL(s.cant_7, 0) AS S7,
		ISNULL(s.cant_8, 0) AS S8,
		ISNULL(s.cant_9, 0) AS S9,
		ISNULL(s.cant_10, 0) AS S10,
		ISNULL(s.cantidad, 0) AS cant_s,
		al.denom_almacen nombre_almacen, m.denom_material nombre_material, cmp.denom_color nombre_color,
		rt.cod_rango_nro cod_rango, rt.denom_rango, rt.posic_1, mp.fecha_validacion_stock
FROM stock_mp_tabla s
INNER JOIN Materias_primas mp ON s.cod_material = mp.cod_material AND s.cod_color = mp.cod_color
INNER JOIN Colores_materias_primas cmp ON s.cod_color = cmp.cod_color
INNER JOIN materiales m ON s.cod_material = m.cod_material
LEFT JOIN rango_talles rt ON rt.cod_rango = m.cod_rango
INNER JOIN almacenes al ON al.cod_almacen = s.cod_almacen
GO
---VIEW:dbo.movimientos_stock_mp_v---
CREATE VIEW [dbo].[movimientos_stock_mp_v] AS

------------------------------------------------------------------------ MOVIMIENTOS VIEW ------------------------------------------------------------------------------------------


CREATE VIEW dbo.movimientos_stock_mp_v AS
SELECT m.*, ma.denom_material nombre_material FROM movimientos_stock_mp m
LEFT JOIN materiales ma ON m.cod_material = ma.cod_material


GO
---VIEW:dbo.stock_produccion_incumplida_v---
CREATE VIEW [dbo].[stock_produccion_incumplida_v] AS
CREATE VIEW dbo.stock_produccion_incumplida_v
AS
SELECT     TOP 100 PERCENT cod_articulo, denom_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(ISNULL(pos_1_cant, 0)) AS cant_1, 
                      SUM(ISNULL(pos_2_cant, 0)) AS cant_2, SUM(ISNULL(pos_3_cant, 0)) AS cant_3, SUM(ISNULL(pos_4_cant, 0)) AS cant_4, SUM(ISNULL(pos_5_cant, 0)) 
                      AS cant_5, SUM(ISNULL(pos_6_cant, 0)) AS cant_6, SUM(ISNULL(pos_7_cant, 0)) AS cant_7, SUM(ISNULL(pos_8_cant, 0)) AS cant_8, posic_1
FROM         dbo.programacion_empaque_v
WHERE     (situacion = 'p' OR
                      situacion = 'i') AND (anulado = 'n') AND (Confirmada = 's') AND (cumplido_paso = 'n')
GROUP BY cod_articulo, denom_articulo, cod_color_articulo, posic_1

GO
---VIEW:dbo.stock_disponible_total_v---
CREATE VIEW [dbo].[stock_disponible_total_v] AS
CREATE VIEW dbo.stock_disponible_total_v
AS
SELECT     TOP 100 PERCENT dbo.colores_por_articulo.cod_articulo, dbo.articulos.denom_articulo, dbo.colores_por_articulo.cod_color_articulo, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.comercializacion_libre, dbo.colores_por_articulo.categoria_usuario, 
                      dbo.rango_talles.posic_1, SUM(ISNULL(disponible_total.cantidad, 0)) AS cantidad, SUM(ISNULL(disponible_total.cant_1, 0)) AS cant_1, 
                      SUM(ISNULL(disponible_total.cant_2, 0)) AS cant_2, SUM(ISNULL(disponible_total.cant_3, 0)) AS cant_3, SUM(ISNULL(disponible_total.cant_4, 0)) 
                      AS cant_4, SUM(ISNULL(disponible_total.cant_5, 0)) AS cant_5, SUM(ISNULL(disponible_total.cant_6, 0)) AS cant_6, 
                      SUM(ISNULL(disponible_total.cant_7, 0)) AS cant_7, SUM(ISNULL(disponible_total.cant_8, 0)) AS cant_8, dbo.colores_por_articulo.fotografia1
FROM         dbo.colores_por_articulo INNER JOIN
                      dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango ON 
                      dbo.colores_por_articulo.cod_articulo = dbo.articulos.cod_articulo LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, cantidad, cant_1, cant_2, cant_3, cant_4, cant_5, cant_6, cant_7, cant_8
                            FROM          dbo.stock_produccion_incumplida_v
                            UNION ALL
                            SELECT     cod_articulo, cod_color_articulo, cant_s, S1, S2, S3, S4, S5, S6, S7, S8
                            FROM         dbo.stock_menos_pendiente_vw
                            WHERE     (cod_almacen = '01')) disponible_total ON 
                      dbo.colores_por_articulo.cod_articulo = disponible_total.cod_articulo COLLATE Modern_Spanish_CI_AS AND 
                      dbo.colores_por_articulo.cod_color_articulo = disponible_total.cod_color_articulo COLLATE Modern_Spanish_CI_AS
GROUP BY dbo.colores_por_articulo.cod_articulo, dbo.articulos.denom_articulo, dbo.colores_por_articulo.cod_color_articulo, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.comercializacion_libre, dbo.colores_por_articulo.categoria_usuario, 
                      dbo.rango_talles.posic_1, dbo.colores_por_articulo.fotografia1, dbo.colores_por_articulo.vigente, dbo.articulos.vigente
HAVING      (dbo.colores_por_articulo.id_tipo_producto_stock <> '07') AND (dbo.colores_por_articulo.vigente = 'S') AND (dbo.articulos.vigente = 'S')
ORDER BY dbo.articulos.denom_articulo, dbo.colores_por_articulo.cod_color_articulo

GO
---VIEW:dbo.despachos_d_v---
CREATE VIEW [dbo].[despachos_d_v] AS
CREATE VIEW despachos_d_v AS 
SELECT
	d.nro_despacho nro_despacho,
	d.nro_item nro_item,
	d.empresa empresa,
	d.anulado anulado,
	cli.razon_social razon_social,
	c.cod_cliente cod_cliente,
	c.cod_sucursal cod_sucursal,
	c.cod_ecommerce_order cod_ecommerce_order, 
	d.nro_pedido nro_pedido,
	d.nro_item_pedido nro_item_pedido,
	d.cod_almacen cod_almacen,
	d.cod_articulo cod_articulo,
	d.cod_color_articulo cod_color_articulo,
	d.nro_remito nro_remito,
	d.letra_remito letra_remito,
	r.nro_factura nro_factura,
	r.punto_venta_factura punto_venta_factura,
	r.tipo_docum_factura tipo_docum_factura,
	r.letra_factura letra_factura,
	d.precio_al_facturar precio_al_facturar,
	d.descuento_pedido descuento_pedido,
	d.recargo_pedido recargo_pedido,
	d.iva_porc iva_porc,
	d.precio_unitario precio_unitario,
	d.precio_unitario_final precio_unitario_final,
	d.precio_unitario_facturar precio_unitario_facturar,
	d.precio_unitario_facturar_final precio_unitario_facturar_final,
	d.cantidad cantidad,
	d.cant_1 cant_1,
	d.cant_2 cant_2,
	d.cant_3 cant_3,
	d.cant_4 cant_4,
	d.cant_5 cant_5,
	d.cant_6 cant_6,
	d.cant_7 cant_7,
	d.cant_8 cant_8,
	d.cant_9 cant_9,
	d.cant_10 cant_10,
	d.fecha_alta fecha_alta,
	d.fecha_baja fecha_baja,
	d.fecha_ultima_mod fecha_ultima_mod,
	d.cod_usuario_baja cod_usuario_baja
FROM
	despachos_d d
LEFT JOIN remitos_c r ON d.empresa = r.empresa AND d.nro_remito = r.nro_remito AND d.letra_remito = r.letra
INNER JOIN despachos_c c ON d.empresa = c.empresa AND d.nro_despacho = c.nro_despacho
INNER JOIN clientes cli ON cli.cod_cli = c.cod_cliente
GO
---VIEW:dbo.usuarios_por_seccion_v---
CREATE VIEW [dbo].[usuarios_por_seccion_v] AS

CREATE VIEW [dbo].[usuarios_por_seccion_v] AS
SELECT a.cod_seccion, b.*
FROM usuarios_por_seccion a
INNER JOIN users b ON a.cod_usuario = b.cod_usuario
GO
---VIEW:dbo.documentos_d_v---
CREATE VIEW [dbo].[documentos_d_v] AS
CREATE VIEW documentos_d_v AS 
SELECT
	d.*,
	c.anulado,
	c.cod_cliente
FROM
	documentos_d d
LEFT JOIN documentos_c c ON d.empresa = c.empresa AND c.punto_venta = d.punto_venta AND d.tipo_docum = c.tipo_docum AND d.nro_documento = c.nro_documento AND d.letra = c.letra

GO
---VIEW:dbo.predespachos_v---
CREATE VIEW [dbo].[predespachos_v] AS

--Es para tener cliente y sucursal en los getListObject
CREATE VIEW predespachos_v AS 
SELECT
	pre.empresa empresa,
	pre.nro_pedido nro_pedido,
	pre.nro_item nro_item,
	pe.anulado anulado,
	pec.cod_ecommerce_order cod_ecommerce_order,
	pec.cod_cliente cod_cliente,
	c.razon_social razon_social,
	pec.cod_sucursal cod_sucursal,
	pre.cod_almacen cod_almacen,
	pre.cod_articulo cod_articulo,
	pre.cod_color_articulo cod_color_articulo,
	pre.predespachados predespachados,
	pre.pred_1 pred_1,
	pre.pred_2 pred_2,
	pre.pred_3 pred_3,
	pre.pred_4 pred_4,
	pre.pred_5 pred_5,
	pre.pred_6 pred_6,
	pre.pred_7 pred_7,
	pre.pred_8 pred_8,
	pre.pred_9 pred_9,
	pre.pred_10 pred_10,
	pre.tickeados tickeados,
	pre.tick_1 tick_1,
	pre.tick_2 tick_2,
	pre.tick_3 tick_3,
	pre.tick_4 tick_4,
	pre.tick_5 tick_5,
	pre.tick_6 tick_6,
	pre.tick_7 tick_7,
	pre.tick_8 tick_8,
	pre.tick_9 tick_9,
	pre.tick_10 tick_10,
	pre.fecha_alta fecha_alta,
	pre.fecha_ultima_mod fecha_ultima_mod
FROM
	predespachos pre
INNER JOIN pedidos_d pe ON pre.empresa = pe.empresa AND pre.nro_pedido = pe.nro_pedido AND pre.nro_item = pe.nro_item
INNER JOIN pedidos_c pec ON pre.empresa = pec.empresa AND pre.nro_pedido = pec.nro_pedido
INNER JOIN clientes c ON pec.cod_cliente = c.cod_cli
GO
---VIEW:dbo.proveedores_materias_primas_v---
CREATE VIEW [dbo].[proveedores_materias_primas_v] AS
CREATE VIEW proveedores_materias_primas_v AS
	SELECT pmp.*, m.denom_material
	FROM proveedores_materias_primas pmp
	INNER JOIN materiales m ON m.cod_material = pmp.cod_material


GO
---VIEW:dbo.almacenes_por_seccion_v---
CREATE VIEW [dbo].[almacenes_por_seccion_v] AS

CREATE VIEW almacenes_por_seccion_v AS
SELECT a.cod_seccion, b.*
FROM almacenes_por_seccion a
INNER JOIN almacenes b ON a.cod_almacen = b.cod_almacen
GO
---VIEW:dbo.stock_pedidos_sin_predespachar---
CREATE VIEW [dbo].[stock_pedidos_sin_predespachar] AS
/*Stock pedidos que no fueron predespachados*/
CREATE VIEW dbo.stock_pedidos_sin_predespachar
AS
SELECT     TOP 100 PERCENT dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, SUM(ISNULL(dbo.pedidos_d.pend_1, 
                      CONVERT(numeric, 0))) AS pend_1, SUM(ISNULL(dbo.pedidos_d.pend_2, CONVERT(numeric, 0))) AS pend_2, SUM(ISNULL(dbo.pedidos_d.pend_3, CONVERT(numeric, 
                      0))) AS pend_3, SUM(ISNULL(dbo.pedidos_d.pend_4, CONVERT(numeric, 0))) AS pend_4, SUM(ISNULL(dbo.pedidos_d.pend_5, CONVERT(numeric, 0))) AS pend_5, 
                      SUM(ISNULL(dbo.pedidos_d.pend_6, CONVERT(numeric, 0))) AS pend_6, SUM(ISNULL(dbo.pedidos_d.pend_7, CONVERT(numeric, 0))) AS pend_7, 
                      SUM(ISNULL(dbo.pedidos_d.pend_8, CONVERT(numeric, 0))) AS pend_8, SUM(ISNULL(dbo.pedidos_d.pend_9, CONVERT(numeric, 0))) AS pend_9, 
                      SUM(ISNULL(dbo.pedidos_d.pend_10, CONVERT(numeric, 0))) AS pend_10, SUM(ISNULL(dbo.pedidos_d.pend_1, CONVERT(numeric, 0)) 
                      + ISNULL(dbo.pedidos_d.pend_2, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_3, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_4, 
                      CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_5, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_6, CONVERT(numeric, 0)) 
                      + ISNULL(dbo.pedidos_d.pend_7, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_8, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_9, 
                      CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_10, CONVERT(numeric, 0))) AS cant_pend
FROM         dbo.pedidos_d INNER JOIN
                      dbo.pedidos_c ON dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido
WHERE     (dbo.pedidos_d.pendiente > 0) AND (dbo.pedidos_d.anulado = 'N') AND (dbo.pedidos_c.anulado = 'N')
GROUP BY dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo

GO
---VIEW:dbo.stock_pedidos_predespachados---
CREATE VIEW [dbo].[stock_pedidos_predespachados] AS


/*Stock pedidos asignados sin detalle

*/
CREATE   VIEW [dbo].[stock_pedidos_predespachados]
 AS
SELECT     TOP 100 PERCENT pre.cod_almacen, pre.cod_articulo, pre.cod_color_articulo, SUM(ISNULL(pre.pred_1, 0)) AS a1, SUM(ISNULL(pre.pred_2, 0)) AS a2, 
                      SUM(ISNULL(pre.pred_3, 0)) AS a3, SUM(ISNULL(pre.pred_4, 0)) AS a4, SUM(ISNULL(pre.pred_5, 0)) AS a5, SUM(ISNULL(pre.pred_6, 0)) AS a6, 
                      SUM(ISNULL(pre.pred_7, 0)) AS a7, SUM(ISNULL(pre.pred_8, 0)) AS a8, SUM(ISNULL(pre.pred_9, 0)) AS a9, SUM(ISNULL(pre.pred_10, 0)) AS a10, 
                      SUM(ISNULL(pre.predespachados, 0)) AS cant_a
FROM         dbo.predespachos_v pre
WHERE     (pre.anulado = 'N')
GROUP BY pre.cod_almacen, pre.cod_articulo, pre.cod_color_articulo



GO
---VIEW:dbo.promedio_tolerancia_pagados---
CREATE VIEW [dbo].[promedio_tolerancia_pagados] AS

CREATE VIEW promedio_tolerancia_pagados
AS
SELECT 
	casiPagas.documento_fecha,
	casiPagas.cod_cli,
	casiPagas.empresa,
	casiPagas.factura_nro,
	casiPagas.letra_factura,
	promedioHijas.promedio
FROM
(
	SELECT
		facturasNuevas.documento_fecha,
		facturasNuevas.cod_cli,
		facturasNuevas.empresa,
		facturasNuevas.factura_nro,
		facturasNuevas.letra_factura
	FROM
		(SELECT d.documento_fecha, d.cod_cli, d.empresa, d.factura_nro, d.letra_factura, d.total_factura FROM docum_clientes_cabecera d WHERE
			d.documento_fecha > CONVERT(DATETIME, '01/12/2011', 103) AND
			(d.anulada = 'N' OR d.anulada IS NULL) AND
			(d.tipo_docum = 'FAC' OR d.tipo_docum = 'NDB') AND
			d.grado = 'M'
		) facturasNuevas
	LEFT JOIN docum_suma_hijas hijas ON (facturasNuevas.empresa = hijas.empresa AND facturasNuevas.factura_nro = hijas.factura_nro AND facturasNuevas.letra_factura = hijas.letra_factura)
	--WHERE
		--hijas.sumaHijas > (facturasNuevas.total_factura * 0.95)
) casiPagas

LEFT JOIN
(
	SELECT
		a1.empresa empresa,
		a1.factura_nro factura_nro,
		a1.letra_factura letra_factura,
		SUM((DATEDIFF(DAY, a2.documento_fecha, 
			(CASE a1.docum_cancel_tipo WHEN 'NCR' THEN (a2.documento_fecha) WHEN 'REC' THEN (a3.fecha_recibo) ELSE a1.fecha_docum_anulacion END))
			 * (a1.total_factura / a2.total_factura))) promedio
	FROM
		docum_clientes_cabecera a1
	LEFT JOIN
		docum_clientes_cabecera a2 ON (
			a1.empresa = a2.empresa AND a1.factura_nro = a2.factura_nro AND 
			a1.letra_factura = a2.letra_factura AND a2.grado = 'M' AND 
			a1.tipo_docum = a2.tipo_docum AND
			(a2.anulada = 'N' OR a2.anulada IS NULL)
		)
	LEFT JOIN
		recibo_cabecera a3 ON (
			a1.empresa = a3.empresa AND a1.docum_cancel_nro = a3.recibo_nro AND 
			(a3.anulado = 'N' OR a3.anulado IS NULL)
		)
	WHERE
		a1.documento_fecha > CONVERT(DATETIME, '01/12/2011', 103) AND
		a1.grado = 'H' AND 
		(a1.tipo_docum = 'FAC' OR a1.tipo_docum = 'NDB')
	GROUP BY a1.empresa, a1.factura_nro, a1.letra_factura
) promedioHijas
ON
casiPagas.empresa = promedioHijas.empresa AND
casiPagas.factura_nro = promedioHijas.factura_nro AND
casiPagas.letra_factura = promedioHijas.letra_factura

WHERE promedio IS NOT NULL

GO
---VIEW:dbo.stock_pt_real_v---
CREATE VIEW [dbo].[stock_pt_real_v] AS
CREATE VIEW dbo.stock_pt_real_v AS select	alm.cod_almacen + ' - ' + alm.denom_almacen as "Almacen",
		art.cod_articulo + ' - ' + art.denom_articulo as "Articulo",
		cxa.cod_color_articulo + ' - ' + cxa.denom_color as "Color",
		ccu.denom_categoria as "Categoria",
		tps.denom_tipo_producto as "Tipo",
		stk.cantidad as "Total",
		stk.cant_1 as "Cantidad 1",
		stk.cant_2 as "Cantidad 2",
		stk.cant_3 as "Cantidad 3",
		stk.cant_4 as "Cantidad 4",
		stk.cant_5 as "Cantidad 5",
		stk.cant_6 as "Cantidad 6",
		stk.cant_7 as "Cantidad 7",
		stk.cant_8 as "Cantidad 8",
		stk.cant_9 as "Cantidad 9",
		stk.cant_10 as "Cantidad 10"
from	almacenes alm,
		articulos art,
		colores_por_articulo cxa,
		categorias_calzado_usuarios ccu,
		stock stk,
		tipo_producto_stock tps
where	art.cod_articulo = cxa.cod_articulo
and		cxa.categoria_usuario = ccu.cod_categoria
and		alm.cod_almacen = stk.cod_almacen
and		art.cod_articulo = stk.cod_articulo
and		cxa.cod_color_articulo = stk.cod_color_articulo
and		cxa.id_tipo_producto_stock = tps.id_tipo_producto_stock
GO
---VIEW:dbo.cajas_resumen---
CREATE VIEW [dbo].[cajas_resumen] AS

CREATE VIEW cajas_resumen AS 

SELECT 
	fecha,
	(
		CASE
		WHEN (SELECT 1 FROM caja_saldos_diarios WHERE caja_fecha = fecha) = 1 THEN 'S'
		ELSE 'N'
		END
	) caja_cerrada,
	SUM(cd_importe_1) cd_importe_1,
	SUM(cd_importe_2) cd_importe_2,
	SUM(cd_importe_3) cd_importe_3,
	SUM(cd_importe_4) cd_importe_4,
	SUM(cd_importe_5) cd_importe_5,
	SUM(cd_importe_6) cd_importe_6,
	SUM(oi_importe_1) oi_importe_1,
	SUM(oi_importe_2) oi_importe_2,
	SUM(oi_importe_3) oi_importe_3,
	SUM(oi_importe_4) oi_importe_4,
	SUM(oi_importe_5) oi_importe_5,
	SUM(oi_importe_6) oi_importe_6,
	SUM(db_importe_1) db_importe_1,
	SUM(db_importe_2) db_importe_2,
	SUM(op_importe_1) op_importe_1,
	SUM(op_importe_2) op_importe_2,
	SUM(gr_importe_1) gr_importe_1,
	SUM(cc_importe_1) cc_importe_1,
	SUM(cc_importe_2) cc_importe_2,
	SUM(cc_importe_3) cc_importe_3,
	SUM(cc_importe_4) cc_importe_4,
	SUM(cc_importe_5) cc_importe_5,
	SUM(cc_importe_6) cc_importe_6,
	SUM(cc_importe_total) cc_importe_total
FROM
(
	--Cobranza Deudores (los CD de documentos)
	SELECT
		fecha_caja fecha,
		COALESCE(SUM(do.importe_1), 0) cd_importe_1,
		COALESCE(SUM(do.importe_2), 0) cd_importe_2,
		COALESCE(SUM(do.importe_3), 0) cd_importe_3,
		COALESCE(SUM(do.importe_4), 0) cd_importe_4,
		COALESCE(SUM(do.importe_5), 0) cd_importe_5,
		COALESCE(SUM(do.importe_6), 0) cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM documentos do
	WHERE
		fecha_caja IS NOT NULL AND
		tipo_docum = 'REC'	AND
		operacion_tipo = 'CD'
	GROUP BY fecha_caja

	UNION

	--Otros Ingresos (los OI de documentos)
	SELECT
		fecha_caja fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		COALESCE(SUM(do2.importe_1), 0) oi_importe_1,
		COALESCE(SUM(do2.importe_2), 0) oi_importe_2,
		COALESCE(SUM(do2.importe_3), 0) oi_importe_3,
		COALESCE(SUM(do2.importe_4), 0) oi_importe_4,
		COALESCE(SUM(do2.importe_5), 0) oi_importe_5,
		COALESCE(SUM(do2.importe_6), 0) oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM documentos do2
	WHERE
		fecha_caja IS NOT NULL AND
		tipo_docum = 'REC'	AND
		operacion_tipo = 'OI'
	GROUP BY fecha_caja

	UNION

	--Depósitos Bancarios Efectivo (los BD y E de bancos_depositos_cabecera)
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		COALESCE(SUM(ba1.total_boleta_deposito), 0) db_importe_1,
		0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM bancos_depositos_cabecera ba1
	WHERE
		caja_fecha IS NOT NULL AND
		tipo_docum_u_operacion = 'BD' AND
		tipo_valor = 'E'
	GROUP BY caja_fecha

	UNION

	--Depósitos Bancarios Cheques (los BD y C de bancos_depositos_cabecera)
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1,
		COALESCE(SUM(ba2.total_boleta_deposito), 0) db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM bancos_depositos_cabecera ba2
	WHERE
		caja_fecha IS NOT NULL AND
		ti
po_docum_u_operacion = 'BD' AND
		tipo_valor = 'C'
	GROUP BY caja_fecha

	UNION

	--Órdenes de pago
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		COALESCE(SUM(op.imp_entreg_en_valor_1), 0) op_importe_1,
		COALESCE(SUM(op.imp_entreg_en_valor_2), 0) op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM ordenes_de_pago op
	WHERE
		caja_fecha IS NOT NULL AND
		operacion_tipo = 'OP'
	GROUP BY caja_fecha

	UNION

	--Gastos por Rendición varios (de gastos_rendicion)
	SELECT
		fecha_rendicion fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		COALESCE(SUM(gr.total_importe), 0) gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM gastos_rendicion gr
	WHERE
		fecha_rendicion IS NOT NULL
	GROUP BY fecha_rendicion

	UNION

	--Cajas Cerradas
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		cc.importe_recibido_en_valor_1 cc_importe_1,
		cc.importe_recibido_en_valor_2 cc_importe_2,
		cc.importe_recibido_en_valor_3 cc_importe_3,
		cc.importe_recibido_en_valor_4 cc_importe_4,
		cc.importe_recibido_en_valor_5 cc_importe_5,
		cc.importe_recibido_en_valor_6 cc_importe_6,
		ISNULL(cc.importe_recibido_en_valor_1, 0) + 
			ISNULL(cc.importe_recibido_en_valor_2, 0) + 
			ISNULL(cc.importe_recibido_en_valor_3, 0) + 
			ISNULL(cc.importe_recibido_en_valor_4, 0) + 
			ISNULL(cc.importe_recibido_en_valor_5, 0) + 
			ISNULL(cc.importe_recibido_en_valor_6, 0)
			cc_importe_total
	FROM caja_saldos_diarios cc
	WHERE
		caja_fecha IS NOT NULL

) a
GROUP BY fecha
GO
---VIEW:dbo.facturas_con_saldo_pendientexxx---
CREATE VIEW [dbo].[facturas_con_saldo_pendientexxx] AS
CREATE VIEW dbo.facturas_con_saldo_pendiente
AS
SELECT     empresa, tipo_docum, factura_nro, letra_factura, cod_cli, saldo_pendiente, documento_fecha, pagado_total, desactivado_aplicado, grado
FROM         dbo.docum_clientes_cabecera
WHERE     (saldo_pendiente > 0) AND (pagado_total = N'n') AND (desactivado_aplicado = N'n') AND (grado = N'm')

GO
---VIEW:dbo.Stock_pt_real_valorizado_v---
CREATE VIEW [dbo].[Stock_pt_real_valorizado_v] AS
CREATE VIEW dbo.Stock_pt_real_valorizado_v
AS
SELECT     TOP 100 PERCENT dbo.stock_pt.cod_almacen, dbo.stock_pt.nombre_almacen, dbo.stock_pt.cod_articulo, dbo.stock_pt.nombre_articulo, 
                      dbo.stock_pt.cod_color_articulo, dbo.stock_pt.cant_s, dbo.stock_pt.cod_linea, dbo.stock_pt.cod_marca, dbo.stock_pt.id_tipo_producto_stock, 
                      dbo.costo_producto_total_V.costo, dbo.costo_producto_total_V.costo_linea, dbo.costo_producto_total_V.costo_total
FROM         dbo.costo_producto_total_V RIGHT OUTER JOIN
                      dbo.stock_pt ON dbo.costo_producto_total_V.cod_articulo = dbo.stock_pt.cod_articulo AND 
                      dbo.costo_producto_total_V.cod_color_articulo = dbo.stock_pt.cod_color_articulo
ORDER BY dbo.stock_pt.cod_articulo

GO
---VIEW:dbo.facturas_detalle---
CREATE VIEW [dbo].[facturas_detalle] AS

CREATE  VIEW dbo.facturas_detalle
AS
SELECT     TOP 100 PERCENT dbo.Despachos_detalle.nro_despacho_nro AS nro_despacho, dbo.Despachos_detalle.nro_item_despacho AS nro_item, 
                      dbo.Despachos_detalle.cod_empresa_despacho AS empresa, dbo.Despachos_cabecera.cod_cli AS cod_cliente, 
                      dbo.Despachos_cabecera.cod_suc AS cod_sucursal, dbo.Despachos_detalle.nro_pedido_nro AS nro_pedido, dbo.Despachos_detalle.anulado, 
                      dbo.Despachos_detalle.pendiente, dbo.remitos_c.nro_remito, dbo.remitos_c.letra AS letra_remito, 
                      dbo.Despachos_detalle.fecha_ultima_modificacion AS fecha_alta, dbo.Despachos_detalle.cod_almacen, dbo.Despachos_detalle.cod_articulo, 
                      dbo.Despachos_detalle.cod_color, 
                      CASE lista_aplicable WHEN 'D' THEN (CASE precio_al_facturar WHEN 'N' THEN Precio_unitario ELSE precio_distrib END) 
                      ELSE (CASE precio_al_facturar WHEN 'N' THEN Precio_unitario ELSE precio_mayorista_usd END) END AS precio, dbo.remitos_c.nro_factura, 
                      dbo.remitos_c.letra_factura, ISNULL(dbo.Despachos_detalle.cant_1, 0) + ISNULL(dbo.Despachos_detalle.cant_2, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_3, 0) + ISNULL(dbo.Despachos_detalle.cant_4, 0) + ISNULL(dbo.Despachos_detalle.cant_5, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_6, 0) + ISNULL(dbo.Despachos_detalle.cant_7, 0) + ISNULL(dbo.Despachos_detalle.cant_8, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_9, 0) + ISNULL(dbo.Despachos_detalle.cant_10, 0) AS Cantidad, dbo.Despachos_detalle.precio_unitario, 
                      (ISNULL(dbo.Despachos_detalle.cant_1, 0) + ISNULL(dbo.Despachos_detalle.cant_2, 0) + ISNULL(dbo.Despachos_detalle.cant_3, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_4, 0) + ISNULL(dbo.Despachos_detalle.cant_5, 0) + ISNULL(dbo.Despachos_detalle.cant_6, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_7, 0) + ISNULL(dbo.Despachos_detalle.cant_8, 0) + ISNULL(dbo.Despachos_detalle.cant_9, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_10, 0)) 
                      * (CASE lista_aplicable WHEN 'D' THEN (CASE precio_al_facturar WHEN 'N' THEN Precio_unitario ELSE precio_distrib END) 
                      ELSE (CASE precio_al_facturar WHEN 'N' THEN Precio_unitario ELSE precio_mayorista_usd END) END) AS importe_total, 
                      dbo.Despachos_detalle.precio_al_facturar, dbo.colores_por_articulo.precio_distrib, dbo.colores_por_articulo.precio_mayorista_usd, 
                      dbo.Clientes.lista_aplicable, dbo.Despachos_detalle.cant_1, dbo.Despachos_detalle.cant_2, dbo.Despachos_detalle.cant_3, 
                      dbo.Despachos_detalle.cant_4, dbo.Despachos_detalle.cant_5, dbo.Despachos_detalle.cant_6, dbo.Despachos_detalle.cant_7, 
                      dbo.Despachos_detalle.cant_8, dbo.Despachos_detalle.cant_9, dbo.Despachos_detalle.cant_10
FROM         dbo.Clientes INNER JOIN
                      dbo.Despachos_cabecera ON dbo.Clientes.cod_cli = dbo.Despachos_cabecera.cod_cli RIGHT OUTER JOIN
                      dbo.Despachos_detalle INNER JOIN
                      dbo.colores_por_articulo ON dbo.Despachos_detalle.cod_articulo = dbo.colores_por_articulo.cod_articulo AND 
                      dbo.Despachos_detalle.cod_color = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.Despachos_cabecera.nro_despacho = dbo.Despachos_detalle.nro_despacho AND 
                      dbo.Despachos_cabecera.cod_sucursal_despacho = dbo.Despachos_detalle.cod_sucursal_despacho AND 
                      dbo.Despachos_cabecera.cod_empresa_despacho = dbo.Despachos_detalle.cod_empresa_despacho LEFT OUTER JOIN
                      dbo.remitos_c ON dbo.Despachos_detalle.nro_remito = dbo.remitos_c.nro_remito AND dbo.Despachos_detalle.letra_remito = dbo.remitos_c.letra AND 

                      dbo.Despachos_detalle.cod_empresa_despacho = dbo.remitos_c.empresa
WHERE     (dbo.Despachos_detalle.anulado = 'N')


GO
---VIEW:dbo.asignados_por_tarea---
CREATE VIEW [dbo].[asignados_por_tarea] AS
CREATE VIEW dbo.asignados_por_tarea
AS
SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan AS Lote, dbo.Tareas_cabecera.nro_orden_fabricacion, dbo.Tareas_cabecera.nro_tarea, 
                      dbo.Orden_fabricacion.cod_articulo, dbo.Orden_fabricacion.cod_color_articulo, dbo.Tareas_cabecera.fecha_programacion, 
                      dbo.Tareas_cabecera.cantidad - ISNULL(a.cant_a, 0) AS Disponible, dbo.Tareas_cabecera.cantidad AS cant, ISNULL(dbo.Tareas_cabecera.pos_1_cant, 
                      0) AS c1, ISNULL(dbo.Tareas_cabecera.pos_2_cant, 0) AS c2, ISNULL(dbo.Tareas_cabecera.pos_3_cant, 0) AS c3, 
                      ISNULL(dbo.Tareas_cabecera.pos_4_cant, 0) AS c4, ISNULL(dbo.Tareas_cabecera.pos_5_cant, 0) AS c5, ISNULL(dbo.Tareas_cabecera.pos_6_cant, 0) 
                      AS c6, ISNULL(dbo.Tareas_cabecera.pos_7_cant, 0) AS c7, ISNULL(dbo.Tareas_cabecera.pos_8_cant, 0) AS c8, 
                      ISNULL(dbo.Tareas_cabecera.pos_9_cant, 0) AS c9, ISNULL(dbo.Tareas_cabecera.pos_10_cant, 0) AS c10, ISNULL(a.cant_a, 0) AS cant_a, 
                      ISNULL(a.a_1, 0) AS a1, ISNULL(a.a_2, 0) AS a2, ISNULL(a.a_3, 0) AS a3, ISNULL(a.a_4, 0) AS a4, ISNULL(a.a_5, 0) AS a5, ISNULL(a.a_6, 0) AS a6, 
                      ISNULL(a.a_7, 0) AS a7, ISNULL(a.a_8, 0) AS a8, ISNULL(a.a_9, 0) AS a9, ISNULL(a.a_10, 0) AS a10
FROM         dbo.Orden_fabricacion INNER JOIN
                      dbo.Tareas_cabecera ON dbo.Orden_fabricacion.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion LEFT OUTER JOIN
                          (SELECT     TOP 100 PERCENT dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, 
                                                   dbo.asignacion_pedidos.nro_tarea, dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pedidos.cod_color_articulo, 
                                                   SUM(dbo.asignacion_pedidos.cantidad) AS cant_a, SUM(dbo.asignacion_pedidos.a_1) AS a_1, SUM(dbo.asignacion_pedidos.a_2) AS a_2,
                                                    SUM(dbo.asignacion_pedidos.a_3) AS a_3, SUM(dbo.asignacion_pedidos.a_4) AS a_4, SUM(dbo.asignacion_pedidos.a_5) AS a_5, 
                                                   SUM(dbo.asignacion_pedidos.a_6) AS a_6, SUM(dbo.asignacion_pedidos.a_7) AS a_7, SUM(dbo.asignacion_pedidos.a_8) AS a_8, 
                                                   SUM(dbo.asignacion_pedidos.a_9) AS a_9, SUM(dbo.asignacion_pedidos.a_10) AS a_10, 
                                                   dbo.asignacion_pedidos.fecha_original_programada, dbo.asignacion_pedidos.asignado
                            FROM          dbo.pedidos_detalle INNER JOIN
                                                   dbo.pedidos_cabecera ON dbo.pedidos_detalle.cod_empresa = dbo.pedidos_cabecera.cod_empresa AND 
                                                   dbo.pedidos_detalle.cod_sucursal = dbo.pedidos_cabecera.cod_sucursal AND 
                                                   dbo.pedidos_detalle.nro_pedido = dbo.pedidos_cabecera.nro_pedido INNER JOIN
                                                   dbo.asignacion_pedidos ON dbo.pedidos_detalle.nro_pedido_nro = dbo.asignacion_pedidos.nro_pedido AND 
                                                   dbo.pedidos_detalle.nro_item = dbo.asignacion_pedidos.nro_item
                            WHERE      (dbo.pedidos_detalle.cantidad_pendiente > 0) AND (dbo.pedidos_detalle.anulado = 'N') AND (dbo.pedidos_cabecera.anulado = 'N')
                            GROUP BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea, 
                                                   dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pedidos.cod_color_articulo, dbo.asignacion_pedidos.fecha_original_programada, 
                                                   dbo.asignacion_pedidos.asignado
              
              ORDER BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea) a ON 
                      dbo.Tareas_cabecera.nro_orden_fabricacion = a.nro_orden_fabricacion AND dbo.Tareas_cabecera.nro_tarea = a.nro_tarea
WHERE     (dbo.Tareas_cabecera.situacion = 'P' OR
                      dbo.Tareas_cabecera.situacion = 'I') AND (dbo.Tareas_cabecera.anulado = 'N') AND (dbo.Orden_fabricacion.anulado = 'N') AND 
                      (dbo.Orden_fabricacion.nro_plan > 0) AND (dbo.Tareas_cabecera.tipo_tarea IS NULL) AND (dbo.Tareas_cabecera.cantidad > 0)
ORDER BY dbo.Orden_fabricacion.nro_plan, dbo.Tareas_cabecera.nro_orden_fabricacion, dbo.Tareas_cabecera.fecha_programacion

GO
---VIEW:dbo.tranferencias_materias_primas_v---
CREATE VIEW [dbo].[tranferencias_materias_primas_v] AS
CREATE VIEW dbo.tranferencias_materias_primas_v
AS
SELECT     TOP 100 PERCENT fecha_alta AS fecha_Movimiento, cod_material, cod_color, cantidad, cant_1, cant_2, cant_3, cant_4, cant_5, cant_6, cant_7, cant_8, 
                      cant_9, cant_10, cod_almacen, motivo, Nro_operacion, efecto_movimiento
FROM         (SELECT     dbo.tranferencias_materias_primas_c.fecha_alta, dbo.tranferencias_materias_primas_c.almacen_destino AS cod_almacen, 
                                              dbo.tranferencias_materias_primas_d.cod_material, dbo.tranferencias_materias_primas_d.cod_color, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cantidad, 0) AS cantidad, ISNULL(dbo.tranferencias_materias_primas_d.cant_1, 0) AS cant_1, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_2, 0) AS cant_2, ISNULL(dbo.tranferencias_materias_primas_d.cant_3, 0) AS cant_3, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_4, 0) AS cant_4, ISNULL(dbo.tranferencias_materias_primas_d.cant_5, 0) AS cant_5, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_6, 0) AS cant_6, ISNULL(dbo.tranferencias_materias_primas_d.cant_7, 0) AS cant_7, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_8, 0) AS cant_8, ISNULL(dbo.tranferencias_materias_primas_d.cant_9, 0) AS cant_9, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_10, 0) AS cant_10, 
                                              dbo.tranferencias_materias_primas_c.nro_tranferencia_mp AS Nro_operacion, 'E' AS efecto_movimiento, 'TRANF POS' AS motivo
                       FROM          dbo.tranferencias_materias_primas_d INNER JOIN
                                              dbo.tranferencias_materias_primas_c ON 
                                              dbo.tranferencias_materias_primas_d.nro_tranferencia_mp = dbo.tranferencias_materias_primas_c.nro_tranferencia_mp
                       UNION ALL
                       SELECT     dbo.tranferencias_materias_primas_c.fecha_alta, dbo.tranferencias_materias_primas_c.almacen_origen AS cod_almacen, 
                                             dbo.tranferencias_materias_primas_d.cod_material, dbo.tranferencias_materias_primas_d.cod_color, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cantidad, 0) AS Expr1, - ISNULL(dbo.tranferencias_materias_primas_d.cant_1, 0) AS Expr2, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_2, 0) AS Expr3, - ISNULL(dbo.tranferencias_materias_primas_d.cant_3, 0) AS Expr4, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_4, 0) AS Expr5, - ISNULL(dbo.tranferencias_materias_primas_d.cant_5, 0) AS Expr6, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_6, 0) AS Expr7, - ISNULL(dbo.tranferencias_materias_primas_d.cant_7, 0) AS Expr8, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_8, 0) AS Expr9, - ISNULL(dbo.tranferencias_materias_primas_d.cant_9, 0) AS cant_9, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_10, 0) AS cant_10, 
                                             dbo.tranferencias_materias_primas_c.nro_tranferencia_mp AS Nro_operacion, 'S' AS efecto_movimiento, 'TRANF NEG' AS motivo
                       FROM         dbo.tranferencias_materias_primas_d INNER JOIN
                                             dbo.tranferencias_materias_primas_c ON 
                                             dbo.tranferencias_materias_primas_d.nro_tranferencia_mp = dbo.tranferencias_materias_primas_c.nro_tranferencia_mp) tr

GO
---VIEW:dbo.mp_mov_extraor_vw---
CREATE VIEW [dbo].[mp_mov_extraor_vw] AS
CREATE VIEW dbo.mp_mov_extraor_vw
AS
SELECT     fecha_movimiento, cod_material, cod_color_material AS cod_color, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cantidad, 0) 
                      AS cantidad, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_1, 0) AS c1, 
                      (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_2, 0) AS c2, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) 
                      * ISNULL(cant_3, 0) AS c3, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_4, 0) AS c4, 
                      (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_5, 0) AS c5, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) 
                      * ISNULL(cant_6, 0) AS c6, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_7, 0) AS c7, 
                      (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_8, 0) AS c8, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) 
                      * ISNULL(cant_9, 0) AS c9, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_10, 0) AS c10, cod_almacen, 'Ajuste' AS Motivo, 
                      clave_tabla AS nro_operacion, efecto_movimiento
FROM         dbo.materias_primas_movim_extraor

GO
---VIEW:dbo.pendientes_aplicacion_clientes_v---
CREATE VIEW [dbo].[pendientes_aplicacion_clientes_v] AS
CREATE VIEW [dbo].[pendientes_aplicacion_clientes_v] AS
	SELECT	d.cod_cliente, c.razon_social, d.empresa, d.fecha_documento,
			dbo.sumarTiempo(d.fecha_documento, 'dia', 60) fecha_vencimiento,
			d.tipo_docum, d.letra, d.nro_documento, d.observaciones,
			d.importe_total, d.importe_pendiente
	FROM	documentos_c d
			INNER JOIN clientes c ON c.cod_cli = d.cod_cliente
	WHERE	d.tipo_docum IN ('FAC', 'NDB', 'NCR') AND d.importe_pendiente > 0
			AND d.anulado = 'N'

	UNION ALL

	SELECT	r.cod_cliente, (CASE WHEN r.cod_cliente IS NULL THEN r.recibido_de ELSE c.razon_social END), r.empresa, r.fecha_documento, NULL fecha_vencimiento,
			'REC' tipo_docum, 'R' letra, r.nro_recibo nro_documento, r.observaciones,
			r.importe_total, r.importe_pendiente
	FROM	recibo r
			LEFT JOIN clientes c ON c.cod_cli = r.cod_cliente
	WHERE	r.importe_pendiente > 0 AND r.anulado = 'N'
GO
---VIEW:dbo.stock_asignados---
CREATE VIEW [dbo].[stock_asignados] AS
CREATE VIEW dbo.stock_asignados
AS
SELECT     TOP 100 PERCENT dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea, 
                      dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pedidos.cod_color_articulo, SUM(dbo.asignacion_pedidos.cantidad) AS cant_a, 
                      SUM(dbo.asignacion_pedidos.a_1) AS a_1, SUM(dbo.asignacion_pedidos.a_2) AS a_2, SUM(dbo.asignacion_pedidos.a_3) AS a_3, 
                      SUM(dbo.asignacion_pedidos.a_4) AS a_4, SUM(dbo.asignacion_pedidos.a_5) AS a_5, SUM(dbo.asignacion_pedidos.a_6) AS a_6, 
                      SUM(dbo.asignacion_pedidos.a_7) AS a_7, SUM(dbo.asignacion_pedidos.a_8) AS a_8, SUM(dbo.asignacion_pedidos.a_9) AS a_9, 
                      SUM(dbo.asignacion_pedidos.a_10) AS a_10, dbo.asignacion_pedidos.fecha_original_programada, dbo.asignacion_pedidos.asignado
FROM         dbo.pedidos_detalle INNER JOIN
                      dbo.pedidos_cabecera ON dbo.pedidos_detalle.cod_empresa = dbo.pedidos_cabecera.cod_empresa AND 
                      dbo.pedidos_detalle.cod_sucursal = dbo.pedidos_cabecera.cod_sucursal AND 
                      dbo.pedidos_detalle.nro_pedido = dbo.pedidos_cabecera.nro_pedido INNER JOIN
                      dbo.asignacion_pedidos ON dbo.pedidos_detalle.nro_pedido_nro = dbo.asignacion_pedidos.nro_pedido AND 
                      dbo.pedidos_detalle.nro_item = dbo.asignacion_pedidos.nro_item
WHERE     (dbo.pedidos_detalle.cantidad_pendiente > 0) AND (dbo.pedidos_detalle.anulado = 'N') AND (dbo.pedidos_cabecera.anulado = 'N')
GROUP BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea, 
                      dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pedidos.cod_color_articulo, dbo.asignacion_pedidos.fecha_original_programada, 
                      dbo.asignacion_pedidos.asignado
ORDER BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea

GO
---VIEW:dbo.mp_remitos_vw---
CREATE VIEW [dbo].[mp_remitos_vw] AS
/*SELECT     dbo.Remitos_proveedor_cabecera.fecha_recepcion AS fecha_movimiento, dbo.remitos_proveedor_detalle.cod_material, dbo.remitos_proveedor_detalle.cod_color, 
                      ISNULL(dbo.remitos_proveedor_detalle.cantidad, 0) * dbo.materiales.factor_conversion AS cant, ISNULL(dbo.remitos_proveedor_detalle.cant_1, 0) 
                      * dbo.materiales.factor_conversion AS c1, ISNULL(dbo.remitos_proveedor_detalle.cant_2, 0) * dbo.materiales.factor_conversion AS c2, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_3, 0) * dbo.materiales.factor_conversion AS c3, ISNULL(dbo.remitos_proveedor_detalle.cant_4, 0) 
                      * dbo.materiales.factor_conversion AS c4, ISNULL(dbo.remitos_proveedor_detalle.cant_5, 0) * dbo.materiales.factor_conversion AS c5, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_6, 0) * dbo.materiales.factor_conversion AS c6, ISNULL(dbo.remitos_proveedor_detalle.cant_7, 0) 
                      * dbo.materiales.factor_conversion AS c7, ISNULL(dbo.remitos_proveedor_detalle.cant_8, 0) * dbo.materiales.factor_conversion AS c8, '01' AS cod_almacen, 
                      'Rto:' + CAST(dbo.Remitos_proveedor_cabecera.cod_proveedor AS varchar) + ' - ' + dbo.Remitos_proveedor_cabecera.nro_compuesto_remito AS motivo
FROM         dbo.Remitos_proveedor_cabecera INNER JOIN
                      dbo.remitos_proveedor_detalle ON dbo.Remitos_proveedor_cabecera.cod_proveedor = dbo.remitos_proveedor_detalle.cod_proveedor AND 
                      dbo.Remitos_proveedor_cabecera.nro_compuesto_remito = dbo.remitos_proveedor_detalle.nro_compuesto_remito INNER JOIN
                      dbo.materiales ON dbo.remitos_proveedor_detalle.cod_material = dbo.materiales.cod_material
*/
CREATE VIEW dbo.mp_remitos_vw
AS
SELECT     dbo.Remitos_proveedor_cabecera.fecha_recepcion AS fecha_movimiento, dbo.remitos_proveedor_detalle.cod_material, 
                      dbo.remitos_proveedor_detalle.cod_color, ISNULL(dbo.remitos_proveedor_detalle.cantidad, 0) AS cantidad, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_1, 0) AS c1, ISNULL(dbo.remitos_proveedor_detalle.cant_2, 0) AS c2, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_3, 0) AS c3, ISNULL(dbo.remitos_proveedor_detalle.cant_4, 0) AS c4, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_5, 0) AS c5, ISNULL(dbo.remitos_proveedor_detalle.cant_6, 0) AS c6, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_7, 0) AS c7, ISNULL(dbo.remitos_proveedor_detalle.cant_8, 0) AS c8, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_9, 0) AS c9, ISNULL(dbo.remitos_proveedor_detalle.cant_10, 0) AS c10, 
                      dbo.Remitos_proveedor_cabecera.cod_almacen_recepcion AS cod_almacen, 'Remito' AS motivo, 
                      dbo.Remitos_proveedor_cabecera.nro_remito AS nro_operacion, 'S' AS efecto_movimiento
FROM         dbo.Remitos_proveedor_cabecera INNER JOIN
                      dbo.remitos_proveedor_detalle ON dbo.Remitos_proveedor_cabecera.cod_proveedor = dbo.remitos_proveedor_detalle.cod_proveedor AND 
                      dbo.Remitos_proveedor_cabecera.nro_compuesto_remito = dbo.remitos_proveedor_detalle.nro_compuesto_remito INNER JOIN
                      dbo.materiales ON dbo.remitos_proveedor_detalle.cod_material = dbo.materiales.cod_material

GO
---VIEW:dbo.fasonier_v---
CREATE VIEW [dbo].[fasonier_v] AS
CREATE VIEW dbo.fasonier_v AS
SELECT P.*, o.cod_operador ,o.tipo_operador FROM operadores o
INNER JOIN proveedores_datos p ON o.cod_proveedor = p.cod_prov

GO
---VIEW:dbo.VIEW3---
CREATE VIEW [dbo].[VIEW3] AS
CREATE VIEW dbo.VIEW3
AS
SELECT     TOP 100 PERCENT dbo.colores_por_articulo.catalogo_orden_pagina AS [Position], 'Spiral' AS Manufacturer, '' AS Supplier, 
                      dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_d.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo
                       + '_e.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_i.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo
                       + '_t.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_a.jpg' AS [Product Images], 
                      dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' AS [Product Images Caption], 
                      dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' AS [Product Images Caption AG], 
                      'AR Standard rate (21%)' AS Tax, '' AS [Product Carrier], '' AS [Product Accessories], '' AS [Attribute Group Tamaño], 
                      dbo.colores_por_articulo.denom_color AS [Attribute Group Color], '' AS [Attribute Group Dimension], 
                      dbo.stock_01_14_20_por_talle_v.Talle AS [Attribute Group Talle], 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle AS [Combination Reference], 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo AS [Combination Supplier Reference], 
                      0 AS [Combination Supplier Unit Price Tax Excl], '' AS [Combination Location], '' AS [Combination EAN13], '' AS [Combination UPC], 
                      dbo.colores_por_articulo.precio_mayorista_usd / 1.21 AS [Combination Wholesale Price], 
                      dbo.colores_por_articulo.ecommerce_price1 / 1.21 AS [Combination Price], 0 AS [Combination EcoTax], 
                      dbo.stock_01_14_20_por_talle_v.cant_1 AS [Combination Quantity], 0 AS [Combination Weight], 0 AS [Combination Unit Price Impact], 
                      '' AS [Combination Is Default], 1 AS [Combination Minimal Quantity], 0 AS attr_low_stock_threshold, 0 AS attr_low_stock_alert, GETDATE() 
                      AS [Combination Attribute Available Date], 
                      dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_d.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo
                       + '_e.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_i.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo
                       + '_t.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_a.jpg' AS [Combination Images], 
                      dbo.colores_por_articulo.ecommerce_price3 AS [Attribute Group Composition], dbo.articulos.naturaleza, dbo.stock_01_14_20_por_talle_v.Tall
e, 
                      dbo.familias_producto.nombre AS familia, familias_producto_2.nombre AS familia_ecommere, dbo.colores_por_articulo.ecommerce_existe, 
                      dbo.colores_por_articulo.catalogo, dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.cod_color_articulo AS cod_color, 
                      dbo.colores_por_articulo.ecommerce_price3 AS Composition, 
                      'ZAPATILLAS SPIRAL' + dbo.articulos.denom_articulo + '. Tienda Oficial' AS Description_ML
FROM         dbo.familias_producto familias_producto_2 RIGHT OUTER JOIN
                      dbo.familias_producto INNER JOIN
                      dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo ON 
                      dbo.familias_producto.id = dbo.articulos.cod_familia_producto INNER JOIN
                      dbo.Marcas ON dbo.articulos.cod_marca = dbo.Marcas.cod_marca INNER JOIN
                      dbo.lineas_productos ON dbo.articulos.cod_linea = dbo.lineas_productos.cod_linea INNER JOIN
                      dbo.stock_01_14_20_por_talle_v ON dbo.colores_por_articulo.cod_articulo = dbo.stock_01_14_20_por_talle_v.cod_articulo AND 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.stock_01_14_20_por_talle_v.cod_color_articulo ON 
                      familias_producto_2.id = dbo.colores_por_articulo.ecommerce_cod_category
WHERE     (dbo.stock_01_14_20_por_talle_v.Talle <> 'X') AND (dbo.stock_01_14_20_por_talle_v.Talle IS NOT NULL) AND 
                      (dbo.stock_01_14_20_por_talle_v.Talle <> '') AND (dbo.articulos.naturaleza = 'PT') AND (dbo.colores_por_articulo.ecommerce_existe = 'S') AND 
                      (dbo.colores_por_articulo.id_tipo_producto_stock <> '06')
ORDER BY dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle, 
                      dbo.colores_por_articulo.catalogo_orden_pagina

GO
---VIEW:dbo.mp_consumos_vw---
CREATE VIEW [dbo].[mp_consumos_vw] AS
CREATE VIEW dbo.mp_consumos_vw
AS
SELECT     dbo.Tareas_detalle.fecha_salida_real AS fecha_movimiento, dbo.Consumos_tarea.cod_material, dbo.Consumos_tarea.cod_color, 
                      - (1 * ISNULL(dbo.Consumos_tarea.cant_consumo, 0)) AS Cant, - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_1, 0) END)) AS c1, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_2, 0) END)) AS c2, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_3, 0) END)) AS c3, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_4, 0) END)) AS c4, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_5, 0) END)) AS c5, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_6, 0) END)) AS c6, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_7, 0) END)) AS c7, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_8, 0) END)) AS c8, '01' AS cod_almacen, 
                      'consumo tarea: ' + CAST(dbo.Consumos_tarea.nro_orden_fabricacion AS VARCHAR) + '-' + CAST(dbo.Consumos_tarea.nro_tarea AS VARCHAR) AS Motivo
FROM         dbo.Consumos_tarea INNER JOIN
                      dbo.Tareas_detalle ON dbo.Consumos_tarea.nro_orden_fabricacion = dbo.Tareas_detalle.nro_orden_fabricacion AND 
                      dbo.Consumos_tarea.nro_tarea = dbo.Tareas_detalle.nro_tarea AND dbo.Consumos_tarea.cod_seccion = dbo.Tareas_detalle.cod_seccion INNER JOIN
                      dbo.Tareas_cabecera ON dbo.Tareas_detalle.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion AND 
                      dbo.Tareas_detalle.nro_tarea = dbo.Tareas_cabecera.nro_tarea INNER JOIN
                      dbo.materiales AS m ON dbo.Consumos_tarea.cod_material = m.cod_material

GO
---VIEW:dbo.documentos_vendedor---
CREATE VIEW [dbo].[documentos_vendedor] AS

CREATE VIEW [dbo].[documentos_vendedor] AS
	SELECT
			d.*, c.cod_vendedor, c.razon_social
	FROM documentos d
	LEFT JOIN clientes c ON d.cod_cliente = c.cod_cli

GO
---VIEW:dbo.mp_consumos_facon_vw---
CREATE VIEW [dbo].[mp_consumos_facon_vw] AS
CREATE VIEW dbo.mp_consumos_facon_vw
AS
SELECT     fecha_ultima_modificacion AS fecha_movimiento, COD_MATERIAL, cod_color, - (1 * ISNULL(cantidad, 0)) AS cant, - (1 * ISNULL(cant_1, 0)) AS c1, - (1 * ISNULL(cant_2, 
                      0)) AS c2, - (1 * ISNULL(cant_3, 0)) AS c3, - (1 * ISNULL(cant_4, 0)) AS c4, - (1 * ISNULL(cant_5, 0)) AS c5, - (1 * ISNULL(cant_6, 0)) AS c6, - (1 * ISNULL(cant_7, 0)) AS c7, 
                      - (1 * ISNULL(cant_8, 0)) AS c8, '01' AS cod_almacen, 'Consumo facon: ' + COD_OPERADOR AS motivo
FROM         dbo.Stock_mp_fc

GO
---VIEW:dbo.listado_proveedores_v---
CREATE VIEW [dbo].[listado_proveedores_v] AS

CREATE VIEW dbo.listado_proveedores_v AS
	SELECT		p.*, pr.denom_provincia, l.denom_localidad
	FROM		proveedores_v p
				LEFT OUTER JOIN provincias pr ON pr.cod_provincia = p.provincia
				LEFT OUTER JOIN localidades l ON l.cod_localidad = p.localidad
	WHERE		p.anulado = 'N'
GO
---VIEW:dbo.compras_vw---
CREATE VIEW [dbo].[compras_vw] AS
CREATE VIEW dbo.compras_vw
AS
SELECT     TOP 100 PERCENT dbo.compras_cabecera.empresa, dbo.compras_cabecera.fecha, dbo.compras_cabecera.fecha_periodo_fiscal, dbo.compras_cabecera.tipo_doc, 
                      dbo.compras_cabecera.letra, dbo.compras_cabecera.nro_doc, dbo.compras_cabecera.cod_prov, dbo.proveedores_datos.cuit, dbo.proveedores_datos.razon_social, 
                      dbo.compras_cabecera.imputacion_1 AS imputacion, (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.neto_gravado AS neto_gravado, 
                      (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.iva_importe_1 AS iva_1, (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) 
                      * dbo.compras_cabecera.iva_importe_2 AS iva_2, (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.iva_importe_3 AS iva_3, 
                      (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.percepcion_iva AS percepcion_iva, 
                      (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.ingr_brut_reten_juris_1 AS reten_iibb, 
                      (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.total_doc AS total_doc
FROM         dbo.compras_cabecera INNER JOIN
                      dbo.proveedores_datos ON dbo.compras_cabecera.cod_prov = dbo.proveedores_datos.cod_prov
WHERE     (dbo.compras_cabecera.grado = N'M') AND (dbo.compras_cabecera.fecha_periodo_fiscal > CONVERT(DATETIME, '2012-01-01 00:00:00', 102))
ORDER BY dbo.compras_cabecera.fecha_periodo_fiscal DESC, dbo.compras_cabecera.fecha DESC

GO
---VIEW:dbo.patrones_v---
CREATE VIEW [dbo].[patrones_v] AS
CREATE VIEW dbo.patrones_v
AS
SELECT     dbo.Patrones_mp_cabecera.cod_color_articulo, dbo.articulos.denom_articulo, dbo.Patrones_mp_cabecera.cod_articulo, 
                      dbo.Patrones_mp_cabecera.version, dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material, 
                      dbo.Patrones_mp_detalle.cod_seccion, dbo.Patrones_mp_detalle.consumo_par, dbo.Patrones_mp_detalle.conjunto, dbo.articulos.naturaleza, 
                      dbo.materiales.denom_material
FROM         dbo.Patrones_mp_detalle INNER JOIN
                      dbo.Patrones_mp_cabecera ON dbo.Patrones_mp_detalle.cod_articulo = dbo.Patrones_mp_cabecera.cod_articulo AND 
                      dbo.Patrones_mp_detalle.cod_color_articulo = dbo.Patrones_mp_cabecera.cod_color_articulo AND 
                      dbo.Patrones_mp_detalle.version = dbo.Patrones_mp_cabecera.version INNER JOIN
                      dbo.Materias_primas ON dbo.Patrones_mp_detalle.cod_material = dbo.Materias_primas.cod_material AND 
                      dbo.Patrones_mp_detalle.cod_color_material = dbo.Materias_primas.cod_color INNER JOIN
                      dbo.materiales ON dbo.Materias_primas.cod_material = dbo.materiales.cod_material INNER JOIN
                      dbo.colores_por_articulo ON dbo.Patrones_mp_detalle.cod_articulo = dbo.colores_por_articulo.cod_articulo AND 
                      dbo.Patrones_mp_detalle.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo INNER JOIN
                      dbo.articulos ON dbo.colores_por_articulo.cod_articulo = dbo.articulos.cod_articulo

GO
---VIEW:dbo.mp_stock_detallado_vw---
CREATE VIEW [dbo].[mp_stock_detallado_vw] AS
CREATE VIEW mp_stock_detallado_vw AS SELECT fecha_ultima_modificacion AS fecha_movimiento, COD_MATERIAL, cod_color, - (1 * cantidad) AS cant, - (1 * cant_1) AS c1, - (1 * cant_2) AS c2, - (1 * cant_3) AS c3, - (1 * cant_4) AS c4, - (1 * cant_5) AS c5, - (1 * cant_6) AS c6, - (1 * cant_7) AS c7, - (1 * cant_8) AS c8, - (1 * cant_9) AS c9, - (1 * cant_10) AS c10, - (1 * cant_11) AS c11, - (1 * cant_12) AS c12, '01' AS cod_almacen, 'Consumo facon: ' + COD_OPERADOR AS motivo FROM dbo.Stock_mp_fc WHERE cod_material = '0001' AND cod_color = 'b' UNION SELECT fecha_movimiento, cod_material, cod_color_material AS cod_color, CASE WHEN efecto_movimiento = 'e' THEN cantidad ELSE (- 1 * cantidad) END AS cant, cant_1 AS c1, cant_2 AS c2, cant_3 AS c3, cant_4 AS c4, cant_5 AS c5, cant_6 AS c6, cant_7 AS c7, cant_8 AS c8, cant_9 AS c9, cant_10 AS c10, cant_11 AS c11, cant_12 AS c12, cod_almacen, 'Ajuste' AS Motivo FROM dbo.materias_primas_movim_extraor WHERE cod_material = '0001' AND cod_color_material = 'b' UNION SELECT dbo.Remitos_proveedor_cabecera.fecha_recepcion AS fecha_movimiento, dbo.remitos_proveedor_detalle.cod_material, dbo.remitos_proveedor_detalle.cod_color, dbo.remitos_proveedor_detalle.cantidad * dbo.materiales.factor_conversion AS cant, dbo.remitos_proveedor_detalle.cant_1 * dbo.materiales.factor_conversion AS c1, dbo.remitos_proveedor_detalle.cant_2 * dbo.materiales.factor_conversion AS c2, dbo.remitos_proveedor_detalle.cant_3 * dbo.materiales.factor_conversion AS c3, dbo.remitos_proveedor_detalle.cant_4 * dbo.materiales.factor_conversion AS c4, dbo.remitos_proveedor_detalle.cant_5 * dbo.materiales.factor_conversion AS c5, dbo.remitos_proveedor_detalle.cant_6 * dbo.materiales.factor_conversion AS c6, dbo.remitos_proveedor_detalle.cant_7 * dbo.materiales.factor_conversion AS c7, dbo.remitos_proveedor_detalle.cant_8 * dbo.materiales.factor_conversion AS c8, dbo.remitos_proveedor_detalle.cant_9 * dbo.materiales.factor_conversion AS c9, dbo.remitos_proveedor_detalle.cant_10 * dbo.materiales.factor_conversion AS c10, dbo.remitos_proveedor_detalle.cant_11 * dbo.materiales.factor_conversion AS c11, dbo.remitos_proveedor_detalle.cant_12 * dbo.materiales.factor_conversion AS c12, '01' AS cod_almacen, + ' - ' + dbo.Remitos_proveedor_cabecera.nro_compuesto_remito AS motivo FROM dbo.Remitos_proveedor_cabecera INNER JOIN dbo.remitos_proveedor_detalle ON dbo.Remitos_proveedor_cabecera.cod_proveedor = dbo.remitos_proveedor_detalle.cod_proveedor AND dbo.Remitos_proveedor_cabecera.nro_compuesto_remito = dbo.remitos_proveedor_detalle.nro_compuesto_remito INNER JOIN dbo.materiales ON dbo.remitos_proveedor_detalle.cod_material = dbo.materiales.cod_material WHERE dbo.remitos_proveedor_detalle.cod_material = '0001' AND cod_color = 'b' UNION SELECT fecha_consumo AS fecha_movimiento, cod_material, cod_color, - (1 * cant_consumo) AS Cant, - (1 * cant_1) AS c1, - (1 * cant_2) AS c2, - (1 * cant_3) AS c3, - (1 * cant_4) AS c4, - (1 * cant_5) AS c5, - (1 * cant_6) AS c6, - (1 * cant_7) AS c7, - (1 * cant_8) AS c8, - (1 * cant_9) AS c9, - (1 * cant_10) AS c10, - (1 * cant_11) AS c11, - (1 * cant_12) AS c12, '01' AS cod_almacen, 'consumo tarea: ' + CAST(nro_orden_fabricacion AS VARCHAR) + '-' + CAST(nro_tarea AS VARCHAR) AS Motivo FROM dbo.Consumos_tarea WHERE (cod_material = '0001') AND (cod_color = 'b')
GO
---VIEW:dbo.reporte_facturacion_proveedores_v---
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
---VIEW:dbo.gastos_vw---
CREATE VIEW [dbo].[gastos_vw] AS
CREATE VIEW dbo.gastos_vw
AS
SELECT     TOP 100 PERCENT empresa, gasto_fecha AS fecha, fecha_rendicion AS fecha_periodo_fiscal, comprobante_tipo AS tipo_doc, '' AS letra, comprobante_nro AS nro_doc, 
                      '' AS cod_prov, cuit_proveedor AS cuit, gasto_proveedor AS razon_social, imputacion, importe_neto AS neto_gravado, iva_importe AS iva_1, iva_importe_2 AS iva_2, 
                      iva_importe_3 AS iva_3, '' AS percepcion_iva, '' AS reten_iibb, total_importe AS total_doc
FROM         dbo.gastos_rendicion
WHERE     (fecha_rendicion > CONVERT(DATETIME, '2012-01-01 00:00:00', 102))
ORDER BY fecha_rendicion DESC, gasto_fecha DESC

GO
---VIEW:dbo.consumos_comprometidos_v---
CREATE VIEW [dbo].[consumos_comprometidos_v] AS
CREATE VIEW dbo.consumos_comprometidos_v
AS
SELECT     dbo.tareas_incumplidas_v.nro_plan, dbo.tareas_incumplidas_v.nro_orden_fabricacion, dbo.tareas_incumplidas_v.nro_tarea, 
                      dbo.tareas_incumplidas_v.cantidad, dbo.patrones_v.cod_articulo, dbo.patrones_v.denom_articulo, dbo.patrones_v.cod_color_articulo, 
                      dbo.patrones_v.version, dbo.patrones_v.conjunto, dbo.patrones_v.cod_material, dbo.patrones_v.denom_material, dbo.patrones_v.cod_color_material, 
                      dbo.patrones_v.consumo_par, dbo.patrones_v.cod_seccion, ISNULL(dbo.tareas_incumplidas_v.cantidad, 0) 
                      * dbo.patrones_v.consumo_par AS consumo_tarea, ISNULL(dbo.tareas_incumplidas_v.pos_1_cant, 0) * dbo.patrones_v.consumo_par AS cons_1, 
                      ISNULL(dbo.tareas_incumplidas_v.pos_2_cant, 0) * dbo.patrones_v.consumo_par AS cons_2, ISNULL(dbo.tareas_incumplidas_v.pos_3_cant, 0) 
                      * dbo.patrones_v.consumo_par AS cons_3, ISNULL(dbo.tareas_incumplidas_v.pos_4_cant, 0) * dbo.patrones_v.consumo_par AS cons_4, 
                      ISNULL(dbo.tareas_incumplidas_v.pos_5_cant, 0) * dbo.patrones_v.consumo_par AS cons_5, ISNULL(dbo.tareas_incumplidas_v.pos_6_cant, 0) 
                      * dbo.patrones_v.consumo_par AS cons_6, ISNULL(dbo.tareas_incumplidas_v.pos_7_cant, 0) * dbo.patrones_v.consumo_par AS cons_7, 
                      ISNULL(dbo.tareas_incumplidas_v.pos_8_cant, 0) * dbo.patrones_v.consumo_par AS cons_8, ISNULL(dbo.stock_mp.cant, 0) AS stock, 
                      ISNULL(dbo.stock_mp.c1, 0) AS stock_1, ISNULL(dbo.stock_mp.c2, 0) AS stock_2, ISNULL(dbo.stock_mp.c3, 0) AS stock_3, ISNULL(dbo.stock_mp.c4, 0) 
                      AS stock_4, ISNULL(dbo.stock_mp.c5, 0) AS stock_5, ISNULL(dbo.stock_mp.c6, 0) AS stock_6, ISNULL(dbo.stock_mp.c7, 0) AS stock_7, 
                      ISNULL(dbo.stock_mp.c8, 0) AS stock_8, dbo.stock_mp.cod_almacen
FROM         dbo.tareas_incumplidas_v INNER JOIN
                      dbo.patrones_v ON dbo.tareas_incumplidas_v.cod_articulo = dbo.patrones_v.cod_articulo AND 
                      dbo.tareas_incumplidas_v.cod_color_articulo = dbo.patrones_v.cod_color_articulo AND 
                      dbo.tareas_incumplidas_v.version = dbo.patrones_v.version AND 
                      dbo.tareas_incumplidas_v.cod_seccion = dbo.patrones_v.cod_seccion LEFT OUTER JOIN
                      dbo.stock_mp ON dbo.patrones_v.cod_material = dbo.stock_mp.cod_material AND dbo.patrones_v.cod_color_material = dbo.stock_mp.cod_color

GO
---VIEW:dbo.costos_fijos_periodo_v---
CREATE VIEW [dbo].[costos_fijos_periodo_v] AS
CREATE VIEW dbo.costos_fijos_periodo_v
AS
SELECT     dbo.costos_fijos_c.nro_periodo, dbo.costos_fijos_d.cod_linea, 
                      dbo.costos_fijos_c.costo_estructura * dbo.costos_fijos_d.porcentaje_periodo / 100 / dbo.costos_fijos_d.cantidad_producida AS costo_linea
FROM         dbo.costos_fijos_c INNER JOIN
                      dbo.costos_fijos_d ON dbo.costos_fijos_c.nro_costo_fijo = dbo.costos_fijos_d.nro_costo_fijo_c

GO
---VIEW:dbo.movimientos_caja_v---
CREATE VIEW [dbo].[movimientos_caja_v] AS

CREATE VIEW [dbo].[movimientos_caja_v] AS
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
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM recibo r
	LEFT OUTER JOIN clientes c ON c.cod_cliente = r.cod_cliente
	INNER JOIN importe_por_operacion_c ipoc ON r.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE r.anulado = 'N'

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
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM orden_de_pago op
	LEFT OUTER JOIN proveedores_datos p ON p.cod_prov = op.cod_proveedor
	INNER JOIN importe_por_operacion_c ipoc ON op.cod_importe_operacio
n = ipoc.cod_importe_operacion
	WHERE op.anulado = 'N'

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
		ipoc.cod_importe_operacion cod_importe_operacion
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
		ipoc.cod_importe_operacion cod_importe_operacion
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
		WHERE ac2.entrada_salida = 'E' AND acc.cod_a
creditar_debitar_cheque = ac2.cod_acreditar_debitar_cheque AND acc.empresa = ac2.empresa) para,
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
		ipoc.cod_importe_operacion cod_importe_operacion
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
		ipoc.cod_importe_operacion cod_importe_operacion
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
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_o
peracion_c ipoc1
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
		ipoc.cod_importe_operacion cod_importe_operacion
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
		ipoc.cod_importe_operacion cod_importe_operacion
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
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE tbo.cod_importe_operacion = ipoc1.cod_importe_operaci
on) efectivo,
		0 cheques,
		0 transferencias,
		0 retenciones,
		tbo.importe_total total, ipoc.cod_caja, dbo.relativeDate(tbo.fecha,'today',0) fecha,
		tbo.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM transferencia_bancaria_operacion tbo
	INNER JOIN cuenta_bancaria cb ON tbo.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
	INNER JOIN caja caja ON caja.cod_caja = cb.cod_caja
	INNER JOIN importe_por_operacion_c ipoc ON tbo.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE tbo.anulado = 'N'

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
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM rendicion_de_gastos rg
	INNER JOIN importe_por_operacion_c ipoc ON rg.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja
	WHERE rg.anulado = 'N'

	UNION ALL
	
	SELECT 'I' tipo, 'AS' tipo_documento, aps.nro_aporte_socio numero, s.nombre de, 'SPIRAL SHOES S.A.' para,
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
		aps.importe_total total, ipoc.cod_caja, dbo.relativeDate(aps.fecha_documento,'today',0) fecha,
		aps.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM aporte_socio aps
	LEFT OUTER JOIN socio s ON s.cod_socio = aps.cod_socio
	INNER JOIN importe_por_operacion_c ipoc ON aps.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE aps.anulado = 'N'
	
	UNION ALL
	
	SELECT 'E' tipo, 'RS' tipo_documento, aps.nro_retiro_socio numero, 'SPIRAL SHOES S.A.' de, s.nombre para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE aps.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INN
ER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 's'
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
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM retiro_socio aps
	LEFT OUTER JOIN socio s ON s.cod_socio = aps.cod_socio
	INNER JOIN importe_por_operacion_c ipoc ON aps.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE aps.anulado = 'N'
	
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
		ipoc.cod_importe_operacion cod_importe_operacion
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
		WHERE r.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		0 cheques,
		0 transferencia
s,
		0 retenciones,
		r.importe_total total, ipoc.cod_caja, dbo.relativeDate(r.fecha_documento,'today',0) fecha,
		r.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM prestamo r
	LEFT OUTER JOIN cuenta_bancaria cb ON cb.cod_cuenta_bancaria = r.cod_cuenta_bancaria
	INNER JOIN importe_por_operacion_c ipoc ON r.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE r.anulado = 'N'
	
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
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM cobro_cheque_ventanilla_d ccvd
	INNER JOIN cobro_cheque_ventanilla_c ccvc ON ccvc.cod_cobro_cheque_ventanilla = ccvd.cod_cobro_cheque_ventanilla AND ccvc.empresa = ccvd.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ccvd.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja
	WHERE ccvd.entrada_salida = 'E'
GO
---VIEW:dbo.usuarios_por_area_empresa_v---
CREATE VIEW [dbo].[usuarios_por_area_empresa_v] AS

CREATE VIEW usuarios_por_area_empresa_v AS
	SELECT a.id_area_empresa, b.*
	FROM usuarios_por_area_empresa a
	INNER JOIN users b ON a.cod_usuario = b.cod_usuario
GO
---VIEW:dbo.egresos_compras_gastos_vw---
CREATE VIEW [dbo].[egresos_compras_gastos_vw] AS


CREATE   view    egresos_compras_gastos_vw as

select  * from(SELECT top 100 percent  dbo.compras_cabecera.empresa, dbo.compras_cabecera.fecha, dbo.compras_cabecera.fecha_periodo_fiscal, dbo.compras_cabecera.tipo_doc, 
                      dbo.compras_cabecera.letra, dbo.compras_cabecera.nro_doc, dbo.compras_cabecera.cod_prov, dbo.proveedores_datos.cuit, dbo.proveedores_datos.razon_social, 
                      dbo.compras_cabecera.imputacion_1 AS imputacion, (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.neto_gravado AS neto_gravado, 
                      (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.iva_importe_1 AS iva_1, (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) 
                      * dbo.compras_cabecera.iva_importe_2 AS iva_2, (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.iva_importe_3 AS iva_3, 
                      (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.percepcion_iva AS percepcion_iva, 
                      (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.ingr_brut_reten_juris_1 AS reten_iibb, 
                      (CASE WHEN tipo_doc = 'NCR' THEN '-1' ELSE 1 END) * dbo.compras_cabecera.total_doc AS total_doc,'Compra' as tipo
FROM         dbo.compras_cabecera INNER JOIN
                      dbo.proveedores_datos ON dbo.compras_cabecera.cod_prov = dbo.proveedores_datos.cod_prov
WHERE     (dbo.compras_cabecera.grado = N'M') AND (dbo.compras_cabecera.fecha_periodo_fiscal > CONVERT(DATETIME, '2011-06-01 00:00:00', 102))
ORDER BY dbo.compras_cabecera.fecha_periodo_fiscal DESC, dbo.compras_cabecera.fecha DESC)as c 
union all 
select  * from (SELECT top 100 percent  empresa, gasto_fecha AS fecha, gasto_fecha AS fecha_periodo_fiscal, comprobante_tipo AS tipo_doc, '' AS letra, 
                      comprobante_nro AS nro_doc, '' AS cod_prov, cuit_proveedor AS cuit, gasto_proveedor AS razon_social, imputacion, importe_neto AS neto_gravado,
                   iva_importe AS iva_1,    iva_importe_2 AS iva_2, iva_importe_3 AS iva_3, '' AS percepcion_iva, '' AS reten_iibb, total_importe AS total_doc, 'Gasto' AS tipo
FROM         dbo.gastos_rendicion
WHERE     (gasto_fecha > CONVERT(DATETIME, '2011-06-01 00:00:00', 102))
ORDER BY gasto_fecha DESC) as g


GO
---VIEW:dbo.stock_registros_aux_vw---
CREATE VIEW [dbo].[stock_registros_aux_vw] AS

CREATE VIEW [dbo].[stock_registros_aux_vw]
AS
	/*
		Esta consulta trae registros de cada lugar donde se debe sacar el stock y pone uno por línea
		Los asignados los trae como Almacen '01'. De donde se la llame se debe agrupar y hacer la suma.
		Se usa para la view 'stock_menos_asignado'
	 */
(
	SELECT Tareas_detalle.cod_almacen, Orden_fabricacion.cod_articulo, Orden_fabricacion.cod_color_articulo, Sum(Tareas_detalle.cant_1) c_1, Sum(Tareas_detalle.cant_2) c_2, Sum(Tareas_detalle.cant_3) c_3, Sum(Tareas_detalle.cant_4) c_4, Sum(Tareas_detalle.cant_5) c_5, Sum(Tareas_detalle.cant_6) c_6, Sum(Tareas_detalle.cant_7) c_7, Sum(Tareas_detalle.cant_8) c_8, Sum(Tareas_detalle.cant_9) c_9, Sum(Tareas_detalle.cant_10) c_10, Sum(Tareas_detalle.cant_11) c_11, Sum(Tareas_detalle.cant_12) c_12
	FROM (articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN (Orden_fabricacion INNER JOIN (Tareas_cabecera INNER JOIN Tareas_detalle ON (Tareas_cabecera.nro_tarea = Tareas_detalle.nro_tarea) AND (Tareas_cabecera.nro_orden_fabricacion = Tareas_detalle.nro_orden_fabricacion)) ON Orden_fabricacion.nro_orden_fabricacion = Tareas_cabecera.nro_orden_fabricacion) ON (colores_por_articulo.cod_articulo = Orden_fabricacion.cod_articulo) AND (colores_por_articulo.cod_color_articulo = Orden_fabricacion.cod_color_articulo)
	WHERE ((Tareas_detalle.cod_almacen <> '') AND ((Tareas_detalle.cod_seccion)=60) AND ((articulos.vigente)='s') AND ((colores_por_articulo.vigente)='s') AND ((Tareas_cabecera.anulado)<>'s') AND ((Tareas_detalle.cumplido_paso)='s') AND ((articulos.naturaleza)='PT'))
	GROUP BY Orden_fabricacion.cod_articulo, Orden_fabricacion.cod_color_articulo, Tareas_detalle.cod_almacen
) UNION (
	SELECT Despachos_detalle.cod_almacen, Despachos_detalle.cod_articulo cod_articulo, Despachos_detalle.cod_color cod_color_articulo, Sum(-[entr_1]) c_1, Sum(-[entr_2]) c_2, Sum(-[entr_3]) c_3, Sum(-[entr_4]) c_4, Sum(-[entr_5]) c_5, Sum(-[entr_6]) c_6, Sum(-[entr_7]) c_7, Sum(-[entr_8]) c_8, Sum(-[entr_9]) c_9, Sum(-[entr_10]) c_10, Sum(-[entr_11]) c_11, Sum(-[entr_12]) c_12
	FROM (((dbo.articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN Despachos_detalle ON (colores_por_articulo.cod_color_articulo = Despachos_detalle.cod_color) AND (colores_por_articulo.cod_articulo = Despachos_detalle.cod_articulo)) INNER JOIN Despachos_cabecera ON (Despachos_detalle.nro_despacho = Despachos_cabecera.nro_despacho) AND (Despachos_detalle.cod_sucursal_despacho = Despachos_cabecera.cod_sucursal_despacho) AND (Despachos_detalle.cod_empresa_despacho = Despachos_cabecera.cod_empresa_despacho)) INNER JOIN remitos_cabecera ON (Despachos_detalle.letra_remito = remitos_cabecera.letra_remito) AND (Despachos_detalle.nro_remito = remitos_cabecera.nro_remito) AND (Despachos_detalle.cod_sucursal_despacho = remitos_cabecera.cod_sucursal_despacho) AND (Despachos_detalle.cod_empresa_despacho = remitos_cabecera.cod_empresa_despacho)
	WHERE ((Despachos_detalle.cod_almacen <> '') AND (colores_por_articulo.vigente = 's') AND ((articulos.vigente)='s') AND ((articulos.naturaleza)='PT') AND ((Despachos_cabecera.anulado = 'n')))
	GROUP BY Despachos_detalle.cod_articulo, Despachos_detalle.cod_color, Despachos_detalle.cod_almacen
) UNION (
	--NEGATIVOS
	SELECT prod_terminados_movim_extraor.cod_almacen, prod_terminados_movim_extraor.cod_articulo cod_articulo, prod_terminados_movim_extraor.cod_color_articulo cod_color_articulo, Sum([cant_1]*-1) c_1, Sum([cant_2]*-1) c_2, Sum([cant_3]*-1) c_3, Sum([cant_4]*-1) c_4, Sum([cant_5]*-1) c_5, Sum([cant_6]*-1) c_6, Sum([cant_7]*-1) c_7, Sum([cant_8]*-1) c_8, Sum([cant_9]*-1) c_9, Sum([cant_10]*-1) c_10, Sum([cant_11]*-1) c_11, Sum([cant_12]*-1) c_12
	FROM (dbo.articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN dbo.prod_terminados_movim_extraor ON (colo
res_por_articulo.cod_articulo = prod_terminados_movim_extraor.cod_articulo) AND (colores_por_articulo.cod_color_articulo = prod_terminados_movim_extraor.cod_color_articulo)
	WHERE ((prod_terminados_movim_extraor.cod_almacen <> '') AND ((articulos.vigente)='s') AND ((colores_por_articulo.vigente)='s') AND ((articulos.naturaleza)='PT') AND (((prod_terminados_movim_extraor.tipo_movimiento)='aju_neg' Or (prod_terminados_movim_extraor.tipo_movimiento)='SINIES')))
	GROUP BY prod_terminados_movim_extraor.cod_almacen, prod_terminados_movim_extraor.cod_articulo, prod_terminados_movim_extraor.cod_color_articulo
) UNION (
	--POSITIVOS
	SELECT prod_terminados_movim_extraor.cod_almacen, prod_terminados_movim_extraor.cod_articulo cod_articulo, prod_terminados_movim_extraor.cod_color_articulo cod_color_articulo, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_1 ELSE cant_1 END) c_1, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_2 ELSE cant_2 END) c_2, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_3 ELSE cant_3 END) c_3, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_4 ELSE cant_4 END) c_4, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_5 ELSE cant_5 END) c_5, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_6 ELSE cant_6 END) c_6, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_7 ELSE cant_7 END) c_7, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_8 ELSE cant_8 END) c_8, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_9 ELSE cant_9 END) c_9, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_10 ELSE cant_10 END) c_10, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_11 ELSE cant_11 END) c_11, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_12 ELSE cant_12 END) c_12
	FROM (dbo.articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN dbo.prod_terminados_movim_extraor ON (colores_por_articulo.cod_articulo = prod_terminados_movim_extraor.cod_articulo) AND (colores_por_articulo.cod_color_articulo = prod_terminados_movim_extraor.cod_color_articulo)
	WHERE ((prod_terminados_movim_extraor.cod_almacen <> '') AND ((articulos.vigente)='s') AND ((colores_por_articulo.vigente)='s') AND ((articulos.naturaleza)='PT') AND (((prod_terminados_movim_extraor.tipo_movimiento)<>'aju_neg' AND (prod_terminados_movim_extraor.tipo_movimiento)<>'sinies')))
	GROUP BY prod_terminados_movim_extraor.cod_almacen, prod_terminados_movim_extraor.cod_articulo, prod_terminados_movim_extraor.cod_color_articulo
) UNION (
	SELECT docum_clientes_detalle.cod_almacen, docum_clientes_detalle.cod_articulo cod_articulo, docum_clientes_detalle.cod_color cod_color_articulo, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_1 ELSE -[cant_1] END) c_1, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_2 ELSE -[cant_2] END) c_2, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_3 ELSE -[cant_3] END) c_3, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_4 ELSE -[cant_4] END) c_4, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_5 ELSE -[cant_5] END) c_5, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_6 ELSE -[cant_6] END) c_6, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_7 ELSE -[cant_7] END) c_7, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_8 ELSE -[cant_8] END) c_8, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_9 ELSE -[cant_9] END) c_9, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_10 ELSE -[cant_10] END) c_10, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_11 ELSE -[cant_11] END) c_11, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_12 ELSE -[cant_12] END) c_12
	FROM (articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN dbo.docum_clientes_detalle ON (colores_por_articulo.cod_color_articulo = docum_clientes_detalle.cod_color) AND (colores_por_articulo.cod_articulo = docum_clientes_detalle.cod_articulo)
	WHERE ((docum_clientes_detalle.cod_almacen <> '') AND ((colores_por_articulo.vigente)='s') AND ((articulos.vigente)='s') AND ((articulos.natural
eza)='PT'))
	GROUP BY docum_clientes_detalle.cod_articulo, docum_clientes_detalle.cod_color, docum_clientes_detalle.cod_almacen
) UNION (
	SELECT '01' cod_almacen, cod_articulo, cod_color_articulo, (-1) * SUM(pend_1) c_1, (-1) * SUM(pend_2) c_2, (-1) * SUM(pend_3) c_3, (-1) * SUM(pend_4) c_4, (-1) * SUM(pend_5) c_5, (-1) * SUM(pend_6) c_6, (-1) * SUM(pend_7) c_7, (-1) * SUM(pend_8) c_8, (-1) * SUM(pend_9) c_9, (-1) * SUM(pend_10) c_10, (-1) * SUM(pend_11) c_11, (-1) * SUM(pend_12) c_12
	FROM pedidos_detalle de
	INNER JOIN pedidos_cabecera ca ON ca.nro_pedido = de.nro_pedido AND (ca.anulado = 'N' OR ca.anulado IS NULL)
	WHERE (
		(pend_1 IS NOT NULL OR pend_2 IS NOT NULL OR pend_3 IS NOT NULL OR pend_4 IS NOT NULL OR pend_5 IS NOT NULL OR pend_6 IS NOT NULL OR pend_7 IS NOT NULL OR pend_8 IS NOT NULL OR pend_9 IS NOT NULL OR pend_10 IS NOT NULL OR pend_11 IS NOT NULL OR pend_12 IS NOT NULL) AND
		(cantidad_pendiente > 0) AND
		(de.anulado = 'N' OR de.anulado IS NULL)
	)
	GROUP BY cod_articulo, cod_color_articulo
) 

GO
---VIEW:dbo.costos_fijos_periodo_vig_v---
CREATE VIEW [dbo].[costos_fijos_periodo_vig_v] AS
CREATE VIEW dbo.costos_fijos_periodo_vig_v
AS
SELECT     dbo.costos_fijos_c.nro_periodo, dbo.costos_fijos_d.cod_linea, 
                      dbo.costos_fijos_c.costo_estructura * dbo.costos_fijos_d.porcentaje_periodo / 100 / dbo.costos_fijos_d.cantidad_producida AS costo_linea
FROM         dbo.costos_fijos_c INNER JOIN
                      dbo.costos_fijos_d ON dbo.costos_fijos_c.nro_costo_fijo = dbo.costos_fijos_d.nro_costo_fijo_c
WHERE     (dbo.costos_fijos_c.vigente = 'S')

GO
---VIEW:dbo.stock_menos_pendiente_vw---
CREATE VIEW [dbo].[stock_menos_pendiente_vw] AS
CREATE VIEW dbo.stock_menos_pendiente_vw
AS
SELECT     stock_d.cod_almacen, stock_d.cod_articulo, stock_d.cod_color_articulo, SUM(stock_d.cant_s) AS cant_s, SUM(stock_d.S1) AS S1, SUM(stock_d.S2) 
                      AS S2, SUM(stock_d.S3) AS S3, SUM(stock_d.S4) AS S4, SUM(stock_d.S5) AS S5, SUM(stock_d.S6) AS S6, SUM(stock_d.S7) AS S7, SUM(stock_d.S8) 
                      AS S8, SUM(stock_d.S9) AS S9, SUM(stock_d.S10) AS S10
FROM         (SELECT     '01' AS cod_almacen, cod_articulo, cod_color_articulo, SUM(cant_s) AS cant_s, SUM(S1) AS S1, SUM(S2) AS S2, SUM(S3) AS S3, SUM(S4) 
                                              AS S4, SUM(S5) AS S5, SUM(S6) AS S6, SUM(S7) AS S7, SUM(S8) AS S8, SUM(S9) AS S9, SUM(S10) AS S10
                       FROM          dbo.stock_pt
                       WHERE      (cod_almacen = '01' OR
                                              cod_almacen = '14' OR
                                              cod_almacen = '20')
                       GROUP BY cod_articulo, cod_color_articulo
                       UNION
                       SELECT     cod_almacen, cod_articulo, cod_color_articulo, SUM(- (ISNULL(pendiente, 0) + ISNULL(predespachados, 0))) AS cant_s, 
                                             SUM(- (ISNULL(pend_1, 0) + ISNULL(pred_1, 0))) AS S1, SUM(- (ISNULL(pend_2, 0) + ISNULL(pred_2, 0))) AS S2, SUM(- (ISNULL(pend_3, 0) 
                                             + ISNULL(pred_3, 0))) AS S3, SUM(- (ISNULL(pend_4, 0) + ISNULL(pred_4, 0))) AS S4, SUM(- (ISNULL(pend_5, 0) + ISNULL(pred_5, 0))) AS S5, 
                                             SUM(- (ISNULL(pend_6, 0) + ISNULL(pred_6, 0))) AS S6, SUM(- (ISNULL(pend_7, 0) + ISNULL(pred_7, 0))) AS S7, SUM(- (ISNULL(pend_8, 0) 
                                             + ISNULL(pred_8, 0))) AS S8, SUM(- (ISNULL(pend_9, 0) + ISNULL(pred_9, 0))) AS S9, SUM(- (ISNULL(pend_10, 0) + ISNULL(pred_10, 0))) 
                                             AS S10
                       FROM         dbo.pedidos_d_v
                       WHERE     (anulado = 'N') AND (ISNULL(pendiente, 0) + ISNULL(predespachados, 0) > 0)
                       GROUP BY cod_almacen, cod_articulo, cod_color_articulo) stock_d LEFT OUTER JOIN
                      dbo.colores_por_articulo c ON c.cod_articulo = stock_d.cod_articulo AND c.cod_color_articulo = stock_d.cod_color_articulo
WHERE     (c.comercializacion_libre <> 'A')
GROUP BY stock_d.cod_almacen, stock_d.cod_articulo, stock_d.cod_color_articulo

GO
---VIEW:dbo.costo_producto_total_V---
CREATE VIEW [dbo].[costo_producto_total_V] AS
CREATE VIEW dbo.costo_producto_total_V
AS
SELECT     costo_agrupado.cod_linea AS cod_linea, costo_agrupado.cod_articulo AS cod_articulo, costo_agrupado.denom_articulo AS denom_articulo, 
                      costo_agrupado.cod_color_articulo AS cod_color_articulo, costo_agrupado.Costo AS costo, dbo.costos_fijos_periodo_vig_v.costo_linea AS costo_linea,
                       costo_agrupado.Costo + dbo.costos_fijos_periodo_vig_v.costo_linea AS costo_total
FROM         (SELECT     cod_articulo, denom_articulo, cod_color_articulo, SUM(Costo) AS Costo, cod_linea
                       FROM          dbo.costo_mp_producto_detalle_v
                       GROUP BY cod_articulo, denom_articulo, cod_color_articulo, cod_linea) costo_agrupado INNER JOIN
                      dbo.costos_fijos_periodo_vig_v ON costo_agrupado.cod_linea = dbo.costos_fijos_periodo_vig_v.cod_linea

GO
---VIEW:dbo.mp_stock_agrupado_vw---
CREATE VIEW [dbo].[mp_stock_agrupado_vw] AS
CREATE VIEW mp_stock_agrupado_vw AS SELECT cod_material, cod_color_material AS cod_color, SUM(CASE WHEN efecto_movimiento = 'e' THEN cantidad ELSE (- 1 * cantidad) END) AS cant, SUM(cant_1) AS c1, SUM(cant_2) AS c2, SUM(cant_3) AS c3, SUM(cant_4) AS c4, SUM(cant_5) AS c5, SUM(cant_6) AS c6, SUM(cant_7) AS c7, SUM(cant_8) AS c8, SUM(cant_9) AS c9, SUM(cant_10) AS c10, SUM(cant_11) AS c11, SUM(cant_12) AS c12, cod_almacen FROM dbo.materias_primas_movim_extraor GROUP BY dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material, dbo.materias_primas_movim_extraor.cod_almacen UNION SELECT COD_MATERIAL, cod_color, SUM(- (1 * cantidad)) AS cant, SUM(- (1 * cant_1)) AS c1, SUM(- (1 * cant_2)) AS c2, SUM(- (1 * cant_3)) AS c3, SUM(- (1 * cant_4)) AS c4, SUM(- (1 * cant_5)) AS c5, SUM(- (1 * cant_6)) AS c6, SUM(- (1 * cant_7)) AS c7, SUM(- (1 * cant_8)) AS c8, SUM(- (1 * cant_9)) AS c9, SUM(- (1 * cant_10)) AS c10, SUM(- (1 * cant_11)) AS c11, SUM(- (1 * cant_12)) AS c12, '01' AS cod_almacen FROM dbo.Stock_mp_fc GROUP BY COD_MATERIAL, cod_color UNION SELECT cod_material, cod_color, SUM(- (1 * cant_consumo)) AS Cant, SUM(- (1 * cant_1)) AS c1, SUM(- (1 * cant_2)) AS c2, SUM(- (1 * cant_3)) AS c3, SUM(- (1 * cant_4)) AS c4, SUM(- (1 * cant_5)) AS c5, SUM(- (1 * cant_6)) AS c6, SUM(- (1 * cant_7)) AS c7, SUM(- (1 * cant_8)) AS c8, SUM(- (1 * cant_9)) AS c9, SUM(- (1 * cant_10)) AS c10, SUM(- (1 * cant_11)) AS c11, SUM(- (1 * cant_12)) AS c12, '01' AS cod_almacen FROM dbo.Consumos_tarea GROUP BY cod_material, cod_color UNION SELECT dbo.remitos_proveedor_detalle.cod_material, dbo.remitos_proveedor_detalle.cod_color, SUM(dbo.remitos_proveedor_detalle.cantidad * dbo.materiales.factor_conversion) AS cant, SUM(dbo.remitos_proveedor_detalle.cant_1 * dbo.materiales.factor_conversion) AS c1, SUM(dbo.remitos_proveedor_detalle.cant_2 * dbo.materiales.factor_conversion) AS c2, SUM(dbo.remitos_proveedor_detalle.cant_3 * dbo.materiales.factor_conversion) AS c3, SUM(dbo.remitos_proveedor_detalle.cant_4 * dbo.materiales.factor_conversion) AS c4, SUM(dbo.remitos_proveedor_detalle.cant_5 * dbo.materiales.factor_conversion) AS c5, SUM(dbo.remitos_proveedor_detalle.cant_6 * dbo.materiales.factor_conversion) AS c6, SUM(dbo.remitos_proveedor_detalle.cant_7 * dbo.materiales.factor_conversion) AS c7, SUM(dbo.remitos_proveedor_detalle.cant_8 * dbo.materiales.factor_conversion) AS c8, SUM(dbo.remitos_proveedor_detalle.cant_9 * dbo.materiales.factor_conversion) AS c9, SUM(dbo.remitos_proveedor_detalle.cant_10 * dbo.materiales.factor_conversion) AS c10, SUM(dbo.remitos_proveedor_detalle.cant_11 * dbo.materiales.factor_conversion) AS c11, SUM(dbo.remitos_proveedor_detalle.cant_12 * dbo.materiales.factor_conversion) AS c12, '01' AS cod_almacen FROM dbo.Remitos_proveedor_cabecera INNER JOIN dbo.remitos_proveedor_detalle ON dbo.Remitos_proveedor_cabecera.cod_proveedor = dbo.remitos_proveedor_detalle.cod_proveedor AND dbo.Remitos_proveedor_cabecera.nro_compuesto_remito = dbo.remitos_proveedor_detalle.nro_compuesto_remito INNER JOIN dbo.materiales ON dbo.remitos_proveedor_detalle.cod_material = dbo.materiales.cod_material GROUP BY dbo.remitos_proveedor_detalle.cod_material, dbo.remitos_proveedor_detalle.cod_color
GO
---VIEW:dbo.costo_factura_total_v---
CREATE VIEW [dbo].[costo_factura_total_v] AS
CREATE VIEW dbo.costo_factura_total_v
AS
SELECT     TOP 100 PERCENT dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.punto_venta, 
                      dbo.documentos_cantidades.tipo_docum, dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, 
                      SUM(dbo.costo_producto_total_V.costo * dbo.documentos_cantidades.cantidad) AS costo, 
                      SUM(dbo.documentos_cantidades.cantidad * dbo.documentos_cantidades.precio_unitario_final) 
                      * (CASE dbo.documentos_cantidades.tipo_docum WHEN 'NCR' THEN - 1 ELSE 1 END) AS importe_articulos
FROM         dbo.documentos_cantidades INNER JOIN
                      dbo.costo_producto_total_V ON dbo.documentos_cantidades.cod_articulo = dbo.costo_producto_total_V.cod_articulo AND 
                      dbo.documentos_cantidades.cod_color_articulo = dbo.costo_producto_total_V.cod_color_articulo
GROUP BY dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.tipo_docum, 
                      dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, dbo.documentos_cantidades.punto_venta
ORDER BY dbo.documentos_cantidades.fecha

GO
---VIEW:dbo.documentos_rentabilidad---
CREATE VIEW [dbo].[documentos_rentabilidad] AS
CREATE VIEW dbo.documentos_rentabilidad
AS
SELECT     TOP 100 PERCENT dbo.documentos.empresa, dbo.documentos.punto_venta, dbo.documentos.tipo_docum, dbo.documentos.numero, 
                      dbo.documentos.letra, dbo.documentos.anulado, dbo.documentos.cod_cliente, dbo.Clientes.razon_social, dbo.documentos.fecha, 
                      dbo.documentos.importe_total * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) AS importe_total, 
                      ISNULL(dbo.documentos.importe_neto, 0) * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) AS importe_neto, 
                      ISNULL(dbo.documentos.importe_no_gravado, 0) * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) AS importe_no_gravado, 
                      dbo.costo_factura_total_v.importe_articulos, dbo.costo_factura_total_v.costo, 
                      (dbo.documentos.importe_neto - ISNULL(dbo.documentos.descuento_comercial_importe, 0) - ISNULL(dbo.documentos.descuento_despacho_importe, 
                      0)) * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) AS importe_sin_iva, 
                      ((dbo.documentos.importe_neto - ISNULL(dbo.documentos.descuento_comercial_importe, 0) - ISNULL(dbo.documentos.descuento_despacho_importe, 
                      0)) * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END)) - dbo.costo_factura_total_v.costo AS renta, 
                      ISNULL(dbo.documentos.descuento_comercial_importe, 0) * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) 
                      AS descuento_comercial_importe, ISNULL(dbo.documentos.descuento_comercial_porc, 0) AS descuento_comercial_porc, 
                      ISNULL(dbo.documentos.descuento_despacho_importe, 0) AS descuento_despacho_importe, ISNULL(dbo.documentos.iva_importe_1, 0) 
                      * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) AS iva_importe_1, ISNULL(dbo.documentos.iva_importe_2, 0) 
                      * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) AS iva_importe_2, ISNULL(dbo.documentos.iva_importe_3, 0) 
                      * (CASE dbo.documentos.tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) AS iva_importe_3
FROM         dbo.Clientes INNER JOIN
                      dbo.documentos ON dbo.Clientes.cod_cli = dbo.documentos.cod_cliente LEFT OUTER JOIN
                      dbo.costo_factura_total_v ON dbo.documentos.empresa = dbo.costo_factura_total_v.empresa AND 
                      dbo.documentos.punto_venta = dbo.costo_factura_total_v.punto_venta AND dbo.documentos.tipo_docum = dbo.costo_factura_total_v.tipo_docum AND 
                      dbo.documentos.numero = dbo.costo_factura_total_v.nro_documento AND dbo.documentos.letra = dbo.costo_factura_total_v.letra_documento
WHERE     (dbo.documentos.fecha > '01 / 01 / 2016')
ORDER BY dbo.documentos.fecha

GO
---VIEW:dbo.tareas_cabecera_v---
CREATE VIEW [dbo].[tareas_cabecera_v] AS
CREATE VIEW tareas_cabecera_v AS
	SELECT tc.*, o.cod_articulo, o.cod_color_articulo, o.version
	FROM Tareas_cabecera tc
	INNER JOIN Orden_fabricacion o ON o.nro_orden_fabricacion = tc.nro_orden_fabricacion

GO
---VIEW:dbo.pedidos_clientes_articulo_vendedor---
CREATE VIEW [dbo].[pedidos_clientes_articulo_vendedor] AS
CREATE VIEW dbo.pedidos_clientes_articulo_vendedor
AS
SELECT     TOP 100 PERCENT dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, dbo.pedidos_d.pendiente, dbo.pedidos_d.cantidad AS Pedido, 
                      dbo.pedidos_d.pend_1 AS P1, dbo.pedidos_d.pend_2 AS P2, dbo.pedidos_d.pend_3 AS P3, dbo.pedidos_d.pend_4 AS P4, 
                      dbo.pedidos_d.pend_5 AS P5, dbo.pedidos_d.pend_6 AS P6, dbo.pedidos_d.pend_7 AS P7, dbo.pedidos_d.pend_8 AS P8, 
                      dbo.pedidos_d.pend_9 AS P9, dbo.pedidos_d.pend_10 AS P10, dbo.Clientes.cod_cli, dbo.personal.apellido + N',' + LEFT(dbo.personal.nombres, 1) 
                      AS vendedor, dbo.Clientes.Situacion, dbo.Clientes.razon_social, dbo.Clientes.denom_fantasia, dbo.Clientes.cuit, dbo.pedidos_c.nro_pedido, 
                      dbo.pedidos_c.fecha_alta, dbo.Clientes.cod_localidad_nro, dbo.Clientes.localidad, dbo.pedidos_c.anulado AS anulado_c, 
                      dbo.pedidos_d.anulado AS anulado_d, dbo.pedidos_c.id_estado_pedido, dbo.Clientes.cod_calificacion
FROM         dbo.personal RIGHT OUTER JOIN
                      dbo.pedidos_c INNER JOIN
                      dbo.Clientes ON dbo.pedidos_c.cod_cliente = dbo.Clientes.cod_cli INNER JOIN
                      dbo.pedidos_d ON dbo.pedidos_c.empresa = dbo.pedidos_d.empresa AND dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido LEFT OUTER JOIN
                      dbo.Operadores ON dbo.pedidos_c.cod_vendedor = dbo.Operadores.cod_operador ON 
                      dbo.personal.cod_personal = dbo.Operadores.cod_personal
WHERE     (dbo.pedidos_c.fecha_alta > CONVERT(DATETIME, '2016-01-01 00:00:00', 102)) AND (dbo.pedidos_d.anulado = 'N') AND (dbo.pedidos_c.anulado = 'N')
ORDER BY dbo.pedidos_c.fecha_alta, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo

GO
---VIEW:dbo.movimientos_caja_v_anul---
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
---VIEW:dbo.tareas_detalle_v---
CREATE VIEW [dbo].[tareas_detalle_v] AS

CREATE VIEW tareas_detalle_v AS
	SELECT td.*, tc.anulado, o.cod_articulo, o.cod_color_articulo, o.version, c.vigente, a.vigente articulo_vigente, a.naturaleza
	FROM Tareas_detalle td
	INNER JOIN Tareas_cabecera tc ON tc.nro_orden_fabricacion = td.nro_orden_fabricacion AND tc.nro_tarea = td.nro_tarea
	INNER JOIN Orden_fabricacion o ON o.nro_orden_fabricacion = tc.nro_orden_fabricacion
	INNER JOIN colores_por_articulo c ON c.cod_articulo = o.cod_articulo AND c.cod_color_articulo = o.cod_color_articulo
	INNER JOIN articulos a ON a.cod_articulo = o.cod_articulo


GO
---VIEW:dbo.VIEW2---
CREATE VIEW [dbo].[VIEW2] AS
CREATE VIEW dbo.VIEW2
AS
SELECT     TOP 100 PERCENT empresa, 1 AS punto_venta, 'REC' AS tipo_docum, nro_recibo AS numero, 'R' AS letra, nro_recibo AS nro_comprobante, 
                      anulado, NULL AS tipo_docum_2, dbo.IfNullZero(cod_cliente) AS cod_cliente, NULL AS Expr1, cod_usuario, NULL AS cancel_nro_documento, NULL 
                      AS causa, CAST(observaciones AS VARCHAR(8000)) AS observaciones, fecha_documento AS fecha, fecha_alta, fecha_baja, fecha_ultima_mod, 
                      importe_total, importe_pendiente, NULL AS importe_neto, NULL AS importe_no_gravado, NULL AS iva_importe_1, NULL AS iva_porc_1, NULL 
                      AS iva_importe_2, NULL AS iva_porc_2, NULL AS iva_importe_3, NULL AS iva_porc_3, NULL AS cotizacion_dolar, NULL 
                      AS descuento_comercial_importe, NULL AS descuento_comercial_porc, NULL AS descuento_despacho_importe, NULL AS cod_forma_pago, NULL 
                      AS cae, NULL AS cae_vencimiento, NULL AS cae_obtencion_fecha, NULL AS cae_obtencion_observaciones, NULL AS cae_obtencion_usuario, NULL 
                      AS mail_enviado, NULL AS tiene_detalle, NULL AS dias_promedio_pago, cod_asiento_contable, cod_ecommerce_order, nro_recibo, 
                      cod_importe_operacion, operacion_tipo, imputacion, recibido_de, fecha_documento, fecha_ponderada_pago
FROM         dbo.recibo
WHERE     (anulado = 'N') AND (nro_recibo > 0)
ORDER BY fecha_alta DESC

GO
---VIEW:dbo.movimientos_caja_v_noanul---
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
---VIEW:dbo.ultimos_precios_mp---
CREATE VIEW [dbo].[ultimos_precios_mp] AS
CREATE VIEW dbo.ultimos_precios_mp
AS
SELECT     d.cod_material, d.cod_color,
                          (SELECT     TOP 1 ISNULL(d2.precio_unitario, 0)
                            FROM          Ordenes_compra_detalle d2
                            WHERE      d.cod_material = d2.cod_material AND d.cod_color = d2.cod_color
                            ORDER BY d2.fecha_alta DESC) AS Expr1
FROM         dbo.Ordenes_compra_detalle d INNER JOIN
                      dbo.Ordenes_compra_cabecera c ON d.cod_orden_de_compra = c.cod_orden_de_compra
WHERE     (c.es_hexagono = 'N')
GROUP BY d.cod_material, d.cod_color

GO
---VIEW:dbo.cuenta_corriente_historica_proveedor---
CREATE VIEW [dbo].[cuenta_corriente_historica_proveedor] AS
/*		((CASE tipo_doc 
			WHEN 'NCR' THEN (-1)
			ELSE (1)
		END) * total_doc) total_documento
empresa, cod_prov, tipo_doc, nro_doc, fecha, total_doc importe_debe (FAC NDB), total_doc importe_haber (NCR OP)*/
CREATE VIEW dbo.cuenta_corriente_historica_proveedor
AS
SELECT     empresa, cod_prov, tipo_doc AS tipo_docum, nro_doc AS nro_docum, fecha AS documento_fecha, (CASE tipo_doc WHEN 'NCR' THEN 0 ELSE total_doc END) 
                      AS importe_debe, (CASE tipo_doc WHEN 'NCR' THEN total_doc ELSE 0 END) AS importe_haber, letra
FROM         dbo.compras_cabecera
WHERE     (grado = 'M')
UNION
SELECT     empresa, cod_prov, tipo_docum, orden_pago_nro AS nro_docum, fecha_orden_pago AS documento_fecha, 0 AS importe_debe, 
                      importe_a_pagar AS importe_haber, NULL AS letra
FROM         dbo.ordenes_de_pago
WHERE     (anulado = 'N') OR
                      (anulado IS NULL)

GO
---VIEW:dbo.banco_propio_v---
CREATE VIEW [dbo].[banco_propio_v] AS

CREATE VIEW [dbo].[banco_propio_v] AS
	SELECT b.nombre, bp.*
	FROM banco b
	INNER JOIN banco_propio bp ON b.cod_banco = bp.cod_banco
GO
---VIEW:dbo.saldos_historicos_proveedor---
CREATE VIEW [dbo].[saldos_historicos_proveedor] AS

CREATE VIEW saldos_historicos_proveedor
AS
(
	SELECT empresa, cod_prov, (sum(importe_debe - importe_haber)) saldo 
	FROM cuenta_corriente_historica_proveedor
	GROUP BY empresa, cod_prov
)
GO
---VIEW:dbo.caja_v---
CREATE VIEW [dbo].[caja_v] AS

CREATE VIEW caja_v AS
	SELECT		c.cod_caja, c.cod_duenio, c.cod_caja_padre, c.nombre, c.fecha_limite, c.dias_cierre, ISNULL(c.importe_efectivo, 0) AS importe_efectivo, c.importe_descubierto, 
				c.importe_maximo, c.cod_imputacion, c.caja_banco, c.anulado, c.fecha_alta, c.fecha_baja, c.fecha_ultima_mod, SUM(ISNULL(g.importe, 0)) AS importe_gastitos, c.disp_para_negociar
	FROM		dbo.caja AS c LEFT OUTER JOIN
				dbo.gastito AS g ON c.cod_caja = g.cod_caja AND g.cod_rendicion_gastos IS NULL
	GROUP BY	c.cod_caja, c.cod_duenio, c.cod_caja_padre, c.nombre, c.fecha_limite, c.dias_cierre, c.importe_efectivo, c.importe_descubierto, c.importe_maximo, c.cod_imputacion,
				c.caja_banco, c.anulado, c.fecha_alta, c.fecha_baja, c.fecha_ultima_mod, c.disp_para_negociar
GO
---VIEW:dbo.facturacion_cantidades_por_articulo_v---
CREATE VIEW [dbo].[facturacion_cantidades_por_articulo_v] AS
CREATE VIEW dbo.facturacion_cantidades_por_articulo_v
AS
SELECT     dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.cod_almacen, 
                      dbo.documentos_cantidades.cod_articulo, dbo.articulos.denom_articulo, dbo.documentos_cantidades.cod_color_articulo, 
                      dbo.documentos_cantidades.cantidad, (CASE tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) * ISNULL(precio_unitario_final, 0) precio_unitario_final, 
                      (CASE tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) * cantidad * precio_unitario_final total, dbo.articulos.cod_linea, 
                      dbo.lineas_productos.denom_linea, dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.catalogo
FROM         dbo.documentos_cantidades INNER JOIN
                      dbo.colores_por_articulo ON dbo.documentos_cantidades.cod_articulo = dbo.colores_por_articulo.cod_articulo AND 
                      dbo.documentos_cantidades.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo INNER JOIN
                      dbo.articulos ON dbo.colores_por_articulo.cod_articulo = dbo.articulos.cod_articulo INNER JOIN
                      dbo.lineas_productos ON dbo.articulos.cod_linea = dbo.lineas_productos.cod_linea

GO
---VIEW:dbo.patrones_se_vigentes_v---
CREATE VIEW [dbo].[patrones_se_vigentes_v] AS
CREATE VIEW dbo.patrones_se_vigentes_v
AS
SELECT     TOP 100 PERCENT dbo.Patrones_mp_detalle.cod_seccion, dbo.Patrones_mp_cabecera.cod_articulo, dbo.articulos.denom_articulo, 
                      dbo.Patrones_mp_detalle.cod_color_articulo, dbo.Patrones_mp_detalle.nro_item, dbo.Patrones_mp_detalle.version, 
                      dbo.Patrones_mp_detalle.conjunto, dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material, 
                      dbo.Patrones_mp_detalle.fecha_alta, dbo.Patrones_mp_detalle.consumo_par, dbo.Patrones_mp_cabecera.tipo_patron, 
                      dbo.materiales.factor_conversion, dbo.Patrones_mp_cabecera.borrador, dbo.materiales.unidad_medida AS UM, dbo.materiales.produccion_interna, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.aprob_produccion
FROM         dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo INNER JOIN
                      dbo.Patrones_mp_cabecera INNER JOIN
                      dbo.Patrones_mp_detalle ON dbo.Patrones_mp_cabecera.version = dbo.Patrones_mp_detalle.version AND 
                      dbo.Patrones_mp_cabecera.cod_color_articulo = dbo.Patrones_mp_detalle.cod_color_articulo AND 
                      dbo.Patrones_mp_cabecera.cod_articulo = dbo.Patrones_mp_detalle.cod_articulo INNER JOIN
                      dbo.materiales ON dbo.Patrones_mp_detalle.cod_material = dbo.materiales.cod_material ON 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.Patrones_mp_cabecera.cod_color_articulo AND 
                      dbo.colores_por_articulo.cod_articulo = dbo.Patrones_mp_cabecera.cod_articulo
WHERE     (dbo.articulos.vigente = 'S') AND (dbo.colores_por_articulo.vigente = 'S') AND (dbo.Patrones_mp_cabecera.version_actual = N'S') AND 
                      (dbo.articulos.naturaleza = N'se')
ORDER BY dbo.Patrones_mp_cabecera.cod_articulo, dbo.Patrones_mp_detalle.nro_item

GO
---VIEW:dbo.stock_pedidos_pendientes_con_estado_v---
CREATE VIEW [dbo].[stock_pedidos_pendientes_con_estado_v] AS
CREATE VIEW dbo.stock_pedidos_pendientes_con_estado_v
AS
SELECT     TOP 100 PERCENT dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, SUM(dbo.pedidos_d.pendiente) AS cant_pend, 
                      SUM(dbo.pedidos_d.pend_1) AS pend_1, SUM(dbo.pedidos_d.pend_2) AS pend_2, SUM(dbo.pedidos_d.pend_3) AS pend_3, 
                      SUM(dbo.pedidos_d.pend_4) AS pend_4, SUM(dbo.pedidos_d.pend_5) AS pend_5, SUM(dbo.pedidos_d.pend_6) AS pend_6, 
                      SUM(dbo.pedidos_d.pend_7) AS pend_7, SUM(dbo.pedidos_d.pend_8) AS pend_8, SUM(dbo.pedidos_d.pend_9) AS pend_9, 
                      SUM(dbo.pedidos_d.pend_10) AS pend_10, dbo.pedidos_d.anulado AS anulado_d
FROM         dbo.pedidos_c INNER JOIN
                      dbo.Clientes ON dbo.pedidos_c.cod_cliente = dbo.Clientes.cod_cli INNER JOIN
                      dbo.pedidos_d ON dbo.pedidos_c.empresa = dbo.pedidos_d.empresa AND dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido
WHERE     (dbo.pedidos_c.fecha_alta > CONVERT(DATETIME, '2017-01-01 00:00:00', 102)) AND (dbo.pedidos_c.id_estado_pedido = 1 OR
                      dbo.pedidos_c.id_estado_pedido = 2 OR
                      dbo.pedidos_c.id_estado_pedido IS NULL)
GROUP BY dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, dbo.pedidos_d.anulado, dbo.pedidos_c.cod_almacen, dbo.pedidos_c.anulado
HAVING      (dbo.pedidos_d.anulado = 'N') AND (dbo.pedidos_c.cod_almacen = '01') AND (dbo.pedidos_c.anulado = 'N')
ORDER BY dbo.pedidos_c.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo

GO
---VIEW:dbo.Pedidos_rentabilidad_v---
CREATE VIEW [dbo].[Pedidos_rentabilidad_v] AS
CREATE VIEW dbo.Pedidos_rentabilidad_v
AS
SELECT     p.nro_pedido AS nro_pedido, p.cod_articulo, dbo.costo_producto_total_V.denom_articulo, p.cod_color_articulo, p.cantidad, p.precio_unitario, 
                      dbo.costo_producto_total_V.cod_linea, dbo.costo_producto_total_V.costo, dbo.costo_producto_total_V.costo_linea, 
                      dbo.costo_producto_total_V.costo_total, p.subtotal, dbo.costo_producto_total_V.costo_total * p.cantidad AS subt_costo, 
                      p.subtotal - dbo.costo_producto_total_V.costo_total * p.cantidad AS subt_renta
FROM         (SELECT     TOP 100 PERCENT dbo.pedidos_c.nro_pedido, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, 
                                              dbo.pedidos_d.cantidad AS cantidad, dbo.pedidos_d.precio_unitario, dbo.pedidos_d.anulado, 
                                              dbo.pedidos_d.cantidad * dbo.pedidos_d.precio_unitario AS subtotal
                       FROM          dbo.pedidos_c INNER JOIN
                                              dbo.pedidos_d ON dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido
                       WHERE      (dbo.pedidos_c.anulado = 'N') AND (dbo.pedidos_d.anulado = 'n') AND (dbo.pedidos_c.fecha_alta > CONVERT(DATETIME, 
                                              '2015-01-01 00:00:00', 102))) p INNER JOIN
                      dbo.costo_producto_total_V ON p.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.costo_producto_total_V.cod_color_articulo AND 
                      p.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.costo_producto_total_V.cod_articulo

GO
---VIEW:dbo.costo_mp_semielaborado_detalle_v---
CREATE VIEW [dbo].[costo_mp_semielaborado_detalle_v] AS
CREATE VIEW dbo.costo_mp_semielaborado_detalle_v
AS
SELECT     dbo.patrones_se_vigentes_v.cod_articulo, dbo.patrones_se_vigentes_v.denom_articulo, dbo.patrones_se_vigentes_v.cod_color_articulo, 
                      dbo.patrones_se_vigentes_v.tipo_patron, dbo.patrones_se_vigentes_v.cod_material, dbo.Materias_primas.precio_unitario AS precio, 
                      dbo.materiales.denom_material AS material, dbo.patrones_se_vigentes_v.cod_color_material, dbo.patrones_se_vigentes_v.consumo_par, 
                      dbo.conjuntos.denom_conjunto, dbo.conjuntos.conjunto, dbo.patrones_se_vigentes_v.factor_conversion, dbo.patrones_se_vigentes_v.cod_seccion, 
                      dbo.subrubros_materias_primas.denom_subrubro, dbo.rubros_materias_primas.denom_rubro, dbo.articulos.cod_linea, dbo.materiales.cod_subrubro, 
                      dbo.Materias_primas.precio_unitario * dbo.patrones_se_vigentes_v.consumo_par / dbo.patrones_se_vigentes_v.factor_conversion AS costo
FROM         dbo.patrones_se_vigentes_v INNER JOIN
                      dbo.conjuntos ON dbo.patrones_se_vigentes_v.conjunto = dbo.conjuntos.conjunto INNER JOIN
                      dbo.articulos ON dbo.patrones_se_vigentes_v.cod_articulo = dbo.articulos.cod_articulo INNER JOIN
                      dbo.rubros_materias_primas INNER JOIN
                      dbo.materiales ON dbo.rubros_materias_primas.cod_rubro = dbo.materiales.cod_rubro INNER JOIN
                      dbo.subrubros_materias_primas ON dbo.materiales.cod_rubro = dbo.subrubros_materias_primas.cod_rubro AND 
                      dbo.materiales.cod_subrubro = dbo.subrubros_materias_primas.cod_subrubro INNER JOIN
                      dbo.Materias_primas ON dbo.materiales.cod_material = dbo.Materias_primas.cod_material ON 
                      dbo.patrones_se_vigentes_v.cod_material = dbo.Materias_primas.cod_material AND 
                      dbo.patrones_se_vigentes_v.cod_color_material = dbo.Materias_primas.cod_color
GROUP BY dbo.conjuntos.denom_conjunto, dbo.patrones_se_vigentes_v.consumo_par, dbo.conjuntos.conjunto, dbo.materiales.denom_material, 
                      dbo.patrones_se_vigentes_v.cod_color_material, dbo.conjuntos.conjunto, dbo.materiales.cod_rubro, dbo.materiales.cod_subrubro, 
                      dbo.subrubros_materias_primas.denom_subrubro, dbo.rubros_materias_primas.denom_rubro, dbo.Materias_primas.precio_unitario, 
                      dbo.articulos.cod_linea, dbo.patrones_se_vigentes_v.cod_seccion, dbo.patrones_se_vigentes_v.cod_articulo, 
                      dbo.patrones_se_vigentes_v.denom_articulo, dbo.patrones_se_vigentes_v.cod_color_articulo, dbo.patrones_se_vigentes_v.cod_material, 
                      dbo.patrones_se_vigentes_v.tipo_patron, dbo.patrones_se_vigentes_v.factor_conversion, 
                      dbo.Materias_primas.precio_unitario * dbo.patrones_se_vigentes_v.consumo_par / dbo.patrones_se_vigentes_v.factor_conversion

GO
---VIEW:dbo.VIEW1---
CREATE VIEW [dbo].[VIEW1] AS
CREATE VIEW dbo.VIEW1
AS
SELECT     situacion, cod_seccion, cod_articulo, denom_articulo, cod_color_articulo, SUM(cantidad) AS cant
FROM         dbo.tareas_incumplidas_v
GROUP BY situacion, cod_seccion, cod_articulo, denom_articulo, cod_color_articulo
HAVING      (cod_seccion = 60) OR
                      (cod_seccion = 62)

GO
---VIEW:dbo.permisos_por_usuarios_por_caja_v---
CREATE VIEW [dbo].[permisos_por_usuarios_por_caja_v] AS
CREATE VIEW [dbo].[permisos_por_usuarios_por_caja_v] AS
	SELECT		c.cod_caja, c.nombre, c.anulado, p.cod_usuario,p.cod_permiso,
				(CASE WHEN (SELECT count(*) FROM cuenta_bancaria cb WHERE cb.cod_caja = c.cod_caja) > 0 THEN 'S' ELSE 'N' END) es_caja_banco
	FROM		permisos_por_usuarios_por_caja p
	INNER JOIN	caja c ON p.cod_caja = c.cod_caja
GO
---VIEW:dbo.notificaciones_por_usuario_v---
CREATE VIEW [dbo].[notificaciones_por_usuario_v] AS
--Es porque la clase NotificacionPorUsuario Usuario y necesita todos sus campos para el fill
--Y además es para que aparezca la fecha y así poder filtrar en el getListObject

CREATE VIEW notificaciones_por_usuario_v AS
SELECT
	a.cod_notificacion, a.vista, a.anulado, a.eliminable, CONVERT(CHAR(19), a.fecha_ultima_mod, 120) fecha_ultima_mod,
	c.cod_usuario, c.tipo, c.cod_personal, c.cod_contacto, c.fechaAlta, c.fechaBaja, c.fechaUltimaAct, c.fechaUltimaMod
FROM notificaciones_por_usuario a
INNER JOIN users c ON a.cod_usuario = c.cod_usuario
GO
---VIEW:dbo.usuarios_por_tipo_notificacion_v---
CREATE VIEW [dbo].[usuarios_por_tipo_notificacion_v] AS

CREATE VIEW usuarios_por_tipo_notificacion_v AS

SELECT a.cod_tipo_notificacion, a.eliminable, b.*
FROM usuarios_por_tipo_notificacion a
INNER JOIN users b ON a.cod_usuario = b.cod_usuario
GO
---VIEW:demian.movimientos_caja_v_chq---
CREATE VIEW [demian].[movimientos_caja_v_chq] AS
CREATE VIEW movimientos_caja_v_chq AS 

SELECT * FROM (
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
) a

UNION ALL

SELECT * FROM (
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
		WHERE ac2.entrada_salida = 'E' AND acc.cod_acreditar_debitar_cheque = ac2.cod_acreditar_debitar_cheque AND acc.empresa = ac2.empresa) para,
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
		ipoc.cod_caja, dbo.relativeDate(acc.fecha,'t
oday',0) fecha,
		ac.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM acreditar_debitar_cheque_d ac
	INNER JOIN acreditar_debitar_cheque_c acc ON acc.cod_acreditar_debitar_cheque = ac.cod_acreditar_debitar_cheque AND ac.empresa = acc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ac.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE acc.tipo = 'C'
) b


GO
---VIEW:dbo.roles_por_tipo_notificacion_v---
CREATE VIEW [dbo].[roles_por_tipo_notificacion_v] AS

CREATE VIEW roles_por_tipo_notificacion_v AS
SELECT a.cod_tipo_notificacion, a.eliminable, b.*
FROM roles_por_tipo_notificacion a
INNER JOIN roles b ON a.cod_rol = b.cod_rol
GO
---VIEW:dbo.movimientos_caja_v_chq---
CREATE VIEW [dbo].[movimientos_caja_v_chq] AS
CREATE VIEW movimientos_caja_v_chq AS 

SELECT * FROM (
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
) a

UNION ALL

SELECT * FROM (
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
		WHERE ac2.entrada_salida = 'E' AND acc.cod_acreditar_debitar_cheque = ac2.cod_acreditar_debitar_cheque AND acc.empresa = ac2.empresa) para,
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
		ipoc.cod_caja, dbo.relativeDate(acc.fecha,'t
oday',0) fecha,
		ac.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM acreditar_debitar_cheque_d ac
	INNER JOIN acreditar_debitar_cheque_c acc ON acc.cod_acreditar_debitar_cheque = ac.cod_acreditar_debitar_cheque AND ac.empresa = acc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ac.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE acc.tipo = 'C'
) b


GO
---VIEW:dbo.syssegments---
CREATE VIEW [dbo].[syssegments] AS
CREATE VIEW syssegments (segment, name, status) AS
	SELECT  0, 'system'     , 0  UNION
	SELECT	1, 'default'    , 1  UNION
	SELECT	2, 'logsegment' , 0

GO
---VIEW:dbo.sysconstraints---
CREATE VIEW [dbo].[sysconstraints] AS
CREATE VIEW sysconstraints AS SELECT
	constid = convert(int, id),
	id = convert(int, parent_obj),
	colid = convert(smallint, info),
	spare1 = convert(tinyint, 0),
	status = convert(int,
			CASE xtype
				WHEN 'PK' THEN 1 WHEN 'UQ' THEN 2 WHEN 'F' THEN 3
				WHEN 'C' THEN 4 WHEN 'D' THEN 5 ELSE 0 END
			+ CASE WHEN info != 0			-- CNST_COLUMN / CNST_TABLE
					THEN (16) ELSE (32) END
			+ CASE WHEN (status & 16)!=0	-- CNST_CLINDEX
					THEN (512) ELSE 0 END
			+ CASE WHEN (status & 32)!=0	-- CNST_NCLINDEX
					THEN (1024) ELSE 0 END
			+ (2048)						-- CNST_NOTDEFERRABLE
			+ CASE WHEN (status & 256)!=0	-- CNST_DISABLE
					THEN (16384) ELSE 0 END
			+ CASE WHEN (status & 512)!=0	-- CNST_ENABLE
					THEN (32767) ELSE 0 END
			+ CASE WHEN (status & 4)!=0		-- CNST_NONAME
					THEN (131072) ELSE 0 END
			+ CASE WHEN (status & 1)!=0		-- CNST_NEW
					THEN (1048576) ELSE 0 END
			+ CASE WHEN (status & 1024)!=0	-- CNST_REPL
					THEN (2097152) ELSE 0 END),
	actions = convert(int,  4096),
	error = convert(int, 0)
FROM sysobjects WHERE xtype in ('C', 'F', 'PK', 'UQ', 'D')
					AND (status & 64) = 0

GO
---VIEW:dbo.prestashop_imagenes_update---
CREATE VIEW [dbo].[prestashop_imagenes_update] AS
CREATE VIEW dbo.prestashop_imagenes_update
AS
SELECT     cpa.cod_articulo, cpa.cod_color_articulo,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'e') AS e,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'd') AS d,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'a') AS a,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 't') AS t,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'b') AS b,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'u') AS u,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'f') AS f,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'i1') AS i1,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'i2') AS i2,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'i3') AS i3
FROM         dbo.colores_por_articulo cpa INNER JOIN
                      dbo.articulos ON cpa.cod_articulo = dbo.articulos.cod_articulo
WHERE     (cpa.ecommerce_existe = 'S')

GO
---VIEW:dbo.pedidos_clientes_sin_mora_vw---
CREATE VIEW [dbo].[pedidos_clientes_sin_mora_vw] AS
CREATE VIEW dbo.pedidos_clientes_sin_mora_vw
AS
SELECT     TOP 100 PERCENT dbo.pedidos_detalle.cod_articulo, dbo.pedidos_detalle.cod_color_articulo, SUM(dbo.pedidos_detalle.cantidad_pendiente) 
                      AS Pendiente, SUM(dbo.pedidos_detalle.cantidad) AS Pedido, SUM(dbo.pedidos_detalle.pend_1) AS P1, SUM(dbo.pedidos_detalle.pend_2) AS P2, 
                      SUM(dbo.pedidos_detalle.pend_3) AS P3, SUM(dbo.pedidos_detalle.pend_4) AS P4, SUM(dbo.pedidos_detalle.pend_5) AS P5, 
                      SUM(dbo.pedidos_detalle.pend_6) AS P6, SUM(dbo.pedidos_detalle.pend_7) AS P7, SUM(dbo.pedidos_detalle.pend_8) AS P8, 
                      SUM(dbo.pedidos_detalle.pend_9) AS P9, SUM(dbo.pedidos_detalle.pend_10) AS P10
FROM         dbo.pedidos_detalle INNER JOIN
                      dbo.pedidos_cabecera ON dbo.pedidos_detalle.cod_empresa = dbo.pedidos_cabecera.cod_empresa AND 
                      dbo.pedidos_detalle.cod_sucursal = dbo.pedidos_cabecera.cod_sucursal AND 
                      dbo.pedidos_detalle.nro_pedido = dbo.pedidos_cabecera.nro_pedido INNER JOIN
                      dbo.Clientes ON dbo.pedidos_cabecera.cod_cliente = dbo.Clientes.cod_cliente
WHERE     (dbo.Clientes.cod_calificacion = 01 OR
                      dbo.Clientes.cod_calificacion = 02 OR
                      dbo.Clientes.cod_calificacion = 03 OR
                      dbo.Clientes.cod_calificacion = 04) AND (dbo.pedidos_cabecera.anulado = 'N') AND (dbo.pedidos_detalle.anulado = 'N')
GROUP BY dbo.pedidos_detalle.cod_articulo, dbo.pedidos_detalle.cod_color_articulo
HAVING      (SUM(dbo.pedidos_detalle.cantidad_pendiente) <> 0)
ORDER BY dbo.pedidos_detalle.cod_articulo, dbo.pedidos_detalle.cod_color_articulo

GO
---VIEW:dbo.ecomexperts_articulos_update_v---
CREATE VIEW [dbo].[ecomexperts_articulos_update_v] AS
CREATE VIEW dbo.ecomexperts_articulos_update_v
AS
SELECT     TOP 100 PERCENT dbo.colores_por_articulo.ml_reference AS sku, dbo.colores_por_articulo.ml_name AS titulo, '' AS garantia, 21 AS impuesto, 
                      'default' AS precio_titulo1, dbo.colores_por_articulo.mlibre_precio AS precio_valor1, dbo.stock_01_14_20_por_talle_v.cant_1 AS cantidad, 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle AS var_sku, 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle AS sku_variante, '' AS alt_id, 
                      '' AS ml_item_id, 
                      'SPIRAL SHOES Tienda Online.

*| SKATEBOARDING | SURF | LONGBOARDING | BMX | ZAPATILLAS URBANAS | INDUMENTARIA|*
' + CAST(dbo.colores_por_articulo.ml_description
                       AS VARCHAR(4000)) 
                      + '

Visita nuestra tienda oficial en Mercado Libre:
https://www.mercadolibre.com.ar/tienda/spiral

TALLES CALZADO:
Revisa la guía de talles . Los talles publicados son argentinos. Para un ajuste ideal, mide tu pie apoyando el talón descalzo contra una pared y mide la distancia hasta la punta del dedo más largo. Consulta la equivalencia en centímetros.

PREGUNTAS FRECUENTES:

ENVÍOS:

A Domicilio: Enviamos a todo el país a través de MercadoEnvíos.
A Sucursal: Si retiras en una sucursal del correo, lleva DNI y el código de seguimiento.
Envío FLEX: Entregas en el mismo día si el pedido se realiza antes de las 14 hs. Si es después, llegará al siguiente día hábil (hasta las 22 hs).
AUTENTICIDAD:
Todos los productos son 100% originales. Estás comprando directamente a Spiral Shoes.

POSTVENTA Y CAMBIOS:
Nuestro equipo de Atención al Cliente está disponible de lunes a viernes de 8:30 a 19 hs.
Devoluciones y cambios: ¡Gratis!'
                       AS descripcion, '' AS tags, 0 AS costo_unitario, 'Color' AS atributo2, dbo.colores_por_articulo.denom_color AS variante2, 'Talle' AS atributo1, 
                      dbo.stock_01_14_20_por_talle_v.Talle AS variante1, 1 AS active, 'gold_special' AS var_ml_listing_type_id, '' AS var_ml_category_id, 
                      '' AS var_ml_warranty, 'https://spiralshoes.com/zapatillas/jpg/' + piu.e AS imagen1, 'https://spiralshoes.com/zapatillas/jpg/' + piu.d AS imagen2, 
                      'https://spiralshoes.com/zapatillas/jpg/' + piu.a AS imagen3, 'https://spiralshoes.com/zapatillas/jpg/' + piu.t AS imagen4, 
                      'https://spiralshoes.com/zapatillas/jpg/' + piu.b AS imagen5, 'https://spiralshoes.com/zapatillas/jpg/' + piu.u AS imagen6, 
                      'https://spiralshoes.com/zapatillas/jpg/' + piu.f AS imagen7, 'https://spiralshoes.com/zapatillas/jpg/' + piu.i1 AS imagen8, 
                      'https://spiralshoes.com/zapatillas/jpg/' + piu.i2 AS imagen9, 'https://spiralshoes.com/zapatillas/jpg/' + piu.i3 AS imagen10, 
                      'El producto no tiene código registrado' AS var_motivo_de_gtin_vacío
FROM         dbo.prestashop_imagenes_update piu INNER JOIN
                      dbo.familias_producto INNER JOIN
                      dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo ON 
                      dbo.familias_producto.id = dbo.articulos.cod_familia_producto INNER JOIN
                      dbo.Marcas ON dbo.articulos.cod_marca = dbo.Marcas.cod_marca INNER JOIN
                      dbo.lineas_productos ON dbo.articulos.cod_linea = dbo.lineas_productos.cod_linea INNER JOIN
                      dbo.stock_01_14_20_por_talle_v ON dbo.colores_por_articulo.cod_articulo = dbo.stock_01_14_20_por_talle_v.cod_articulo AND 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.stock_01_14_20_por_talle_v.cod_color_articulo ON 
                      piu.c
od_articulo = dbo.colores_por_articulo.cod_articulo AND piu.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo
WHERE     (dbo.stock_01_14_20_por_talle_v.Talle <> 'X') AND (dbo.stock_01_14_20_por_talle_v.Talle IS NOT NULL) AND 
                      (dbo.stock_01_14_20_por_talle_v.Talle <> '') AND (dbo.colores_por_articulo.ecommerce_existe = 'S') AND 
                      (dbo.colores_por_articulo.id_tipo_producto_stock <> '06') AND (dbo.colores_por_articulo.ecommerce_existe = 'S')

GO
---VIEW:dbo.egresos_op_vw---
CREATE VIEW [dbo].[egresos_op_vw] AS
CREATE VIEW dbo.egresos_op_vw
AS
SELECT     empresa, sucursal_empresa, fecha_orden_pago AS fecha, operacion_tipo AS tipo, tipo_docum AS docum, orden_pago_nro AS op_nro, cod_prov, 
                      importe_a_pagar AS importe, imput_obligacion_paga AS imputacion, nro_rendicion_ff
FROM         dbo.ordenes_de_pago
WHERE     (anulado <> N'S' OR
                      anulado IS NULL) AND (nro_rendicion_ff IS NULL)

GO
---VIEW:dbo.egresos_ff_vw---
CREATE VIEW [dbo].[egresos_ff_vw] AS
CREATE VIEW dbo.egresos_ff
AS
SELECT     dbo.gastos_rendicion.empresa, dbo.gastos_rendicion.sucursal_empresa, dbo.gastos_rendicion.gasto_fecha AS fecha, 
                      dbo.gastos_rendicion.operacion_tipo AS tipo, dbo.ordenes_de_pago.tipo_docum AS docum, dbo.ordenes_de_pago.orden_pago_nro AS op_nro, 
                      '""' AS cod_prov, dbo.gastos_rendicion.total_importe AS importe, dbo.gastos_rendicion.imputacion, dbo.gastos_rendicion.nro_rendicion_ff
FROM         dbo.gastos_rendicion INNER JOIN
                      dbo.ordenes_de_pago ON dbo.gastos_rendicion.clave_access = dbo.ordenes_de_pago.clave_access

GO
---VIEW:dbo.egresos_vw---
CREATE VIEW [dbo].[egresos_vw] AS
CREATE VIEW dbo.egresos_vw
AS
SELECT     dbo.gastos_rendicion.empresa, dbo.gastos_rendicion.sucursal_empresa, dbo.gastos_rendicion.gasto_fecha AS fecha, 
                      dbo.gastos_rendicion.operacion_tipo AS tipo, dbo.ordenes_de_pago.tipo_docum AS docum, dbo.ordenes_de_pago.orden_pago_nro AS op_nro, NULL 
                      AS cod_prov, dbo.gastos_rendicion.total_importe AS importe, dbo.gastos_rendicion.imputacion, dbo.gastos_rendicion.nro_rendicion_ff
FROM         dbo.gastos_rendicion INNER JOIN
                      dbo.ordenes_de_pago ON dbo.gastos_rendicion.clave_access = dbo.ordenes_de_pago.clave_access
UNION
SELECT     empresa, sucursal_empresa, fecha_orden_pago AS fecha, operacion_tipo AS tipo, tipo_docum AS docum, orden_pago_nro AS op_nro, cod_prov, 
                      importe_a_pagar AS importe, imput_obligacion_paga AS imputacion, nro_rendicion_ff
FROM         dbo.ordenes_de_pago
WHERE     (anulado <> N'S' OR
                      anulado IS NULL) AND (nro_rendicion_ff IS NULL)

GO
---VIEW:dbo.roles_por_usuario_v---
CREATE VIEW [dbo].[roles_por_usuario_v] AS

--View para no hacer un JOIN en el mapper
CREATE VIEW roles_por_usuario_v AS
SELECT rpu.cod_usuario cod_usuario, r.cod_rol cod_rol, r.nombre nombre, r.tipo tipo, r.anulado anulado
FROM roles_por_usuario rpu 
INNER JOIN roles r ON rpu.cod_rol = r.cod_rol 

GO
---VIEW:dbo.colores_por_articulo_v---
CREATE VIEW [dbo].[colores_por_articulo_v] AS

--View para no hacer un JOIN en el mapper
CREATE  VIEW colores_por_articulo_v AS
SELECT
	a.denom_articulo,
	cpa.cod_articulo,
	cpa.cod_color_articulo,
	cpa.denom_color,
	cpa.cod_variante,
	cpa.corte,
	cpa.vigente,
	cpa.fecha_de_baja,
	cpa.denom_color_abreviada,
	cpa.cod_masa,
	cpa.disenio,
	cpa.precio_minorista_usd,
	cpa.precio_mayorista_usd,
	cpa.precio_distrib,
	cpa.precio_distrib_minorista,
	cpa.fotografia,
	cpa.fecha_actualiz_precio,
	cpa.muestra_moneda,
	cpa.muestra_vip,
	cpa.muestra_porcentaje_vip,
	cpa.cod_material,
	cpa.cod_color,
	cpa.cod_en_cliente,
	cpa.denominacion_cliente,
	cpa.codigo_de_barras_cliente,
	cpa.cod_en_proveedor,
	cpa.precio_recargado,
	cpa.cod_compuesto_articulo,
	cpa.aprob_disenio,
	cpa.aprob_produccion,
	cpa.fotografia1,
	cpa.fotografia2,
	cpa.fotografia3,
	cpa.fotografia4,
	cpa.fotografia5,
	cpa.id_tipo_producto_stock,
	cpa.catalogo,
	cpa.stock_temp_ecommerce,
	cpa.stock_temp,
	cpa.prod_trim_temp,
	cpa.catalogo_orden_pagina,
	cpa.descuento_color_articulo,
	cpa.cod_base,
	cpa.cod_color_base,
	cpa.precio_minimo,
	cpa.cod_color_cliente,
	cpa.importacion_propia,
	cpa.precio_costo,
	cpa.validacion_stock,
	cpa.fecha_validacion_stock,
	cpa.usuario_valida,
	cpa.fotografia6,
	cpa.fotografia7,
	cpa.fotografia8,
	cpa.zoom_lado_interno,
	cpa.zoom_puntera,
	cpa.zoom_caña,
	cpa.zoom_talon,
	cpa.texto_lado_interno,
	cpa.texto_puntera,
	cpa.texto_caña,
	cpa.texto_talon,
	cpa.texto_varios,
	cpa.comercializacion_libre,
	cpa.fechaAlta,
	cpa.fechaUltimaMod,
	cpa.en_produccion_temp,
	cpa.categoria_usuario,
	cpa.cod_mp_critico_1,
	cpa.cod_mp_critico_2,
	cpa.cod_mp_critico_3,
	cpa.cod_color_mp_critico_1,
	cpa.cod_color_mp_critico_2,
	cpa.cod_color_mp_critico_3,
	cpa.utiliza_cb_cliente,
	cpa.ecommerce_existe,
	cpa.ecommerce_fecha_ultima_sinc,
	cpa.ecommerce_nombre,
	cpa.ecommerce_info,
	cpa.ecommerce_forsale,
	cpa.ecommerce_condition,
	cpa.ecommerce_exclusive,
	cpa.ecommerce_featured,
	cpa.ecommerce_price1,
	cpa.ecommerce_price2,
	cpa.ecommerce_price3,
	cpa.ecommerce_image1,
	cpa.ecommerce_cod_category,
	cpa.seleccion,
	cpa.clasificacion_comercial,
cpa.referencia_web_mayorista,
	a.naturaleza
FROM colores_por_articulo cpa
INNER JOIN articulos a ON a.cod_articulo = cpa.cod_articulo


GO
---VIEW:dbo.documentos_cantidades---
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
---VIEW:dbo.mundial---
CREATE VIEW [dbo].[mundial] AS

CREATE VIEW mundial AS
SELECT j.id, j.nombre, ISNULL(puntaje, 0) puntaje FROM (
	SELECT
		j.id jugador,
		SUM((CASE WHEN pj.goles_1 = p.goles_1 AND pj.goles_2 = p.goles_2 THEN
			3
		ELSE
			(CASE WHEN (pj.goles_1 > pj.goles_2 AND p.goles_1 > p.goles_2) OR (pj.goles_1 < pj.goles_2 AND p.goles_1 < p.goles_2) OR (pj.goles_1 = pj.goles_2 AND p.goles_1 = p.goles_2) THEN
				2
			ELSE
				0
			END)
		END)) puntaje
	FROM mundial_partidos_jugador pj
	LEFT JOIN mundial_partidos p ON pj.id_partido = p.id
	LEFT JOIN mundial_jugadores j ON pj.id_jugador = j.id
	GROUP BY j.id
) a
RIGHT JOIN mundial_jugadores j ON a.jugador = j.id
GO
---VIEW:dbo.stock_01_14_20_por_talle_v---
CREATE VIEW [dbo].[stock_01_14_20_por_talle_v] AS
CREATE VIEW dbo.stock_01_14_20_por_talle_v
AS
SELECT     TOP 100 PERCENT cod_articulo, cod_color_articulo, Talle, cantidad, cant_1
FROM         (SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                              dbo.rango_talles.posic_1 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM          dbo.rango_talles INNER JOIN
                                              dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                  (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_1) AS cant_1
                                                    FROM          dbo.stock
                                                    WHERE      (cod_almacen = '01') OR
                                                                           (cod_almacen = '14') OR
                                                                           (cod_almacen = '20')
                                                    GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                              dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND
                                               stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                              dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_2 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_2) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_3 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_3) AS cant_1
                        
                           FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_4 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_4) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_5 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_5) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color
_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_6 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_6) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_7 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_7) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_8 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_8) AS cant_1

                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_9 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_9) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_10 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_10) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.col
ores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo) DERIVEDTBL
WHERE     (NOT (Talle IS NULL))

GO
---VIEW:dbo.mundial_detalle---
CREATE VIEW [dbo].[mundial_detalle] AS

CREATE VIEW mundial_detalle AS
SELECT 
	pj.id_jugador, j.nombre nombre_jugador, p.id id_partido, p.nombre nombre_partido, pj.goles_1, pj.goles_2,
	(CASE WHEN p.goles_1 IS NOT NULL AND p.goles_2 IS NOT NULL THEN 1 ELSE 0 END) jugado,
	(CASE WHEN pj.goles_1 = p.goles_1 AND pj.goles_2 = p.goles_2 THEN
		3
	ELSE
		(CASE WHEN (pj.goles_1 > pj.goles_2 AND p.goles_1 > p.goles_2) OR (pj.goles_1 < pj.goles_2 AND p.goles_1 < p.goles_2) OR (pj.goles_1 = pj.goles_2 AND p.goles_1 = p.goles_2) THEN
			2
		ELSE
			0
		END)
	END) puntaje
FROM mundial_partidos_jugador pj
LEFT JOIN mundial_partidos p ON pj.id_partido = p.id
LEFT JOIN mundial_jugadores j ON pj.id_jugador = j.id
GO
---VIEW:dbo.prestashop_articulos_update_v---
CREATE VIEW [dbo].[prestashop_articulos_update_v] AS
CREATE VIEW dbo.prestashop_articulos_update_v
AS
SELECT     TOP 100 PERCENT (CASE ecommerce_forsale WHEN 'S' THEN (1) ELSE 0 END) AS [On Sale], 0 AS [Online Only], '' AS EAN13, '' AS UPC, 0 AS [Eco-Tax],
                       (CASE [stock_01_14_20_por_talle_v].[cantidad] WHEN NULL THEN 0 ELSE [stock_01_14_20_por_talle_v].[cantidad] END) AS Quantity, 
                      1 AS [Minimal Quantity], 0 AS Price, dbo.colores_por_articulo.precio_mayorista_usd AS [Wholesale Price], '' AS Unity, 0 AS [Unit Price Ratio], 
                      0 AS [Additional Shipping Cost], dbo.colores_por_articulo.ecommerce_reference AS Reference, dbo.articulos.cod_articulo AS [Supplier Reference], 
                      0 AS [Supplier Unit Price (Tax Excl)], '' AS Location, 0 AS Width, 0 AS Height, 0 AS Depth, 0 AS Weight, 2 AS [Out of Stock], 0 AS [Delivery time], 
                      0 AS [Quantity Discount], 0 AS Customizable, 1 AS Status, '301-[category]' AS [Redirect Type], 0 AS id_type_redirected, 1 AS [Available for Order], 
                      '' AS [Date available], 0 AS show_condition, 'new' AS Condition, 1 AS [Show Price], 1 AS Indexed, 'both' AS Visibility, 0 AS [Is Pack Product], 
                      0 AS [Has attachments], 0 AS [Is Virtual Product], '' AS [Cache default Attribute], GETDATE() AS [Date Add], GETDATE() AS [Date Update], 
                      0 AS [Advanced Stock Management], 3 AS pack_stock_type, 1 AS State, dbo.colores_por_articulo.ecommerce_description AS Description, 
                      '' AS [Short Description], '' AS [Friendly URL], dbo.colores_por_articulo.ecommerce_name AS [Product Name], '' AS [Available Now], 
                      '' AS [Available Later], '' AS [Delivery time of instock products], '' AS [Delivery time of out-of-stock products with allowed orders], '' AS [Description AG], 
                      '' AS [Short Description AG], '' AS Tags, '' AS [Friendly URL AG], '' AS [Meta Description AG], '' AS [Meta Keywords AG], '' AS [Meta Title AG], 
                      dbo.colores_por_articulo.ecommerce_name AS [Product Name AG], '' AS [Available Now AG], '' AS [Available Later AG], 
                      '' AS [Delivery time of in-stock products AG], '' AS [Delivery time of out-of-stock products with allowed orders AG], '' AS [Tags AG], '' AS [Product URL], 
                      'Raíz|Inicio|' + dbo.colores_por_articulo.ecommerce_cod_category AS Category, dbo.colores_por_articulo.catalogo_orden_pagina AS [Position], 
                      'SPIRAL SHOES' AS Manufacturer, 'SPIRAL SHOES' AS Supplier, ISNULL(piu.e, '') + ';' + ISNULL(piu. d, '') + ';' + ISNULL(piu.a, '') + ';' + ISNULL(piu. t, '') 
                      + ';' + ISNULL(piu.b, '') + ';' + ISNULL(piu.u, '') + ';' + ISNULL(piu.f, '') + ';' + ISNULL(piu.i1, '') AS [Product Images], 
                      dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name
                       + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' AS [Product Images Caption], 
                      dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name
                       + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_po
r_articulo.cod_color_articulo
                       + ';' AS [Product Images Caption AG], 'AR Standard rate (21%)' AS Tax, '' AS [Product Carrier], '' AS [Product Accessories], '' AS [Attribute Group Tamaño],
                       dbo.colores_por_articulo.denom_color AS [Attribute Group Color], '' AS [Attribute Group Dimension], 
                      dbo.stock_01_14_20_por_talle_v.Talle AS [Attribute Group Talle], 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle AS [Combination Reference], 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo AS [Combination Supplier Reference], 
                      0 AS [Combination Supplier Unit Price Tax Excl], '' AS [Combination Location], '' AS [Combination EAN13], '' AS [Combination UPC], 
                      dbo.colores_por_articulo.precio_mayorista_usd / 1.21 AS [Combination Wholesale Price], 
                      dbo.colores_por_articulo.ecommerce_price1 / 1.21 AS [Combination Price], 0 AS [Combination EcoTax], 
                      dbo.stock_01_14_20_por_talle_v.cant_1 AS [Combination Quantity], 0 AS [Combination Weight], 0 AS [Combination Unit Price Impact], 
                      '' AS [Combination Is Default], 1 AS [Combination Minimal Quantity], 0 AS attr_low_stock_threshold, 0 AS attr_low_stock_alert, GETDATE() 
                      AS [Combination Attribute Available Date], ISNULL(piu.e, '') + ';' + ISNULL(piu. d, '') + ';' + ISNULL(piu.a, '') + ';' + ISNULL(piu. t, '') + ';' + ISNULL(piu.b, '') 
                      + ';' + ISNULL(piu.u, '') + ';' + ISNULL(piu.f, '') + ';' + ISNULL(piu.i1, '') AS [Combination Images], 
                      dbo.colores_por_articulo.composition AS [Attribute Group Composition], dbo.articulos.naturaleza, dbo.stock_01_14_20_por_talle_v.Talle, 
                      dbo.colores_por_articulo.ecommerce_cod_category AS familia_ecommerce, dbo.colores_por_articulo.ecommerce_existe, 
                      dbo.colores_por_articulo.ecomm_especific_price_identifier AS reduction_identifier, 
                      dbo.colores_por_articulo.ecomm_especific_price_reduction AS reduction, dbo.colores_por_articulo.ecomm_especific_price_from AS reduction_from, 
                      dbo.colores_por_articulo.ecomm_especific_price_to AS reduction_to, dbo.colores_por_articulo.catalogo, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.cod_color_articulo AS cod_color, 
                      dbo.colores_por_articulo.ecommerce_price3 AS Composition, 'ZAPATILLAS SPIRAL' + dbo.articulos.denom_articulo AS Description_ML, 
                      dbo.articulos.ml_denominacion
FROM         dbo.prestashop_imagenes_update piu INNER JOIN
                      dbo.familias_producto INNER JOIN
                      dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo ON 
                      dbo.familias_producto.id = dbo.articulos.cod_familia_producto INNER JOIN
                      dbo.Marcas ON dbo.articulos.cod_marca = dbo.Marcas.cod_marca INNER JOIN
                      dbo.lineas_productos ON dbo.articulos.cod_linea = dbo.lineas_productos.cod_linea INNER JOIN
                      dbo.stock_01_14_20_por_talle_v ON dbo.colores_por_articulo.cod_articulo = dbo.stock_01_14_20_por_talle_v.cod_articulo AND 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.stock_01_14_20_por_talle_v.cod_color_articulo ON 
                      piu.cod_articulo = dbo.colores_por_articulo.cod_articulo AND piu.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo
WHERE     (dbo.stock_01_14_20_por_talle_v.Talle <> 'X') AND (dbo.stock_01_14_20_por_talle_v.Talle IS NOT NULL) AND 
                      (dbo.stock_01_14_20_por_talle_v.Talle <> '') AND (dbo.articulos.natu
raleza = 'PT') AND (dbo.colores_por_articulo.ecommerce_existe = 'S') AND 
                      (dbo.colores_por_articulo.id_tipo_producto_stock <> '06') AND (dbo.stock_01_14_20_por_talle_v.cant_1 > 0)
ORDER BY dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle, 
                      dbo.colores_por_articulo.catalogo_orden_pagina

GO
(716 rows affected)
1> 