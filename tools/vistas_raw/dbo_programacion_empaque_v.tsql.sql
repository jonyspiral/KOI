CREATE VIEW [dbo].[programacion_empaque_v] AS
CREATE VIEW dbo.programacion_empaque_v
AS
SELECT     dbo.Orden_fabricacion.Confirmada, dbo.Orden_fabricacion.fecha_inicio, dbo.Tareas_cabecera.fecha_corte, dbo.Tareas_cabecera.fecha_aparado, 
                      dbo.Tareas_cabecera.fecha_armado, dbo.Tareas_cabecera.fecha_programacion, dbo.Orden_fabricacion.nro_plan, 
                      dbo.Tareas_cabecera.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, dbo.Tareas_cabecera.anulado, dbo.Orden_fabricacion.cod_articulo, 
                      dbo.articulos.denom_articulo, dbo.Orden_fabricacion.cod_color_articulo, dbo.Tareas_detalle.cod_seccion, 
                      dbo.Tareas_detalle.fecha_salida_programada, dbo.Tareas_cabecera.cantidad, dbo.Tareas_cabecera.cantidad_ultimo_paso_cumplido, 
                      dbo.Tareas_detalle.cumplido_paso, dbo.Tareas_cabecera.tipo_tarea, dbo.Tareas_cabecera.situacion, dbo.Tareas_cabecera.pos_1_cant, 
                      dbo.Tareas_cabecera.pos_2_cant, dbo.Tareas_cabecera.pos_3_cant, dbo.Tareas_cabecera.pos_4_cant, dbo.Tareas_cabecera.pos_5_cant, 
                      dbo.Tareas_cabecera.pos_6_cant, dbo.Tareas_cabecera.pos_7_cant, dbo.Tareas_cabecera.pos_8_cant, dbo.rango_talles.posic_1, 
                      dbo.colores_por_articulo.cod_material, dbo.colores_por_articulo.cod_color, dbo.Tareas_cabecera.ultima_seccion_cumplida, 
                      dbo.Tareas_cabecera.operador_entregado, dbo.Tareas_cabecera.observacion, dbo.Tareas_cabecera.ultimo_paso_cumplido, 
                      dbo.Tareas_cabecera.seleccion, dbo.colores_por_articulo.cod_mp_critico_1, dbo.colores_por_articulo.cod_mp_critico_2, 
                      dbo.colores_por_articulo.cod_mp_critico_3, dbo.colores_por_articulo.cod_color_mp_critico_1, dbo.colores_por_articulo.cod_color_mp_critico_2, 
                      dbo.colores_por_articulo.cod_color_mp_critico_3, dbo.Orden_fabricacion.version, dbo.articulos.cod_ruta
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango INNER JOIN
                      dbo.colores_por_articulo INNER JOIN
                      dbo.Orden_fabricacion INNER JOIN
                      dbo.Tareas_cabecera ON dbo.Orden_fabricacion.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion INNER JOIN
                      dbo.Tareas_detalle ON dbo.Tareas_cabecera.nro_tarea = dbo.Tareas_detalle.nro_tarea AND 
                      dbo.Tareas_cabecera.nro_orden_fabricacion = dbo.Tareas_detalle.nro_orden_fabricacion ON 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.Orden_fabricacion.cod_color_articulo AND 
                      dbo.colores_por_articulo.cod_articulo = dbo.Orden_fabricacion.cod_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
WHERE     (dbo.Tareas_detalle.cod_seccion = 60) OR
                      (dbo.Tareas_detalle.cod_seccion = 62)

GO
