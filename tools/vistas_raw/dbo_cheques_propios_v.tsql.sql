CREATE VIEW [dbo].[cheques_propios_v] AS
CREATE VIEW cheques_propios_v AS
	SELECT		c.cod_cheque, c.empresa, c.numero, banco.nombre AS banco_nombre, c.importe, c.fecha_vencimiento, p.cod_prov, p.razon_social, 
				c.librador_nombre, c.dias_vencimiento AS dias, c.esperando_en_banco, c.fecha_credito_debito, c.concluido
	FROM		dbo.cheque_v AS c
				LEFT OUTER JOIN dbo.proveedores_datos AS p ON c.cod_proveedor = p.cod_prov
				INNER JOIN dbo.banco AS banco ON banco.cod_banco = c.cod_banco
	WHERE		c.cod_cuenta_bancaria IS NOT NULL AND (c.concluido = 'N' OR
				(c.concluido = 'S' AND c.esperando_en_banco = 'D' AND c.fecha_credito_debito IS NULL)) AND
				c.cod_rechazo_cheque IS NULL AND c.anulado = 'N'
GO
