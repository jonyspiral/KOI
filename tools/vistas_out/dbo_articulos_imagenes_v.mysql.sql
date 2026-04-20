CREATE VIEW `articulos_imagenes_v` AS
CREATE VIEW articulos_imagenes_v
AS
SELECT PERCENT *
FROM         dbo.articulos_imagenes ai
WHERE     (tipo = N'imagen')
ORDER BY producto, orden


LIMIT 100;;
