
# 🧠 Proceso de Poblamiento de sku_variantes desde SQL Server

## 📌 Objetivo

Actualizar la tabla `sku_variantes` en MySQL utilizando datos provenientes de la vista `view_sku_variantes` y calculando stock dinámicamente a través de la función `StockSkuService::obtenerStockSKU`.

---

## ⚙️ Flujo del Comando `sku:poblar-desde-view`

### 1. Fuente de Datos
Se utiliza la vista `view_sku_variantes` como origen, que contiene combinaciones de `cod_articulo`, `cod_color_articulo` y `talle`.

### 2. Cálculo de Stock
Para cada fila, se ejecuta:

```php
$stockEcom = StockSkuService::obtenerStockSKU(..., ['01', '14']);
$stock2da  = StockSkuService::obtenerStockSKU(..., ['02']);
```

> ✅ `StockSkuService::obtenerStockSKU` fue corregido para leer correctamente desde SQL Server, incluyendo el mapeo `cod_rango` → `rango_talles`.

### 3. Inserción/Actualización
Se actualiza o inserta en `sku_variantes` con:

- `stock`, `stock_ecommerce`: valor devuelto de almacenes 01 y 14.
- `stock_2da`: valor desde almacén 02.
- `sync_status`: `'N'`
- Otros datos como `ml_name`, `color`, `talle`, `precio`, etc., también se copian.

---

## 🧪 Verificación Sugerida

Luego de ejecutar:

```bash
php artisan sku:poblar-desde-view
```

Verificar con:

```sql
SELECT * FROM sku_variantes WHERE cod_articulo = '3202' AND cod_color_articulo = 'BN';
```

---

## 📝 Observaciones Técnicas

- La función `obtenerStockSKU` ahora interpreta correctamente `posic_1` a `posic_20` de la tabla `rango_talles`.
- El error anterior (stock 0) se debía a un mal mapeo del campo `cod_rango_talle` que fue corregido por `cod_rango`.

---

## ✅ Estado Actual

- Funcionalidad verificada vía Tinker.
- Stock correcto reportado para SKU 3202-BN-38.
