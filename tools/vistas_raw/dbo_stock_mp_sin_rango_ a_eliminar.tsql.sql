CREATE VIEW [dbo].[stock_mp_sin_rango_ a_eliminar] AS
CREATE VIEW [dbo].[stock_mp_sin_rango] AS 
SELECT TOP 100 PERCENT cod_material, cod_color, SUM(cant) AS cant FROM 
(SELECT dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material AS cod_color, SUM(- dbo.materias_primas_movim_extraor.cantidad) AS cant
 FROM dbo.materias_primas_movim_extraor INNER JOIN dbo.materiales ON dbo.materias_primas_movim_extraor.cod_material = dbo.materiales.cod_material
 WHERE (dbo.materias_primas_movim_extraor.efecto_movimiento = 'S') AND (dbo.materiales.anulado = 'N')   
GROUP BY dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material 

UNION
 SELECT dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material AS cod_color, SUM(dbo.materias_primas_movim_extraor.cantidad) AS cant
 FROM dbo.materias_primas_movim_extraor INNER JOIN dbo.materiales ON dbo.materias_primas_movim_extraor.cod_material = dbo.materiales.cod_material
 WHERE (dbo.materias_primas_movim_extraor.efecto_movimiento = 'e') AND (dbo.materiales.anulado = 'N') 
GROUP BY dbo.materias_primas_movim_extraor.cod_material, dbo.materias_primas_movim_extraor.cod_color_material 

UNION
 SELECT remitos_proveedor_detalle.cod_material, remitos_proveedor_detalle.cod_color, SUM([cantidad] * [factor_conversion]) AS Cant 
FROM (Remitos_proveedor_cabecera INNER JOIN remitos_proveedor_detalle ON (Remitos_proveedor_cabecera.cod_proveedor = remitos_proveedor_detalle.cod_proveedor) AND (Remitos_proveedor_cabecera.nro_compuesto_remito = remitos_proveedor_detalle.nro_compuesto_remito)) INNER JOIN materiales ON remitos_proveedor_detalle.cod_material = materiales.cod_material
 WHERE (dbo.materiales.anulado = 'N') 
GROUP BY remitos_proveedor_detalle.cod_material, remitos_proveedor_detalle.cod_color 

UNION 
SELECT 
Consumos_tarea.cod_material, Consumos_tarea.cod_color,sum(- cant_consumo) AS cant 
FROM Consumos_tarea  
  INNER JOIN materiales ON Consumos_tarea.cod_material = materiales.cod_material 
WHERE (dbo.materiales.anulado = 'N')
group by dbo.Consumos_tarea.cod_material, Consumos_tarea.cod_color 

 UNION 
SELECT Stock_mp_fc.COD_MATERIAL, Stock_mp_fc.cod_color, SUM(- cantidad) AS cant
 FROM Stock_mp_fc INNER JOIN materiales ON Stock_mp_fc.cod_material = materiales.cod_material WHERE (dbo.materiales.anulado = 'N') 
GROUP BY Stock_mp_fc.COD_MATERIAL, Stock_mp_fc.cod_color) a GROUP BY cod_material, cod_color ORDER BY cod_material, cod_color
GO
