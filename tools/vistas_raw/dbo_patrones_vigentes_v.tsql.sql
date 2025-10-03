CREATE VIEW [dbo].[patrones_vigentes_v] AS
CREATE VIEW dbo.patrones_vigentes_v
AS
SELECT     TOP 100 PERCENT dbo.Patrones_mp_detalle.cod_seccion, dbo.Patrones_mp_cabecera.cod_articulo, dbo.articulos.denom_articulo, 
                      dbo.Patrones_mp_detalle.cod_color_articulo, dbo.Patrones_mp_detalle.nro_item, dbo.Patrones_mp_detalle.version, 
                      dbo.Patrones_mp_detalle.conjunto, dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material, 
                      dbo.Patrones_mp_detalle.fecha_alta, dbo.Patrones_mp_detalle.consumo_par, dbo.Patrones_mp_cabecera.tipo_patron, 
                      dbo.materiales.factor_conversion, dbo.Patrones_mp_cabecera.borrador, dbo.materiales.unidad_medida AS UM, dbo.materiales.produccion_interna, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.articulos.naturaleza
FROM         dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo INNER JOIN
                      dbo.Patrones_mp_cabecera INNER JOIN
                      dbo.Patrones_mp_detalle ON dbo.Patrones_mp_cabecera.version = dbo.Patrones_mp_detalle.version AND 
                      dbo.Patrones_mp_cabecera.cod_color_articulo = dbo.Patrones_mp_detalle.cod_color_articulo AND 
                      dbo.Patrones_mp_cabecera.cod_articulo = dbo.Patrones_mp_detalle.cod_articulo INNER JOIN
                      dbo.materiales ON dbo.Patrones_mp_detalle.cod_material = dbo.materiales.cod_material ON 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.Patrones_mp_cabecera.cod_color_articulo AND 
                      dbo.colores_por_articulo.cod_articulo = dbo.Patrones_mp_cabecera.cod_articulo
WHERE     (dbo.articulos.vigente = 'S') AND (dbo.colores_por_articulo.aprob_produccion = 'S') AND (dbo.colores_por_articulo.vigente = 'S') AND 
                      (dbo.Patrones_mp_cabecera.version_actual = N'S') AND (dbo.colores_por_articulo.id_tipo_producto_stock = '01' OR
                      dbo.colores_por_articulo.id_tipo_producto_stock = '02' OR
                      dbo.colores_por_articulo.id_tipo_producto_stock = '04' OR
                      dbo.colores_por_articulo.id_tipo_producto_stock = '08' OR
                      dbo.colores_por_articulo.id_tipo_producto_stock = '05') AND (dbo.articulos.naturaleza = N'pt')
ORDER BY dbo.Patrones_mp_cabecera.cod_articulo, dbo.Patrones_mp_detalle.nro_item

GO
