<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produccion\ArticuloColorController;
use App\Http\Controllers\Mlibre\WebhookController;

Route::post('/mlibre/webhook', [WebhookController::class, 'handle']);


// Ruta de prueba
/* Route::get('/test-api', function () {
    return response()->json(['message' => 'API funcionando']);
});
 */
// ABM de ArticuloColor
/* Route::get('/articulos/{cod_articulo}/colores', [ArticuloColorController::class, 'index']);
Route::post('/articulos/{cod_articulo}/colores', [ArticuloColorController::class, 'store']);
Route::put('/articulos/{cod_articulo}/colores/{cod_color_articulo}', [ArticuloColorController::class, 'update']);
Route::delete('/articulos/{cod_articulo}/colores/{cod_color_articulo}', [ArticuloColorController::class, 'destroy']);
 */