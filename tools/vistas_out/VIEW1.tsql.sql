CREATE VIEW dbo.VIEW1
AS
SELECT     situacion, cod_seccion, cod_articulo, denom_articulo, cod_color_articulo, SUM(cantidad) AS cant
FROM         dbo.tareas_incumplidas_v
GROUP BY situacion, cod_seccion, cod_articulo, denom_articulo, cod_color_articulo