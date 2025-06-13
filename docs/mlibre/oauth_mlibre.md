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

# 🛠️ Tinker Manual: Renovación Manual del Token de Mercado Libre

Este procedimiento permite **renovar manualmente el `access_token`** de Mercado Libre usando Laravel Tinker, sin necesidad de ejecutar el flujo completo OAuth.

---

## ✅ Verificar token existente

```php
use Illuminate\Support\Facades\DB;

$old = DB::table('mlibre_tokens')->first();
$old->refresh_token;
```

---

## 🔁 Renovar token manualmente vía API

```php
use Illuminate\Support\Facades\Http;

$response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
    'grant_type'    => 'refresh_token',
    'client_id'     => env('MLIBRE_APP_ID'),
    'client_secret' => env('MLIBRE_SECRET'),
    'refresh_token' => $old->refresh_token,
]);

$data = $response->json();
```

---

## 💾 Guardar token renovado

```php
DB::table('mlibre_tokens')->update([
    'access_token'  => $data['access_token'],
    'refresh_token' => $data['refresh_token'],
    'expires_at'    => now()->addSeconds($data['expires_in']),
    'updated_at'    => now(),
]);
```

---

## 🧪 Verificar validez del token

```php
$response = Http::withToken($data['access_token'])->get('https://api.mercadolibre.com/users/me');

$response->status(); // debe ser 200 si es válido
$response->json();   // muestra info del usuario
```

---

## 🧩 Notas adicionales

- Este flujo puede automatizarse con el comando Artisan:

  ```bash
  php artisan mlibre:renovar-token
  ```


  ```

- Si el token no es válido, verás un error como:

  ```json
  {
    "status": 403,
    "code": "PA_UNAUTHORIZED_RESULT_FROM_POLICIES",
    "message": "At least one policy returned UNAUTHORIZED.",
    "blocked_by": "PolicyAgent"
  }
  ```✨ Autenticación y Manejo de Tokens para Múltiples Usuarios de Mercado Libre

Este documento describe cómo KOI2 gestiona múltiples cuentas de Mercado Libre utilizando el sistema de tokens OAuth 2.0. Cada token se asocia a un user_id de ML.

1. 🔐 Tabla mlibre_tokens

Estructura:

id BIGINT
user_id BIGINT NOT NULL
access_token VARCHAR(255)
refresh_token VARCHAR(255)
expires_at DATETIME
created_at DATETIME
updated_at DATETIME

Cada fila representa un token autorizado por un vendedor de Mercado Libre.

2. 🚀 Servicio: MlibreTokenService

public function getValidAccessToken(int $userId): string

Si el token existe y no expiró, lo devuelve.

Si está vencido, lo renueva con el refresh_token.

Si no existe, lanza una excepción.

$response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
    'grant_type' => 'refresh_token',
    'client_id' => env('MLIBRE_APP_ID'),
    'client_secret' => env('MLIBRE_SECRET'),
    'refresh_token' => $token->refresh_token,
]);

3. 📁 Flujo de Autenticación OAuth

Ya implementado en rutas:

Route::prefix('mlibre')->group(function () {
    Route::get('/auth', [MeliAuthController::class, 'redirect']);
    Route::get('/callback', [MeliAuthController::class, 'callback']);
});

En el callback:

POST https://api.mercadolibre.com/oauth/token

Guarda o actualiza el token en mlibre_tokens

Guarda el user_id del vendedor

4. 🔄 Comando: mlibre:renovar-tokens

Renueva los tokens de todos los usuarios registrados.

php artisan mlibre:renovar-tokens

Itera sobre cada user_id y llama a:

getValidAccessToken($userId);

5. ❓ Cómo determinar el user_id

Autenticación OAuth devuelve user_id directamente.

Desde publicaciones: si se conoce el ml_id, se puede mapear al user_id mediante metadatos.

En sistemas multiusuario: guardar asociación local usuario del sistema <-> user_id de ML

6. ⚡ Casos de uso

Publicaciones desde varias cuentas.

Autenticación desde apps internas o externas.

Interacción concurrente sin colisiones de token.

7. 💾 Consideraciones

access_token expira a las 6 horas

refresh_token puede ser revocado si el usuario desconecta la app

Siempre validar estado del token antes de hacer un request

Gestionar errores HTTP 401/403

Documentación actualizada al 10 de junio de 2025


se agrego la ML_USER_ID del .ENV al codigo para que no de error al pedir el Token  comando para solicitarlo:
 app(App\Services\Mlibre\MlibreTokenService::class)->getValidAccessToken()
