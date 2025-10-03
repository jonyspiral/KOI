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
