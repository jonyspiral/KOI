



<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sistemas\Abms\AbmCreatorController;
use App\Http\Controllers\Sistemas\Importar\ImportarController;
use App\Http\Controllers\Produccion\MarcaController;
use App\Http\Controllers\Produccion\HormaController;
use App\Http\Controllers\Produccion\RangoTalleController;

use App\Http\Controllers\Produccion\RutasProduccionController;
use App\Http\Controllers\Produccion\ArticulosNewController;
use App\Http\Controllers\Produccion\SeccionesProduccionController;
use App\Http\Controllers\Produccion\ArticuloController;
use App\Http\Controllers\Produccion\FamiliasProductoController;
use App\Http\Controllers\Produccion\CurvaController;
use App\Http\Controllers\Produccion\PasosRutasProduccionController;
use App\Http\Controllers\Produccion\ProductController;
use App\Http\Controllers\Produccion\AlmacenController;

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
Route::post('sistemas/importar/eliminar-config', [ImportarController::class, 'eliminarConfig'])
    ->name('sistemas.importar.eliminar_config');
    
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
// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: FamiliasProducto - Generado el 2025-04-01 09:46:58

Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('familias_producto', FamiliasProductoController::class)->names('familias_producto');
});


// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Curva - Generado el 2025-04-02 00:56:03


Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('curvas', CurvaController::class)->names('curvas');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: FamiliasProducto - Generado el 2025-04-02 05:28:50


Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('familias_productos', FamiliasProductoController::class)->names('familias_productos');
});

// 🧩 Ruta de prueba para el layout Master-Detail Livewire
// Muestra un producto con sus colores relacionados (cabecera + subform)


Route::get('products/{id}/with-colors', [ProductController::class, 'showWithColors'])
    ->name('products.showWithColors');

    

Route::put('products/{id}', function () {
    return redirect()->back()->with('status', 'Guardado de prueba.');
})->name('products.update');

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: PasosRutasProduccion - Generado el 2025-04-04 18:05:35

Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('pasos_rutas_produccion', PasosRutasProduccionController::class)->names('pasos_rutas_produccion');
});




Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('marcas', MarcaController::class)->names('marcas');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: SeccionesProduccion - Generado el 2025-04-12 11:01:31


Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('secciones_produccion', SeccionesProduccionController::class)->names('secciones_produccion');
});








// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Almacen - Generado el 2025-04-17 18:57:50


Route::resource('produccion/abms/almacenes', AlmacenController::class)
    ->names('produccion.abms.almacenes');

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: SeccionesProduccion - Generado el 2025-04-17 19:02:13


Route::resource('produccion/abms/secciones_produccion', SeccionesProduccionController::class)
    ->names('produccion.abms.secciones_produccion');

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: ArticulosNew - Generado el 2025-04-18 01:59:59


Route::resource('produccion/abms/articulos_new', ArticulosNewController::class)
    ->names('produccion.abms.articulos_new');
