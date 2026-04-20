CREATE VIEW [dbo].[mp_remitos_vw] AS
/*SELECT     dbo.Remitos_proveedor_cabecera.fecha_recepcion AS fecha_movimiento, dbo.remitos_proveedor_detalle.cod_material, dbo.remitos_proveedor_detalle.cod_color, 
                      ISNULL(dbo.remitos_proveedor_detalle.cantidad, 0) * dbo.materiales.factor_conversion AS cant, ISNULL(dbo.remitos_proveedor_detalle.cant_1, 0) 
                      * dbo.materiales.factor_conversion AS c1, ISNULL(dbo.remitos_proveedor_detalle.cant_2, 0) * dbo.materiales.factor_conversion AS c2, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_3, 0) * dbo.materiales.factor_conversion AS c3, ISNULL(dbo.remitos_proveedor_detalle.cant_4, 0) 
                      * dbo.materiales.factor_conversion AS c4, ISNULL(dbo.remitos_proveedor_detalle.cant_5, 0) * dbo.materiales.factor_conversion AS c5, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_6, 0) * dbo.materiales.factor_conversion AS c6, ISNULL(dbo.remitos_proveedor_detalle.cant_7, 0) 
                      * dbo.materiales.factor_conversion AS c7, ISNULL(dbo.remitos_proveedor_detalle.cant_8, 0) * dbo.materiales.factor_conversion AS c8, '01' AS cod_almacen, 
                      'Rto:' + CAST(dbo.Remitos_proveedor_cabecera.cod_proveedor AS varchar) + ' - ' + dbo.Remitos_proveedor_cabecera.nro_compuesto_remito AS motivo
FROM         dbo.Remitos_proveedor_cabecera INNER JOIN
                      dbo.remitos_proveedor_detalle ON dbo.Remitos_proveedor_cabecera.cod_proveedor = dbo.remitos_proveedor_detalle.cod_proveedor AND 
                      dbo.Remitos_proveedor_cabecera.nro_compuesto_remito = dbo.remitos_proveedor_detalle.nro_compuesto_remito INNER JOIN
                      dbo.materiales ON dbo.remitos_proveedor_detalle.cod_material = dbo.materiales.cod_material
*/
CREATE VIEW dbo.mp_remitos_vw
AS
SELECT     dbo.Remitos_proveedor_cabecera.fecha_recepcion AS fecha_movimiento, dbo.remitos_proveedor_detalle.cod_material, 
                      dbo.remitos_proveedor_detalle.cod_color, ISNULL(dbo.remitos_proveedor_detalle.cantidad, 0) AS cantidad, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_1, 0) AS c1, ISNULL(dbo.remitos_proveedor_detalle.cant_2, 0) AS c2, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_3, 0) AS c3, ISNULL(dbo.remitos_proveedor_detalle.cant_4, 0) AS c4, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_5, 0) AS c5, ISNULL(dbo.remitos_proveedor_detalle.cant_6, 0) AS c6, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_7, 0) AS c7, ISNULL(dbo.remitos_proveedor_detalle.cant_8, 0) AS c8, 
                      ISNULL(dbo.remitos_proveedor_detalle.cant_9, 0) AS c9, ISNULL(dbo.remitos_proveedor_detalle.cant_10, 0) AS c10, 
                      dbo.Remitos_proveedor_cabecera.cod_almacen_recepcion AS cod_almacen, 'Remito' AS motivo, 
                      dbo.Remitos_proveedor_cabecera.nro_remito AS nro_operacion, 'S' AS efecto_movimiento
FROM         dbo.Remitos_proveedor_cabecera INNER JOIN
                      dbo.remitos_proveedor_detalle ON dbo.Remitos_proveedor_cabecera.cod_proveedor = dbo.remitos_proveedor_detalle.cod_proveedor AND 
                      dbo.Remitos_proveedor_cabecera.nro_compuesto_remito = dbo.remitos_proveedor_detalle.nro_compuesto_remito INNER JOIN
                      dbo.materiales ON dbo.remitos_proveedor_detalle.cod_material = dbo.materiales.cod_material

GO
