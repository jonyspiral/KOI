CREATE VIEW dbo.filas_asientos_contables_v
AS
SELECT     TOP 100 PERCENT c.cod_asiento, c.empresa, c.nombre AS asunto, dbo.round(c.importe) AS importe, c.cod_ejercicio, c.fecha_asiento, d.numero_fila, 
                      d.cod_imputacion, pc.denomin