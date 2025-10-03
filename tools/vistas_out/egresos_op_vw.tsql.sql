CREATE VIEW dbo.egresos_op_vw
AS
SELECT     empresa, sucursal_empresa, fecha_orden_pago AS fecha, operacion_tipo AS tipo, tipo_docum AS docum, orden_pago_nro AS op_nro, cod_prov, 
                      importe_a_pagar AS importe, imput_obligacion_paga 