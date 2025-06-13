# 🛠️ Comandos Artisan - Mercado Libre KOI2

## 📦 Comando: `mlibre:actualizar-publicacion`
**Descripción:** Actualiza el stock y/o el SKU (`seller_custom_field`) de publicaciones en Mercado Libre.  
Soporta publicaciones con o sin variaciones.

### 🧠 Lógica:
- Si la publicación tiene variaciones:
  - Se requiere `--variation={id}`.
  - Actualiza `available_quantity` y/o `seller_custom_field` en la variación.
  - **No** se modifica `attributes[].SELLER_SKU`.
  - Si tiene `inventory_id` (FULL), no se permite update.

- Si la publicación **no tiene variaciones**:
  - Actualiza `available_quantity` y/o `seller_custom_field` en el nivel del ítem.
  - También actualiza o crea el atributo `SELLER_SKU` en `attributes[]`.

### 🧪 Ejemplo:
```bash
php artisan mlibre:actualizar-publicacion MLA2083830308 --sku=8031B1205
php artisan mlibre:actualizar-publicacion MLA1976421938 --variation=186495712969 --stock=5 --sku=DROP-B45
```

---

## ⏱️ BREAK: 2025-06-13 02:42:21
