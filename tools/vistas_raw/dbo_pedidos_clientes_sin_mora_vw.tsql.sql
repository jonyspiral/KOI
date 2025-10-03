CREATE VIEW [dbo].[pedidos_clientes_sin_mora_vw] AS
CREATE VIEW dbo.pedidos_clientes_sin_mora_vw
AS
SELECT     TOP 100 PERCENT dbo.pedidos_detalle.cod_articulo, dbo.pedidos_detalle.cod_color_articulo, SUM(dbo.pedidos_detalle.cantidad_pendiente) 
                      AS Pendiente, SUM(dbo.pedidos_detalle.cantidad) AS Pedido, SUM(dbo.pedidos_detalle.pend_1) AS P1, SUM(dbo.pedidos_detalle.pend_2) AS P2, 
                      SUM(dbo.pedidos_detalle.pend_3) AS P3, SUM(dbo.pedidos_detalle.pend_4) AS P4, SUM(dbo.pedidos_detalle.pend_5) AS P5, 
                      SUM(dbo.pedidos_detalle.pend_6) AS P6, SUM(dbo.pedidos_detalle.pend_7) AS P7, SUM(dbo.pedidos_detalle.pend_8) AS P8, 
                      SUM(dbo.pedidos_detalle.pend_9) AS P9, SUM(dbo.pedidos_detalle.pend_10) AS P10
FROM         dbo.pedidos_detalle INNER JOIN
                      dbo.pedidos_cabecera ON dbo.pedidos_detalle.cod_empresa = dbo.pedidos_cabecera.cod_empresa AND 
                      dbo.pedidos_detalle.cod_sucursal = dbo.pedidos_cabecera.cod_sucursal AND 
                      dbo.pedidos_detalle.nro_pedido = dbo.pedidos_cabecera.nro_pedido INNER JOIN
                      dbo.Clientes ON dbo.pedidos_cabecera.cod_cliente = dbo.Clientes.cod_cliente
WHERE     (dbo.Clientes.cod_calificacion = 01 OR
                      dbo.Clientes.cod_calificacion = 02 OR
                      dbo.Clientes.cod_calificacion = 03 OR
                      dbo.Clientes.cod_calificacion = 04) AND (dbo.pedidos_cabecera.anulado = 'N') AND (dbo.pedidos_detalle.anulado = 'N')
GROUP BY dbo.pedidos_detalle.cod_articulo, dbo.pedidos_detalle.cod_color_articulo
HAVING      (SUM(dbo.pedidos_detalle.cantidad_pendiente) <> 0)
ORDER BY dbo.pedidos_detalle.cod_articulo, dbo.pedidos_detalle.cod_color_articulo

GO
