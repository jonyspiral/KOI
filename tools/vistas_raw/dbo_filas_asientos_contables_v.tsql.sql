CREATE VIEW [dbo].[filas_asientos_contables_v] AS
CREATE VIEW dbo.filas_asientos_contables_v
AS
SELECT     TOP 100 PERCENT c.cod_asiento, c.empresa, c.nombre AS asunto, dbo.round(c.importe) AS importe, c.cod_ejercicio, c.fecha_asiento, d.numero_fila, 
                      d.cod_imputacion, pc.denominacion AS denominacion_imputacion, dbo.round(d.importe_debe) AS importe_debe, dbo.round(d.importe_haber) 
                      AS importe_haber, d.fecha_vencimiento, d.observaciones, d.anulado
FROM         dbo.asientos_contables c INNER JOIN
                      dbo.filas_asientos_contables d ON c.cod_asiento = d.cod_asiento INNER JOIN
                      dbo.plan_cuentas pc ON pc.cuenta = d.cod_imputacion
ORDER BY c.fecha_asiento DESC

GO
