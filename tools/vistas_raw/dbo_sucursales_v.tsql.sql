CREATE VIEW [dbo].[sucursales_v] AS
CREATE VIEW sucursales_v AS 
SELECT s.*, l.cod_zona_geo
FROM sucursales_clientes s
LEFT JOIN localidades l ON s.cod_pais = l.cod_pais AND s.cod_provincia = l.cod_provincia AND s.cod_localidad_nro = l.cod_localidad_nro


GO
