CREATE VIEW dbo.costo_mp_factura_v
AS
SELECT     TOP 100 PERCENT dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.punto_venta, 
                      dbo.documentos_cantidades.tipo_docum, dbo.documentos_cant