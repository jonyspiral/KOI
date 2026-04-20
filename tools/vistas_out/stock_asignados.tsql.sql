CREATE VIEW dbo.stock_asignados
AS
SELECT     TOP 100 PERCENT dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea, 
                      dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pe