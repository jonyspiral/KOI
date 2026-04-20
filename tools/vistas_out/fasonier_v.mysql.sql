CREATE OR REPLACE VIEW fasonier_v AS SELECT P.*, o.cod_operador ,o.tipo_operador FROM operadores oINNER JOIN proveedores_datos p ON o.cod_proveedor = p.cod_prov;;
