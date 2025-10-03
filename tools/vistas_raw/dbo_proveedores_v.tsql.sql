CREATE VIEW [dbo].[proveedores_v] AS

CREATE VIEW [dbo].[proveedores_v] AS
	SELECT p.*, ia.importe_acumulado_mes, ia.importe_retenido_mes, gp1.saldo saldo_1, gp2.saldo saldo_2, (gp1.saldo + gp2.saldo) saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN dbo.importes_op_acumulado_mes AS ia ON p.cod_prov = ia.cod_proveedor
	LEFT JOIN gestion_proveedores_1 AS gp1 ON p.cod_prov = gp1.cod_prov
	LEFT JOIN gestion_proveedores_2 AS gp2 ON p.cod_prov = gp2.cod_prov
GO
