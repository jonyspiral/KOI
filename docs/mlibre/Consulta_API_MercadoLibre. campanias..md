
# ✅ Consulta exitosa a la API de Mercado Libre con User-Agent personalizado

Este instructivo documenta cómo resolver el error `Invalid app_version` al consumir la API pública de Mercado Libre desde Laravel.

---

## 🧩 Problema

Al consultar cualquier endpoint de la API de Mercado Libre, como `/items/{id}`, con un token válido, podés recibir el siguiente error:

```json
{
  "message": "Invalid app_version",
  "error": "bad_request",
  "status": 400,
  "cause": []
}
```

---

## 🧠 Causa

Mercado Libre **requiere que todas las apps especifiquen un `User-Agent` válido** que identifique la aplicación y su versión. No hacerlo puede disparar este error.

---

## 🛠 Solución

### ✅ Encabezados HTTP obligatorios

```php
$response = Http::withHeaders([
    'Authorization' => "Bearer $token",
    'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
    'Accept'        => 'application/json',
    'Content-Type'  => 'application/json',
])->get("https://api.mercadolibre.com/items/MLA1107851967");

dd($response->json());
```

---

### 📌 Resultado

La respuesta fue correctamente devuelta por Mercado Libre:

- ID del item: `MLA1107851967`
- Título: `Zapatillas Spiral Pow One Flow Small Tienda Oficial`
- Precio: `107490`
- Cantidad disponible: `268`
- Logística: `xd_drop_off`
- Marca: `Spiral`

✅ Se validó que agregar `User-Agent` personalizado evita el error `Invalid app_version`.

---

## 💡 Recomendación

Centralizar el header con un macro Laravel:

```php
Http::macro('ml', function ($token) {
    return Http::withHeaders([
        'Authorization' => "Bearer $token",
        'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
    ]);
});
```

Y luego:

```php
Http::ml($token)->get(...);
```

---

## 📁 Uso futuro: campañas promocionales

Con esta validación resuelta, ahora podés consultar también:

```
GET https://api.mercadolibre.com/seller-promotions/items/{item_id}
```

Y ver si hay campañas activas para tus publicaciones.

---

## 📌 Credenciales utilizadas

- **APP ID:** `3974289321121032`
- **User ID:** `448490530`
- **User-Agent:** `KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)`

---

Este instructivo puede guardarse como documentación técnica de integración Mercado Libre en KOI2.
