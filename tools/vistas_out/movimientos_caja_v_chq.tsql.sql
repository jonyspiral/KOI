CREATE VIEW movimientos_caja_v_chq AS 

SELECT * FROM (
	SELECT 'E' tipo, 'DC' tipo_documento, dc.cod_acreditar_debitar_cheque numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d dc1
		INNER oday',0) fecha,
		ac.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM acreditar_debitar_cheque_d ac
	INNER JOIN acreditar_debitar_cheque_c acc ON acc.cod_acreditar_debitar_cheque = ac.cod_acreditar_debitar_cheq