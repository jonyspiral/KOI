
# 🛠️ Avance Sincronización de Variantes ML (`ml_variantes`) — Spiral Shoes

## ✅ Objetivo

Importar publicaciones de Mercado Libre desde archivos JSON y sincronizarlas con KOI2, permitiendo edición y eventual envío del campo `seller_custom_field`.

---

## 📁 Ubicación de JSON

- Carpeta: `/storage/app/private/mlibre/items`
- Cada archivo representa una publicación descargada vía API.

---

## 🗃️ Estructura de la tabla `ml_variantes`

Se confirmó y adaptó la tabla con los siguientes campos clave:

```sql
ml_variantes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ml_id VARCHAR,
    variation_id BIGINT,
    color VARCHAR,
    talle VARCHAR,
    precio DECIMAL(10,2),
    stock INT,
    seller_custom_field_actual VARCHAR,
    var_sku_sugerido VARCHAR,
    nuevo_seller_custom_field VARCHAR,
    sincronizado TINYINT,
    raw_json JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

---

## ⚙️ Proceso implementado

### 1. Comando Artisan

```bash
php artisan mlibre:parsear-json-variantes
```

- Parsea los archivos `.json` de Mercado Libre.
- Inserta las variantes en `ml_variantes`, extrayendo:
  - `ml_id`, `variation_id`, `color`, `talle`, `precio`, `stock`
  - `var_sku_sugerido`: generado a partir de `ml_id + inicial color + talle`
- Se usa `truncate()` previamente para limpiar.

### 2. Modelo `MlVariante`

```php
class MlVariante extends Model
{
    protected $table = 'ml_variantes';

    protected $fillable = [
        'ml_id', 'variation_id', 'color', 'talle',
        'precio', 'stock', 'seller_custom_field_actual',
        'var_sku_sugerido', 'nuevo_seller_custom_field',
        'sincronizado', 'raw_json'
    ];

    protected $casts = [
        'raw_json' => 'array',
    ];
}
```

---

## 🧩 Vista de Edición (`/mlibre/variantes`)

- Controlador: `MlibreVariantesController`
- Vista: muestra la lista de variantes con campos editables.
- Permite ingresar manualmente el `nuevo_seller_custom_field`.

```php
<td>
  <input type="text" name="variantes[{{ $v->id }}][nuevo_seller_custom_field]"
         value="{{ $v->nuevo_seller_custom_field ?? $v->var_sku_sugerido }}">
</td>
```

---

## 🔄 Pendiente

- Agregar botón de sincronización hacia Mercado Libre vía API (`PUT /items/:id/variations/:id`).
- Implementar campo `seller_custom_field_actual` desde `raw_json`.
- Crear vista `mlibre_variantes_update_v` unificada con datos KOI.
