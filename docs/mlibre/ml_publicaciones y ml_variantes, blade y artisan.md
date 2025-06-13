# 📦 Flujo de Carga de Publicaciones y Variantes ML

## ✅ FLUJO COMPLETO: CARGA DE PUBLICACIONES Y VARIANTES

### 1. Descarga de Publicaciones desde JSON
Los archivos `.json` descargados desde la API de Mercado Libre se almacenan localmente en:

```
storage/app/private/mlibre/items/
```

### 2. Command: `mlibre:importar-json`
Este comando recorre todos los JSON y procesa publicaciones con y sin variantes.

#### 📥 Tabla `ml_publicaciones`:
Campos importantes:
- `ml_id`, `ml_name`
- `family_id`, `family_name`
- `category_id`, `official_store_id`
- `currency_id`, `price`, etc.

#### 📥 Tabla `ml_variantes`:
Por cada `variation` o para publicaciones sin variantes:
- `ml_id`, `variation_id`, `titulo`
- `color`, `talle`, `modelo`
- `precio`, `stock`, `stock_full`, `stock_flex`
- `product_number`, `seller_sku`, `seller_custom_field`
- `nuevo_seller_custom_field`, `seller_custom_field_actual`, `sincronizado`

---

## 🧩 Blade: `resources/views/mlibre/variantes.blade.php`

### 🎯 Objetivo:
Gestionar y editar variantes publicadas en Mercado Libre.

### ⚙️ Características:

#### 1. Filtros por campo
Inputs dinámicos para:
- `ml_id`, `color`, `talle`, `modelo`, `titulo`, `seller_sku`, `variation_id`, `product_number`, `seller_custom_field`

#### 2. Ordenamiento (`sort_link`)
Ordenar por:
- `ml_id`, `variation_id`, `modelo`, `precio`, `stock`, etc.

#### 3. Paginación
- Cantidad total de variantes
- Navegación con `links()`

#### 4. Edición rápida del campo SCF nuevo
- Campo `nuevo_seller_custom_field`
- Botón 💾 para guardar en lote

#### 5. Publicación individual de SCF
- Botón 🔁 para publicar SCF a través de la API ML

#### 6. Exportar a Excel
- Botón 📤 que genera un `.xlsx` con las variantes visibles

#### 7. Sincronización de SKUs
- Botón 🔄 para sincronizar todos los SKUs (opcional)

#### 8. Visualización completa
- Campos: `ml_id`, `variation_id`, `titulo`, `modelo`, `seller_sku`, `product_number`, `color`, `talle`, `precio`, `stock`, `stock_flex`, `stock_full`, `SCF actual`, `nuevo SCF`, `sincronizado`

---

## 🚧 Próximos pasos sugeridos
- [ ] Automatizar carga (`schedule`)
- [ ] Implementar `mlibre:actualizar-stock`
- [ ] Edición masiva por grupo
- [ ] Validaciones de campos

