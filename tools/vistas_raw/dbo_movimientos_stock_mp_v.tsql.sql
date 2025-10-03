CREATE VIEW [dbo].[movimientos_stock_mp_v] AS

------------------------------------------------------------------------ MOVIMIENTOS VIEW ------------------------------------------------------------------------------------------


CREATE VIEW dbo.movimientos_stock_mp_v AS
SELECT m.*, ma.denom_material nombre_material FROM movimientos_stock_mp m
LEFT JOIN materiales ma ON m.cod_material = ma.cod_material


GO
