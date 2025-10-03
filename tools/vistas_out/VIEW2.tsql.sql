CREATE VIEW dbo.VIEW2
AS
SELECT     TOP 100 PERCENT empresa, 1 AS punto_venta, 'REC' AS tipo_docum, nro_recibo AS numero, 'R' AS letra, nro_recibo AS nro_comprobante, 
                      anulado, NULL AS tipo_docum_2, dbo.IfNullZero(cod_cliente) AS 