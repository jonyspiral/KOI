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
