CREATE VIEW dbo.materiales_preferentes_v
AS
SELECT     TOP 100 PERCENT dbo.materiales.cod_material, dbo.Materias_primas.cod_color, dbo.materiales.denom_material AS Material, 
                      dbo.Proveedores_materias_primas.cod_proveedor, dbo.prov