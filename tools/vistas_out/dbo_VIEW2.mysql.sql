CREATE VIEW `VIEW2` AS
CREATE VIEW VIEW2
AS
SELECT PERCENT empresa, 1 AS punto_venta, 'REC' AS tipo_docum, nro_recibo AS numero, 'R' AS letra, nro_recibo AS nro_comprobante, 
                      anulado, NULL AS tipo_docum_2, dbo.IfNullZero(cod_cliente) AS cod_cliente, NULL AS Expr1, cod_usuario, NULL AS cancel_nro_documento, NULL 
                      AS causa, CAST(observaciones AS CHAR(8000)) AS observaciones, fecha_documento AS fecha, fecha_alta, fecha_baja, fecha_ultima_mod, 
                      importe_total, importe_pendiente, NULL AS importe_neto, NULL AS importe_no_gravado, NULL AS iva_importe_1, NULL AS iva_porc_1, NULL 
                      AS iva_importe_2, NULL AS iva_porc_2, NULL AS iva_importe_3, NULL AS iva_porc_3, NULL AS cotizacion_dolar, NULL 
                      AS descuento_comercial_importe, NULL AS descuento_comercial_porc, NULL AS descuento_despacho_importe, NULL AS cod_forma_pago, NULL 
                      AS cae, NULL AS cae_vencimiento, NULL AS cae_obtencion_fecha, NULL AS cae_obtencion_observaciones, NULL AS cae_obtencion_usuario, NULL 
                      AS mail_enviado, NULL AS tiene_detalle, NULL AS dias_promedio_pago, cod_asiento_contable, cod_ecommerce_order, nro_recibo, 
                      cod_importe_operacion, operacion_tipo, imputacion, recibido_de, fecha_documento, fecha_ponderada_pago
FROM         dbo.recibo
WHERE     (anulado = 'N') AND (nro_recibo > 0)
ORDER BY fecha_alta DESC


LIMIT 100;;
