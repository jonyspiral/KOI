CREATE VIEW [dbo].[cuenta_corriente_historica_proveedor] AS
/*		((CASE tipo_doc 
			WHEN 'NCR' THEN (-1)
			ELSE (1)
		END) * total_doc) total_documento
empresa, cod_prov, tipo_doc, nro_doc, fecha, total_doc importe_debe (FAC NDB), total_doc importe_haber (NCR OP)*/
CREATE VIEW dbo.cuenta_corriente_historica_proveedor
AS
SELECT     empresa, cod_prov, tipo_doc AS tipo_docum, nro_doc AS nro_docum, fecha AS documento_fecha, (CASE tipo_doc WHEN 'NCR' THEN 0 ELSE total_doc END) 
                      AS importe_debe, (CASE tipo_doc WHEN 'NCR' THEN total_doc ELSE 0 END) AS importe_haber, letra
FROM         dbo.compras_cabecera
WHERE     (grado = 'M')
UNION
SELECT     empresa, cod_prov, tipo_docum, orden_pago_nro AS nro_docum, fecha_orden_pago AS documento_fecha, 0 AS importe_debe, 
                      importe_a_pagar AS importe_haber, NULL AS letra
FROM         dbo.ordenes_de_pago
WHERE     (anulado = 'N') OR
                      (anulado IS NULL)

GO
