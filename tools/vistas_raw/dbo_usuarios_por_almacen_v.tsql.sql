CREATE VIEW [dbo].[usuarios_por_almacen_v] AS



--Es porque la clase UsuarioPorAlmacen hereda de Usuario y necesita todos sus campos para el fill

CREATE VIEW usuarios_por_almacen_v AS
SELECT a.cod_almacen, b.*
FROM usuarios_por_almacen a
INNER JOIN users b ON a.cod_usuario = b.cod_usuario
GO
