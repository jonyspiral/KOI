CREATE VIEW [dbo].[cheque_v] AS
CREATE VIEW [dbo].[cheque_v] AS
	SELECT c.*, b.nombre as banco_nombre,
	DATEDIFF(dd, GETDATE(), c.fecha_vencimiento) as dias_vencimiento
	FROM cheque c
	LEFT JOIN banco b ON b.cod_banco = c.cod_banco


GO
