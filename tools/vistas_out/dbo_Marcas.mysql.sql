CREATE VIEW `Marcas` AS


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
    spiral.dbo.Marcas;
