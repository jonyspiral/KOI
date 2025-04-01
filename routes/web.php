



<?php
use App\Http\Controllers\Produccion\HormaController;
use App\Http\Controllers\Produccion\RangoTalleController;
use App\Http\Controllers\Produccion\MarcasSyncViewSpiralController;
use App\Http\Controllers\Produccion\RutasProduccionController;
use App\Http\Controllers\Produccion\ArticulosNewController;

use App\Http\Controllers\Produccion\SeccionesProduccionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produccion\ArticuloController;

use App\Http\Controllers\Sistemas\Abms\AbmCreatorController;

use App\Http\Controllers\Sistemas\Importar\ImportarController;

//creador ABMs  

/* Route::prefix('sistemas/abms')->name('abms.')->group(function () {
    Route::get('/crear', [AbmCreatorController::class, 'index'])->name('crear');
    Route::post('/preview', [AbmCreatorController::class, 'preview'])->name('preview');
    Route::post('/generar', [AbmCreatorController::class, 'generar'])->name('generar');
    Route::post('/generar-controlador', [AbmCreatorController::class, 'generarControlador'])->name('generar-controlador');
    Route::post('/generar-vistas', [AbmCreatorController::class, 'generarVistas'])->name('generar-vistas');
    Route::post('/finalizar', [AbmCreatorController::class, 'finalizar'])->name('finalizar');
   // Route::get('/configurar', [AbmCreatorController::class, 'configurar'])->name('configurar');

});
Route::post('/sistemas/abms/configurar', [AbmCreatorController::class, 'configurar'])->name('abms.configurar');
 */

Route::prefix('sistemas/abms')->group(function () {
    Route::get('/crear', [AbmCreatorController::class, 'index'])->name('sistemas.abms.crear');
    Route::post('/preview', [AbmCreatorController::class, 'redirectToPreview'])->name('sistemas.abms.preview.redirect');
    Route::get('/preview/{modelo}', [AbmCreatorController::class, 'preview'])->name('sistemas.abms.preview');
    Route::post('/configurar', [AbmCreatorController::class, 'configurar'])->name('sistemas.abms.configurar');
});




//importar tablas koi ABMs  
Route::prefix('sistemas/importar')->name('sistemas.importar.')->group(function () {
    Route::get('/form', [ImportarController::class, 'form'])->name('form');
    Route::post('/importar', [ImportarController::class, 'importar'])->name('importar');
});


// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Articulo - Generado el 2025-03-30 07:36:58
Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('articulos', ArticuloController::class)->names('articulos');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Horma - Generado el 2025-03-30 07:40:58
Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('hormas', HormaController::class)->names('hormas');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: RutasProduccion - Generado el 2025-03-30 08:11:41
Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('rutas_produccion', RutasProduccionController::class)->names('rutas_produccion');
});