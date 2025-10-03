
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