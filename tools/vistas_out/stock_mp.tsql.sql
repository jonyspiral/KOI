CREATE VIEW dbo.stock_mp
AS
SELECT     TOP 100 PERCENT dbo.Materias_primas.cod_material, dbo.materiales.denom_material, dbo.Materias_primas.cod_color, a.cod_almacen, SUM(a.cant) 
                      AS cant, SUM(a.c1) AS c1, SUM(a.c2) AS c2, SUM(a.c3