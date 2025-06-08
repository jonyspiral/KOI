# OAuth Mercado Libre - Resumen de Integración

Este documento resume los pasos implementados en KOI2 para la autenticación e integración con la API de Mercado Libre mediante OAuth 2.0.

## 🔐 Flujo OAuth

1. **Inicio de autenticación**

   * URL: `https://auth.mercadolibre.com.ar/authorization`
   * Parámetros:

     * `response_type=code`
     * `client_id=MLIBRE_APP_ID`
     * `redirect_uri=MLIBRE_REDIRECT_URI`
   * Acción: Redirecciona al usuario a autorizar la aplicación.

2. **Callback**

   * Ruta: `/mlibre/callback`
   * Recibe: `code` como parámetro
   * Acción: Intercambia el código por un `access_token` y `refresh_token`:

     ```http
     POST https://api.mercadolibre.com/oauth/token
     Content-Type: application/x-www-form-urlencoded

     grant_type=authorization_code
     client_id=...
     client_secret=...
     code=...
     redirect_uri=...
     ```

3. **Almacenamiento**

   * Tabla: `mlibre_tokens`
   * Campos: `user_id`, `access_token`, `refresh_token`, `expires_at`
   * Lógica: Si el token ya existe para ese usuario, se actualiza. Si no, se crea.

4. **Renovación automática**

   * Método: `getValidAccessToken()`
   * Acción: Revisa si el token está vencido. Si lo está, usa `refresh_token` para renovarlo:

     ```http
     POST https://api.mercadolibre.com/oauth/token

     grant_type=refresh_token
     client_id=...
     client_secret=...
     refresh_token=...
     ```
   * Actualiza nuevamente `mlibre_tokens` con el nuevo token y fecha de expiración.

## 🔎 Pruebas realizadas

* ✅ Autenticación en entorno **development** y **producción**.
* ✅ Redirección y almacenamiento de tokens.
* ✅ Uso del token para realizar requests:

  * `GET /users/{user_id}/items/search`
  * `GET /items/{item_id}`
  * `POST /items` (publicación de test)

## 📌 Variables de entorno

```env
MLIBRE_APP_ID=...
MLIBRE_SECRET=...
MLIBRE_REDIRECT_URI=https://devkoi2.spiralshoes.com/mlibre/callback
MLIBRE_ENV=development
```

En producción:

```env
MLIBRE_REDIRECT_URI=https://koi2.spiralshoes.com/mlibre/callback
MLIBRE_ENV=production
```

## 🗂️ Rutas Laravel

```php
Route::prefix('mlibre')->group(function () {
    Route::get('/auth', [MeliAuthController::class, 'redirect'])->name('mlibre.auth');
    Route::get('/callback', [MeliAuthController::class, 'callback'])->name('mlibre.callback');
    Route::get('/publicar-test', [MeliAuthController::class, 'publicarTest']);
    Route::get('/test-categoria', [MeliAuthController::class, 'testCategoria']);
});
```

---

> Este flujo quedó funcional y validado. Podemos extenderlo con gestión de múltiples cuentas, publicaciones, imágenes, stock, y más.
