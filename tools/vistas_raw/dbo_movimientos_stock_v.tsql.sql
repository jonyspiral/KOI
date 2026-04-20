CREATE VIEW [dbo].[movimientos_stock_v] AS

CREATE VIEW movimientos_stock_v AS
SELECT m.*, a.denom_articulo nombre_articulo FROM movimientos_stock m
LEFT JOIN articulos a ON m.cod_articulo = a.cod_articulo

GO
