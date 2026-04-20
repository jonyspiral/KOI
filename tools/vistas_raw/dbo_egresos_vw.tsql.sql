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
