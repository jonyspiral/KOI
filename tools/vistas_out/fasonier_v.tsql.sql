CREATE VIEW dbo.fasonier_v AS
SELECT P.*, o.cod_operador ,o.tipo_operador FROM operadores o
INNER JOIN proveedores_datos p ON o.cod_proveedor = p.cod_prov
