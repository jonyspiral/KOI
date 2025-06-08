<?php
use App\Http\Controllers\Sistemas\Importar\ImportarController;
use App\Http\Controllers\Sistemas\Abms\AbmCreatorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

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
use App\Http\Controllers\Produccion\ColoresPorArticuloController;
use App\Http\Controllers\Mlibre\MlibreItemsController;
use App\Http\Controllers\Mlibre\MeliAuthController;
use App\Http\Controllers\Mlibre\PublicacionesController;

Route::prefix('mlibre/publicaciones')->group(function () {
    Route::get('/', [PublicacionesController::class, 'index'])->name('mlibre.publicaciones.index');
    Route::get('/{id}/edit', [PublicacionesController::class, 'edit'])->name('mlibre.publicaciones.edit');
    Route::put('/{id}', [PublicacionesController::class, 'update'])->name('mlibre.publicaciones.update');
});

Route::prefix('mlibre')->group(function () {
    Route::get('/auth', [MeliAuthController::class, 'redirect'])->name('mlibre.auth');
    Route::get('/callback', [MeliAuthController::class, 'callback'])->name('mlibre.callback');
});


Route::get('/mlibre/publicar', [MlibreItemsController::class, 'formPublicar'])->name('mlibre.publicar');
Route::post('/mlibre/publicar', [MlibreItemsController::class, 'generarPublicaciones'])->name('mlibre.publicar.enviar');
Route::get('/mlibre/test-publicar-pow', [MlibreItemsController::class, 'testPowSkateb']);

Route::prefix('meli/items')->group(function () {
    Route::get('/', [MlibreItemsController::class, 'listar']);
    Route::get('{id}', [MlibreItemsController::class, 'ver']);
    Route::put('{id}/activar', [MlibreItemsController::class, 'activar']);
    Route::put('{id}/pausar', [MlibreItemsController::class, 'pausar']);
    Route::post('{id}/actualizar', [MlibreItemsController::class, 'actualizar']);
});
Route::get('/mlibre/test-publicar-pow', [MlibreItemsController::class, 'testPowSkateb']);



if (App::environment('development')) {
    Route::get('/', function () {
        return 'KOI2 Desarrollo Activo';
    });
} elseif (App::environment('production')) {
    Route::get('/', function () {
        return view('welcome');
    });
}


Route::get('/meli/callback', [MeliAuthController::class, 'callback']);
if (App::environment('development')) {
    Route::get('/meli/publicar-test', [MeliAuthController::class, 'publicarTest']);
}
Route::get('/meli/test-categoria', [MeliAuthController::class, 'testCategoria']);


Route::get('/mlibre/variantes/{sku}', [MlibreItemsController::class, 'verVariantes'])->name('mlibre.publicar.variantes');

Route::post('/mlibre/variantes/{sku}/publicar', [MlibreItemsController::class, 'publicarVariantes'])->name('mlibre.publicar.variantes.enviar');
Route::get('/mlibre/variantes', [\App\Http\Controllers\Mlibre\MlibreItemsController::class, 'formPublicarVariantes'])->name('mlibre.variantes');




// Creador ABMs
Route::prefix('sistemas/abms')->group(function () {
    Route::get('/crear', [AbmCreatorController::class, 'index'])->name('sistemas.abms.crear');

    // ✅ Agregar soporte para GET y POST en preview
    Route::match(['get', 'post'], '/preview/{modelo}', [AbmCreatorController::class, 'preview'])->name('sistemas.abms.preview');
    
    Route::post('/configurar', [AbmCreatorController::class, 'configurar'])->name('sistemas.abms.configurar');
    Route::post('/guardar-subformulario', [AbmCreatorController::class, 'guardarSubformulario'])->name('sistemas.abms.guardar_subformulario');

});

Route::post('/abmcreator/campos-subformulario', [AbmCreatorController::class, 'cargarCamposSubformulario'])->name('abmcreator.campos_subformulario');
//importar tablas koi ABMs  
Route::prefix('sistemas/importar')->name('sistemas.importar.')->group(function () {
    Route::get('/form', [ImportarController::class, 'form'])->name('form');
    Route::post('/importar', [ImportarController::class, 'importar'])->name('importar');
});
Route::post('sistemas/importar/eliminar-config', [ImportarController::class, 'eliminarConfig'])
    ->name('sistemas.importar.eliminar_config');
    
Route::get('/test-importar', function () {
    return route('sistemas.importar.form');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: RutasProduccion - Generado el 2025-03-30 08:11:41
Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('rutas_produccion', RutasProduccionController::class)->names('rutas_produccion');
});


// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Curva - Generado el 2025-04-02 00:56:03


Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('curvas', CurvaController::class)->names('curvas');
});



