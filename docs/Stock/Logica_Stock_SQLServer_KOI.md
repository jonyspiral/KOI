
# 📦 Lógica de Stock en KOI (SQL Server)

Este documento describe la lógica utilizada en KOI para el cálculo de stock por artículo, color y talle,
optimizando el rendimiento al interactuar con SQL Server.

---

## 🔹 Objetivo
Obtener cantidades de stock agrupadas por artículo, color y posición (talle), aplicando filtros por almacén
y otros criterios, con la mínima cantidad de consultas posibles.

---

## 🔹 Problema Original
Antes, el sistema llamaba a `Stock::obtenerCantidadPorPosicion()` para **cada** fila y talle de la grilla,
lo que generaba **miles de consultas a SQL Server** para una sola página, resultando en tiempos de
respuesta de 30+ segundos para ~300 registros.

---

## 🔹 Solución Aplicada

### 1. Consulta Única Agrupada
Se reemplazó el esquema de múltiples queries por **una única consulta grande**:
```php
$stockFiltrado = Stock::whereIn('cod_almacen', $almacenes)
    ->get()
    ->groupBy(fn($s) => "{$s->cod_articulo}-{$s->cod_color_articulo}");
```
Esto trae todas las cantidades (`cant_1` a `cant_10`) de los artículos filtrados y las agrupa en memoria.

---

### 2. Cache Local en Memoria
Se implementó un arreglo `$stockCache` para evitar recalcular cantidades ya obtenidas.
La clave de cache se construye así:
```
{cod_articulo}-{cod_color_articulo}-{posicion}-{almacenes}
```

Ejemplo:
```
0121-N-3-01-02-03
```

---

### 3. Cálculo desde Memoria
Cuando se necesita una cantidad:
1. Se arma la clave.
2. Si no existe en `$stockCache`, se calcula sumando la columna correspondiente (`cant_X`) de `$stockFiltrado`.
3. Se guarda en cache y se devuelve.

Ejemplo:
```php
$keyStock = "{$item->cod_articulo}-{$item->cod_color_articulo}";
$stockGroup = $stockFiltrado[$keyStock] ?? collect();
$cantidad = $stockGroup->sum("cant_$i");
```

---

### 4. Uso en Totales y Paginado
La misma cache se reutiliza en:
- **Cálculo de totales** (query clonada sin paginación)
- **Mapeo de registros paginados**

De esta forma se evita recalcular valores y se mantiene consistencia.

---

## 🔹 Beneficios
- **Rendimiento:** De 34 segundos → ~1.7 segundos para 300 registros.
- **Menos carga en SQL Server:** Una sola query por filtro aplicado.
- **Código más limpio:** Lógica centralizada en `obtenerCantidadConCache()` o en `MemCacheHelper`.

---

## 🔹 Reutilización en Otras Apps
Esta técnica se puede aplicar siempre que:
1. Se pueda traer un conjunto grande de datos con una sola consulta.
2. Los cálculos se puedan hacer en memoria.
3. Se pueda mantener una cache local para reuso durante la ejecución.

Ejemplos de aplicación:
- Reportes de inventario.
- Análisis de ventas por talla.
- Comparación de stock entre almacenes.

---

✍️ **Autor:** Equipo KOI - Optimización Stock  
📅 **Última actualización:** 2025-08-09
