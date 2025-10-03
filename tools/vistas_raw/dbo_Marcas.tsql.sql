CREATE VIEW [dbo].[Marcas] AS

-- Crear la vista llamada 'marcas' en la base de datos 'encinitas', que apunta a la tabla de 'spiral'
CREATE VIEW Marcas AS
SELECT 
    cod_marca,
    cod_cliente,
    denom_marca,
    anulado,
    fecha_ultima_modificacion,
    autor_ultima_modificacion,
    cod_prov,
    logo,
    fechaAlta,
    fechaBaja
FROM 
    spiral.dbo.Marcas

GO