// 🧩 Ruta de prueba para el layout Master-Detail Livewire
// Muestra un producto con sus colores relacionados (cabecera + subform)


Route::get('products/{id}/with-colors', [ProductController::class, 'showWithColors'])
    ->name('products.showWithColors');

    

Route::put('products/{id}', function () {
    return redirect()->back()->with('status', 'Guardado de prueba.');
})->name('products.update');



// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: SeccionesProduccion - Generado el 2025-04-12 11:01:31


Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
    Route::resource('secciones_produccion', SeccionesProduccionController::class)->names('secciones_produccion');
});




// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: ArticulosNew - Generado el 2025-04-18 01:59:59


Route::resource('produccion/abms/articulos_new', ArticulosNewController::class)
    ->names('produccion.abms.articulos_new');
    Route::put('produccion/abms/articulos_new/restaurar/{id}', [\App\Http\Controllers\Produccion\ArticulosNewController::class, 'restaurar'])
    ->name('produccion.abms.articulos_new.restaurar');
    
   
// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: FamiliasProducto - Generado el 2025-04-20 15:27:02


Route::prefix('produccion/abms/familias_producto')->name('produccion.abms.familias_producto.')->group(function () {
    Route::resource('', FamiliasProductoController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    
    Route::post('{id}/restaurar', [FamiliasProductoController::class, 'restaurar'])->name('restaurar');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Horma - Generado el 2025-04-20 15:57:31


Route::prefix('produccion/abms/hormas')->name('produccion.abms.hormas.')->group(function () {
    Route::resource('', HormaController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    
    Route::post('{id}/restaurar', [HormaController::class, 'restaurar'])->name('restaurar');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Marca - Generado el 2025-04-21 16:35:43


Route::prefix('produccion/abms/marcas')->name('produccion.abms.marcas.')->group(function () {
    Route::resource('', MarcaController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    
    Route::post('{id}/restaurar', [MarcaController::class, 'restaurar'])->name('restaurar');
});
Route::get('exportar-colores-articulo', [\App\Http\Controllers\Produccion\ColoresPorArticuloController::class, 'exportar'])->name('colores.exportar');


// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: ColoresPorArticulo - Generado el 2025-04-23 01:50:34
;

Route::prefix('produccion/abms/colores_por_articulo')->name('produccion.abms.colores_por_articulo.')->group(function () {
    Route::resource('', ColoresPorArticuloController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    
    Route::post('{id}/restaurar', [ColoresPorArticuloController::class, 'restaurar'])->name('restaurar');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Articulo - Generado el 2025-04-25 02:21:00


Route::prefix('produccion/abms/articulos')->name('produccion.abms.articulos.')->group(function () {
    Route::resource('', ArticuloController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    
    Route::post('{id}/restaurar', [ArticuloController::class, 'restaurar'])->name('restaurar');
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: PasosRutasProduccion - Generado el 2025-04-25 16:29:01


Route::prefix('produccion/abms/pasos_rutas_produccion')->name('produccion.abms.pasos_rutas_produccion.')->group(function () {
    Route::resource('', PasosRutasProduccionController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    
    Route::post('{id}/restaurar', [PasosRutasProduccionController::class, 'restaurar'])->name('restaurar');
});





// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Horma - Generado el 2025-05-02 03:46:08


Route::prefix('produccion/abms/horma')->name('produccion.abms.horma.')->group(function () {
    Route::resource('', HormaController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index'   => 'index',
            'create'  => 'create',
            'store'   => 'store',
            'show'    => 'show',
            'edit'    => 'edit',
            'update'  => 'update',
            'destroy' => 'destroy',
        ]);

    Route::post('{id}/restaurar', [HormaController::class, 'restaurar'])->name('restaurar');
});
// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Almacen - Generado el 2025-05-04 14:37:36


Route::prefix('produccion/abms/almacenes')->name('produccion.abms.almacenes.')->group(function () {
    Route::resource('', AlmacenController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index'   => 'index',
            'create'  => 'create',
            'store'   => 'store',
            'show'    => 'show',
            'edit'    => 'edit',
            'update'  => 'update',
            'destroy' => 'destroy',
        ]);

    Route::post('{id}/restaurar', [AlmacenController::class, 'restaurar'])->name('restaurar');
});




// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: Articulo - Generado el 2025-05-20 18:09:06


Route::prefix('produccion/abms/articulo')->name('produccion.abms.articulo.')->group(function () {
    Route::resource('', ArticuloController::class)
        ->parameters(['' => 'id'])
        ->names([
            'index'   => 'index',
            'create'  => 'create',
            'store'   => 'store',
            'show'    => 'show',
            'edit'    => 'edit',
            'update'  => 'update',
            'destroy' => 'destroy',
        ]);

    Route::post('{id}/restaurar', [ArticuloController::class, 'restaurar'])->name('restaurar');
});