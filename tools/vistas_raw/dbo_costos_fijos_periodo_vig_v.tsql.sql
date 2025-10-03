CREATE VIEW [dbo].[costos_fijos_periodo_vig_v] AS
CREATE VIEW dbo.costos_fijos_periodo_vig_v
AS
SELECT     dbo.costos_fijos_c.nro_periodo, dbo.costos_fijos_d.cod_linea, 
                      dbo.costos_fijos_c.costo_estructura * dbo.costos_fijos_d.porcentaje_periodo / 100 / dbo.costos_fijos_d.cantidad_producida AS costo_linea
FROM         dbo.costos_fijos_c INNER JOIN
                      dbo.costos_fijos_d ON dbo.costos_fijos_c.nro_costo_fijo = dbo.costos_fijos_d.nro_costo_fijo_c
WHERE     (dbo.costos_fijos_c.vigente = 'S')

GO
