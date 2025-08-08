## Sincronización de Variantes Mercado Libre - KOI2

### ✅ Objetivo General

Implementar una vista en MySQL que consolide todas las variantes (SKU) de productos con datos provenientes de la base SQL Server de Encinitas, con el fin de sincronizar precios, stock, nombre, color y talle con Mercado Libre.

---

### ⚡ Avances Logrados

#### 1. **Conexión con SQL Server (Encinitas)**

* Se utilizó el modelo de Laravel `sqlsrv_encinitas` para acceder a la base de datos `encinitas` desde MySQL.
* Se verificó la comunicación y se usó exitosamente en una consulta para importar stock.

#### 2. **Tabla `stock` en MySQL**

* Se creó una tabla `stock` con los campos:

  * `cod_almacen`, `cod_articulo`, `cod_color_articulo`, `cant_1` a `cant_10`
* Se importó automáticamente desde SQL Server (almacenes 01, 14 y 20).
* Se estableció que la sincronización será siempre **desde SQL Server a MySQL**.



#### 4. **Vista `view_sku_variantes` en MySQL**

* Se construyó una nueva vista consolidada que une:

  * `colores_por_articulo` (datos del producto)
  rango_talles
* Campos clave generados:

  * `sku`: alias de `colores_por_articulo.ml_reference`
  * `var_sku`: `cod_articulo + cod_color_articulo + talle`
  * `color`, `ml_name`, `precio`, `stock`, `talle`
  * `cod_articulo`, `cod_color_articulo` (se agregaron para mayor control)

#### 5. **Modelo Eloquent `SkuVariante`**

* Se creó el modelo Laravel `SkuVariante` apuntando a la vista `view_sku_variantes`.
* Se configuró `primaryKey = null` y `$incrementing = false`.

#### 6. **Controlador `SkuVarianteController`**

* Se implementó el método `index()` con filtros por todos los campos disponibles.
* Se agregó el método `show($id)` para ver detalle de una variante.

#### 7. **Vistas Blade**

* Se creó `index.blade.php` con formulario de filtros y listado paginado.
* Se creó `show.blade.php` para detalle.
* Se estableció como ruta base `/sku/sku_variantes`.

#### 8. **Verificación de Rutas**

* Rutas correctas definidas en `web.php`:

  ```php
  Route::prefix('sku/sku_variantes')->name('sku.sku_variantes.')->group(function () {
      Route::get('/', [SkuVarianteController::class, 'index'])->name('index');
      Route::get('/{id}', [SkuVarianteController::class, 'show'])->name('show');
  });
  ```
* Probadas y funcionando en entorno: `https://devkoi2.spiralshoes.com/sku/sku_variantes`

---

### 🔄 Siguientes Pasos

* Implementar botones de acción para publicar variante en Mercado Libre.
* Permitir edición de campos `ml_name`, `ml_description`, `mlibre_precio` desde KOI.
* Registrar tabla `sku_variantes` en Laravel para futuras sincronizaciones.
* Agregar batch update para `precio` y `stock` según esta vista.

---

### 📄 Resultado

Sistema funcionando y sincronizado entre KOI2 y Encinitas para la gestión de variantes y stock con Mercado Libre, completamente listo para integraciones futuras.


### 🔁 Sincronización de Stock Optimizada (SQL Server → MySQL)

#### ✅ Objetivo
Importar el stock por talle desde la tabla `stock` de SQL Server (Encinitas) hacia la tabla `stock` en MySQL (KOI2) utilizando chunks y `upsert()` para mejor rendimiento.

#### ⚙️ Implementación

- **Modelo origen**: `App\Models\Sql\Stock`  
  - Conectado a `sqlsrv_encinitas`
  - Incluye los campos: `cod_almacen`, `cod_articulo`, `cod_color_articulo`, `cant_1` a `cant_10`
- **Modelo destino**: `App\Models\Stock`  
  - Conectado a MySQL  
  - Mismos campos, más `sync_status`

#### 🛠 Comando Laravel `sync:stock-sql`

- Selecciona los registros desde SQL Server con:
  ```sql
  SELECT [cod_almacen], [cod_articulo], [cod_color_articulo], [cant_1]...[cant_10]
  FROM stock
  WHERE cod_almacen IN ('01', '14', '20')
  ```
- Utiliza `chunk(500)` para procesar en bloques y evitar cuellos de botella.
- Cada bloque es procesado con:
  ```php
  StockMysql::upsert($dataChunk, ['cod_almacen', 'cod_articulo', 'cod_color_articulo']);
  ```
- Se evitaron errores de FreeTDS (`SQL 306` y `8180`) mediante:
  - `select()` explícito
  - Evitar `orderBy` sin campo definido
  - No utilizar `offset` en SQL Server 2000 (incompatible)

#### ✅ Resultado
- Comando final exitoso.
- Se procesaron más de 4.800 registros en chunks, con persistencia eficiente vía `upsert()`.
- Velocidad y rendimiento notablemente mejorados respecto al `updateOrCreate()` clásico.


9/06/2025
