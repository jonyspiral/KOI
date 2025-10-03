CREATE VIEW [dbo].[ingresos_v] AS
CREATE VIEW dbo.ingresos_v
AS
SELECT     fecha_alta, empresa, tipo_docum, nro_documento, cod_cliente, operacion_tipo, importe_1, importe_2, importe_3, importe_4, importe_5, 
                      ingreso_bancario_importe, importe_6, importe_total, plazo_promedio_pago, motivo, anulado
FROM         dbo.recibos_c
WHERE     (anulado <> 's') AND (fecha_alta > CONVERT(DATETIME, '2012-01-01 00:00:00', 102))

GO
