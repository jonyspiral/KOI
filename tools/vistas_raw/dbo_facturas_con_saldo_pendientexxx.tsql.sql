CREATE VIEW [dbo].[facturas_con_saldo_pendientexxx] AS
CREATE VIEW dbo.facturas_con_saldo_pendiente
AS
SELECT     empresa, tipo_docum, factura_nro, letra_factura, cod_cli, saldo_pendiente, documento_fecha, pagado_total, desactivado_aplicado, grado
FROM         dbo.docum_clientes_cabecera
WHERE     (saldo_pendiente > 0) AND (pagado_total = N'n') AND (desactivado_aplicado = N'n') AND (grado = N'm')

GO
