CREATE VIEW [dbo].[stock_produccion_incumplida_v] AS
CREATE VIEW dbo.stock_produccion_incumplida_v
AS
SELECT     TOP 100 PERCENT cod_articulo, denom_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(ISNULL(pos_1_cant, 0)) AS cant_1, 
                      SUM(ISNULL(pos_2_cant, 0)) AS cant_2, SUM(ISNULL(pos_3_cant, 0)) AS cant_3, SUM(ISNULL(pos_4_cant, 0)) AS cant_4, SUM(ISNULL(pos_5_cant, 0)) 
                      AS cant_5, SUM(ISNULL(pos_6_cant, 0)) AS cant_6, SUM(ISNULL(pos_7_cant, 0)) AS cant_7, SUM(ISNULL(pos_8_cant, 0)) AS cant_8, posic_1
FROM         dbo.programacion_empaque_v
WHERE     (situacion = 'p' OR
                      situacion = 'i') AND (anulado = 'n') AND (Confirmada = 's') AND (cumplido_paso = 'n')
GROUP BY cod_articulo, denom_articulo, cod_color_articulo, posic_1

GO
