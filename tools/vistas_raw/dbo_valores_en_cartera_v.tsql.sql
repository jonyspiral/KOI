CREATE VIEW [dbo].[valores_en_cartera_v] AS

CREATE VIEW valores_en_cartera_v AS
SELECT TOP 100 PERCENT c.cod_cheque, c.empresa, c.numero, banco.nombre banco_nombre, c.importe, c.fecha_vencimiento, c.cod_cliente, cl.razon_social, c.librador_nombre, c.librador_cuit, c.dias_vencimiento AS dias
FROM dbo.cheque_v AS c
LEFT JOIN Clientes cl ON c.cod_cliente = cl.cod_cli
LEFT JOIN banco ON banco.cod_banco = c.cod_banco
WHERE (c.cod_cuenta_bancaria IS NULL) AND (c.concluido = 'N') AND (c.cod_rechazo_cheque IS NULL) AND (c.anulado = 'N') AND (c.esperando_en_banco IS NULL)
ORDER BY c.fecha_vencimiento


GO
