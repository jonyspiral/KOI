Consulta exitosa a la API de Mercado Libre con User-Agent personalizado
Este instructivo documenta cómo resolver el error Invalid app_version al consumir la API pública de Mercado Libre desde Laravel y cómo aplicar esta solución para integrar correctamente las campañas promocionales.

🧩 Problema
Al consultar cualquier endpoint de la API de Mercado Libre, como /items/{id} o /seller-promotions/..., con un token válido, podés recibir este error:

json
Copiar
Editar
{
  "message": "Invalid app_version",
  "error": "bad_request",
  "status": 400,
  "cause": []
}
🧠 Causa
Mercado Libre requiere que todas las aplicaciones usen un User-Agent válido, que incluya la identidad de la app y su versión. Omitirlo produce errores como el anterior.

🛠 Solución
✅ Encabezados HTTP obligatorios
php
Copiar
Editar
$response = Http::withHeaders([
    'Authorization' => "Bearer $token",
    'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
    'Accept'        => 'application/json',
    'Content-Type'  => 'application/json',
])->get("https://api.mercadolibre.com/items/MLA1107851967");

dd($response->json());
💡 Recomendación técnica para KOI2
Centralizar los encabezados requeridos en un macro:

php
Copiar
Editar
Http::macro('ml', function ($token) {
    return Http::withHeaders([
        'Authorization' => "Bearer $token",
        'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
    ]);
});
Uso posterior:

php
Copiar
Editar
$response = Http::ml($token)->get("https://api.mercadolibre.com/seller-promotions/users/{$userId}?app_version=v2");
🧪 Resultado validado
Se resolvió correctamente el error Invalid app_version.

Se logró consumir seller-promotions con éxito.

Se detectaron campañas activas de tipo DEAL, SMART, COUPON, etc.

📌 Uso validado con publicaciones
Para consultar si un ítem está en promoción:

bash
Copiar
Editar
GET /seller-promotions/promotions/{promotion_id}/items?promotion_type=DEAL&item_id={item_id}&app_version=v2
✅ Se puede determinar fehacientemente si una publicación participa en una campaña promocional activa.

🔑 Credenciales de referencia
APP ID: 3974289321121032

User ID: 448490530

User-Agent: KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)