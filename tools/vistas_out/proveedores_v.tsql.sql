
CREATE VIEW [dbo].[proveedores_v] AS
	SELECT p.*, ia.importe_acumulado_mes, ia.importe_retenido_mes, gp1.saldo saldo_1, gp2.saldo saldo_2, (gp1.saldo + gp2.saldo) saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN dbo.importes_op_acumulado_mes AS ia 