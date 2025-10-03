CREATE VIEW dbo.costos_fijos_periodo_vig_v
AS
SELECT     dbo.costos_fijos_c.nro_periodo, dbo.costos_fijos_d.cod_linea, 
                      dbo.costos_fijos_c.costo_estructura * dbo.costos_fijos_d.porcentaje_periodo / 100 / dbo.costos_fijos_d.cantida