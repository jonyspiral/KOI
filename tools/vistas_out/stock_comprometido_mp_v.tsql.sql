CREATE VIEW dbo.stock_comprometido_mp_v
AS
SELECT     TOP 100 PERCENT dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material, 
                      SUM(ISNULL(dbo.Patrones_mp_detalle.consumo_par, 0) * ISNULL(dbo.tareas_incump