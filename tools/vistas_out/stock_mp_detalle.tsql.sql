CREATE VIEW dbo.stock_mp_detalle
AS
SELECT     TOP 100 PERCENT fecha_movimiento, cod_material, cod_color, cantidad, c1, c2, c3, c4, c5, c6, c7, c8, c9, c10, cod_almacen, Motivo, nro_operacion, 
                      efecto_movimiento
FROM         (SEL