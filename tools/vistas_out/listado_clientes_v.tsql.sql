CREATE VIEW listado_clientes_v AS
	SELECT	c.cod_cli cod_cliente, c.razon_social, c.denom_fantasia, c.cuit, c.cod_vendedor,
			sc.telefono_1, sc.telefono_2, c.email email_cliente, sc.email email_sucursal,
			pa.cod_pais, pr.cod_provincia, pr.denom_provi