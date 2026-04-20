CREATE VIEW [dbo].[chequera_v] AS
CREATE VIEW [dbo].[chequera_v] AS
	SELECT c.*, d.cod_chequera_d, d.numero, d.utilizado, cb.nombre_cuenta
	FROM chequera_c c
	INNER JOIN chequera_d d ON c.cod_chequera = d.cod_chequera
	INNER JOIN cuenta_bancaria cb ON c.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
	WHERE d.utilizado = 'N'
GO
