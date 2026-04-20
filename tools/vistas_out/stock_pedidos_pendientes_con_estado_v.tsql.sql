CREATE VIEW dbo.stock_pedidos_pendientes_con_estado_v
AS
SELECT     TOP 100 PERCENT dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, SUM(dbo.pedidos_d.pendiente) AS cant_pend, 
                      SUM(dbo.pedidos_d.pend_1) AS pend_1, SUM