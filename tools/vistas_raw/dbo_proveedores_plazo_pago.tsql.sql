CREATE VIEW [dbo].[proveedores_plazo_pago] AS

CREATE VIEW proveedores_plazo_pago AS
SELECT
	cod_prov AS Cod,
	razon_social AS Razon_Social,
	denom_fantasia AS Denom_Fantasia,
	plazo_pago AS Plazo_Pago
FROM proveedores_datos
WHERE anulado = 'N'
GO
