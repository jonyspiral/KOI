CREATE VIEW dbo.egresos_vw
AS
SELECT     dbo.gastos_rendicion.empresa, dbo.gastos_rendicion.sucursal_empresa, dbo.gastos_rendicion.gasto_fecha AS fecha, 
                      dbo.gastos_rendicion.operacion_tipo AS tipo, dbo.ordenes_de_pago.tipo_docum 