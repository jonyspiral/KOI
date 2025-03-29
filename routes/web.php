
<?php

use App\Http\Controllers\Produccion\SeccionesProduccionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produccion\ArticuloController;

use App\Http\Controllers\Sistemas\Abms\AbmCreatorController;

use App\Http\Controllers\Sistemas\Importar\ImportarController;

//creador ABMs  

Route::prefix('sistemas/abms')->name('abms.')->group(function () {
    Route::get('/crear', [AbmCreatorController::class, 'index'])->name('crear');
    Route::post('/preview', [AbmCreatorController::class, 'preview'])->name('preview');
    Route::post('/generar', [AbmCreatorController::class, 'generar'])->name('generar');
    Route::post('/generar-controlador', [AbmCreatorController::class, 'generarControlador'])->name('generar-controlador');
    Route::post('/generar-vistas', [AbmCreatorController::class, 'generarVistas'])->name('generar-vistas');
    Route::post('/finalizar', [AbmCreatorController::class, 'finalizar'])->name('finalizar');
   // Route::get('/configurar', [AbmCreatorController::class, 'configurar'])->name('configurar');

});
Route::post('/sistemas/abms/configurar', [AbmCreatorController::class, 'configurar'])->name('abms.configurar');


//importar tablas koi ABMs  
Route::prefix('sistemas/importar')->name('sistemas.importar.')->group(function () {
    Route::get('/form', [ImportarController::class, 'form'])->name('form');
    Route::post('/importar', [ImportarController::class, 'importar'])->name('importar');
});


//Route::resource('', App\Http\Controllers\Abms\Controller::class);





Route::resource('produccion/secciones-produccion', SeccionesProduccionController::class)->names('produccion.secciones_produccion');


// 🧩 Ruta generada automáticamente por ABM Creator
use App\Http\Controllers\Produccion\ArticulosNewController;
Route::resource('produccion/articulos-new', ArticulosNewController::class)->names('produccion.articulos_new');




use App\Http\Controllers\Produccion\RutasProduccionController;

Route::prefix('produccion')->name('produccion.')->group(function () {
    Route::resource('rutas_produccion', RutasProduccionController::class)
        ->names('rutas_produccion');
});





// 🧩 Ruta generada automáticamente por ABM Creator
use App\Http\Controllers\Produccion\ForecastEncabezadoController;
Route::resource('forecast_encabezado', ForecastEncabezadoController::class)->names('forecast_encabezado');




// 🧩 Ruta generada automáticamente por ABM Creator
Route::resource('secciones_produccion', SeccionesProduccionController::class)->names('secciones_produccion');
