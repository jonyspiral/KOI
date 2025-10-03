CREATE VIEW [dbo].[materiales_preferentes_v] AS
CREATE VIEW dbo.materiales_preferentes_v
AS
SELECT     TOP 100 PERCENT dbo.materiales.cod_material, dbo.Materias_primas.cod_color, dbo.materiales.denom_material AS Material, 
                      dbo.Proveedores_materias_primas.cod_proveedor, dbo.proveedores_datos.razon_social, dbo.rubros_materias_primas.denom_rubro AS Rubro, 
                      dbo.materiales.cod_rango AS Rango, dbo.materiales.unidad_medida AS UMS, dbo.materiales.unidad_medida_compra AS UMC, 
                      dbo.materiales.factor_conversion AS FC, dbo.materiales.lote_minimo, dbo.materiales.lote_multiplo, dbo.materiales.fecha_ultima_modificacion, 
                      dbo.materiales.produccion_interna, dbo.Materias_primas.precio_unitario AS PU, dbo.Proveedores_materias_primas.precio_compra AS PC, 
                      dbo.materiales.cod_subrubro, ISNULL(dbo.Proveedores_materias_primas.precio_compra, dbo.Materias_primas.precio_unitario) AS Precio
FROM         dbo.Materias_primas LEFT OUTER JOIN
                      dbo.Proveedores_materias_primas ON dbo.Materias_primas.cod_material = dbo.Proveedores_materias_primas.cod_material AND 
                      dbo.Materias_primas.cod_color = dbo.Proveedores_materias_primas.cod_color RIGHT OUTER JOIN
                      dbo.materiales INNER JOIN
                      dbo.rubros_materias_primas ON dbo.materiales.cod_rubro = dbo.rubros_materias_primas.cod_rubro ON 
                      dbo.Materias_primas.cod_material = dbo.materiales.cod_material LEFT OUTER JOIN
                      dbo.proveedores_datos ON dbo.Proveedores_materias_primas.cod_proveedor = dbo.proveedores_datos.cod_prov
WHERE     (dbo.Proveedores_materias_primas.preferente_costo = 'S')
ORDER BY dbo.materiales.denom_material

GO
