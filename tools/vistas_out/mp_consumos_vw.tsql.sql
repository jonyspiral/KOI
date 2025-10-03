CREATE VIEW dbo.mp_consumos_vw
AS
SELECT     dbo.Tareas_detalle.fecha_salida_real AS fecha_movimiento, dbo.Consumos_tarea.cod_material, dbo.Consumos_tarea.cod_color, 
                      - (1 * ISNULL(dbo.Consumos_tarea.cant_consumo, 0)) AS Cant, - (