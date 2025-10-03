CREATE VIEW [dbo].[stock_mp_sin_rango] AS 
SELECT TOP 100 PERCENT cod_material, cod_color, SUM(cant) AS cant FROM 
(SELECT dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material AS cod_color, SUM(- dbo.mater