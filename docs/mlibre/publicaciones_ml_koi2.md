# 📦 Módulo de Publicaciones Mercado Libre – KOI2

## ✅ Estado Actual (Junio 2025)

### 1. Descarga y almacenamiento de publicaciones

- Se utiliza el comando `mlibre:descargar-publicaciones` para obtener todas las publicaciones activas de Mercado Libre y guardar su JSON completo en `/storage/app/mlibre/items/`.

### 2. Importación de publicaciones a base de datos

- El comando `mlibre:importar-json` recorre los archivos descargados y:
  - Inserta o actualiza registros en la tabla `ml_publicaciones`.
  - Extrae y guarda:
    - `ml_id`
    - `ml_name`, `ml_description`
    - `status`
    - `mlibre_precio`: mínimo entre las variantes (o `price` si no hay variantes)
    - `mlibre_stock`: suma total de `available_quantity` (si hay variantes)
    - `raw_json`: JSON completo de la publicación
  - Borra variantes anteriores y crea nuevas en la tabla `ml_variantes`, asociadas por `ml_publicacion_id`.

### 3. Edición individual

- Se creó la vista `edit.blade.php`:
  - Muestra campos editables: `ml_name`, `ml_description`, `ml_reference`, `mlibre_precio`, `mlibre_stock`.
  - Permite ver el JSON original.
  - Permite acceder al permalink real en Mercado Libre (si está disponible).

### 4. Visualización de variantes

- En la edición de cada publicación se visualizan sus variantes (`ml_variantes`) en un subformulario.
- Se muestran: `sku_`, `talle`, `precio`, `stock`.
- `sku_` representa la clave de integración con KOI (compuesto por `cod_articulo + cod_color_articulo + talle`).

---

## 📁 Estructura de tablas

### `ml_publicaciones`

| Campo             | Tipo         | Descripción                              |
|------------------|--------------|------------------------------------------|
| `ml_id`           | string       | ID de la publicación (MLAxxxx)           |
| `ml_reference`    | string|null  | Agrupador lógico KOI (editable)          |
| `ml_name`         | string|null  | Título editable                          |
| `ml_description`  | text|null    | Descripción editable                     |
| `mlibre_precio`   | decimal|null | Precio editable o extraído               |
| `mlibre_stock`    | int|null     | Stock editable o extraído                |
| `status`          | string       | Estado ML (active, paused...)            |
| `raw_json`        | json         | JSON completo de la publicación          |

### `ml_variantes`

| Campo             | Tipo         | Descripción                              |
|------------------|--------------|------------------------------------------|
| `ml_publicacion_id` | FK         | Relación con `ml_publicaciones`          |
| `sku_`            | string|null  | Clave de vinculación con KOI             |
| `talle`           | string|null  | Talle de la variante                     |
| `precio`          | decimal|null | Precio de esta variante                  |
| `stock`           | int|null     | Stock disponible de esta variante        |
| `raw_json`        | json         | JSON completo de la variante             |

---

## 🔄 Flujo actual

1. **`curl` + OAuth** → descarga publicaciones como `.json`
2. **Comando Artisan** → guarda publicaciones y variantes en BD
3. **Vista Index** → lista todas las publicaciones, filtrables
4. **Vista Edit** → formulario con campos editables + subformulario de variantes

---

## 🛠 Próximos pasos sugeridos

1. **Edición masiva por `ml_reference`**
   - Agrupar publicaciones con el mismo `ml_reference`
   - Permitir editar múltiples precios y stocks al mismo tiempo

2. **Integración con KOI (stock y precio)**
   - Obtener sugerencias desde KOI por `sku_`
   - Mostrar diferencias visuales (ej: comparador)

3. **Sincronización hacia Mercado Libre**
   - Generar payloads para `PUT /items/{id}` usando los campos editables
   - Confirmación y logs por publicación

4. **Vinculación automática KOI ↔ ML**
   - Sugerir `ml_reference` a partir del `sku_`
   - Mostrar vínculo con artículos y colores de KOI

5. **Botón "Duplicar publicación"**
   - Generar una nueva publicación a partir de una existente

---

## 🧠 Observaciones

- El `sku_` es el mejor nexo entre ML y KOI.
- El uso de `raw_json` completo permite mayor flexibilidad sin comprometer integridad.
- La estructura actual ya permite escalar hacia sincronización bidireccional.
