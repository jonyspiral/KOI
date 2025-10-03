CREATE VIEW [dbo].[lineas_productos] AS

-- Crear la vista llamada 'lineas' en la base de datos 'desarrollo'
CREATE VIEW lineas_productos AS
SELECT 
    cod_linea,
    denom_linea,
    origen,
    lanzamiento_inicial,
    estado_de_linea,
    fecha_de_baja,
    anulado,
    material,
    fecha_ultima_modificacion,
    autor_ultima_modificacion,
    cod_linea_nro,
    fechaAlta,
    titulo_catalogo
FROM 
    spiral.dbo.lineas_productos

GO
