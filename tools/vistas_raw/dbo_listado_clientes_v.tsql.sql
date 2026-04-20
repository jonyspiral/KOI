CREATE VIEW [dbo].[listado_clientes_v] AS
CREATE VIEW listado_clientes_v AS
	SELECT	c.cod_cli cod_cliente, c.razon_social, c.denom_fantasia, c.cuit, c.cod_vendedor,
			sc.telefono_1, sc.telefono_2, c.email email_cliente, sc.email email_sucursal,
			pa.cod_pais, pr.cod_provincia, pr.denom_provincia, l.cod_localidad, l.denom_localidad,
			sc.calle, sc.numero, sc.piso, sc.oficina_depto
	FROM	clientes c
			LEFT OUTER JOIN sucursales_clientes sc ON c.cod_casa_central = sc.cod_suc AND c.cod_cli = sc.cod_cli
			LEFT OUTER JOIN paises pa ON pa.cod_pais = sc.cod_pais
			LEFT OUTER JOIN provincias pr ON pr.cod_provincia = sc.cod_provincia
			LEFT OUTER JOIN localidades l ON l.cod_localidad = sc.cod_localidad
	WHERE	c.anulado = 'N'
GO
