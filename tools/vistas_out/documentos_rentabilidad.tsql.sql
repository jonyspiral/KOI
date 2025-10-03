CREATE VIEW dbo.documentos_rentabilidad
AS
SELECT     TOP 100 PERCENT dbo.documentos.empresa, dbo.documentos.punto_venta, dbo.documentos.tipo_docum, dbo.documentos.numero, 
                      dbo.documentos.letra, dbo.documentos.anulado, dbo.documen