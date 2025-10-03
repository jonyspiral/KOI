CREATE OR REPLACE VIEW VIEW1 AS
1> 2> 1> 2> 3> 4> 5> 6> 7> 8> 9> text
CREATE VIEW VIEW1
AS
SELECT     situacion, cod_seccion, cod_articulo, denom_articulo, cod_color_articulo, SUM(cantidad) AS cant
FROM         tareas_incumplidas_v
GROUP BY situacion, cod_seccion, cod_articulo, denom_articulo, cod_color_articulo
HAVING      (cod_seccion = 60) OR
                      (cod_seccion = 62);
