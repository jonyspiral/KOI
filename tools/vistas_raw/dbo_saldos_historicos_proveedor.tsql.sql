CREATE VIEW [dbo].[saldos_historicos_proveedor] AS

CREATE VIEW saldos_historicos_proveedor
AS
(
	SELECT empresa, cod_prov, (sum(importe_debe - importe_haber)) saldo 
	FROM cuenta_corriente_historica_proveedor
	GROUP BY empresa, cod_prov
)
GO
