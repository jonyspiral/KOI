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
