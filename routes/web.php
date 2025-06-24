<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Sistemas\MenuController;
use App\Http\Controllers\Sistemas\Importar\ImportarController;
use App\Http\Controllers\Sistemas\Abms\AbmCreatorController;
use App\Http\Controllers\Produccion\RutasProduccionController;
use App\Http\Controllers\Produccion\CurvaController;
use App\Http\Controllers\Produccion\SeccionesProduccionController;
use App\Http\Controllers\Produccion\FamiliasProductoController;
use App\Http\Controllers\Produccion\HormaController;
use App\Http\Controllers\Produccion\MarcaController;
use App\Http\Controllers\Produccion\ColoresPorArticuloController;
use App\Http\Controllers\Produccion\ArticuloController;
use App\Http\Controllers\Produccion\PasosRutasProduccionController;
use App\Http\Controllers\Produccion\AlmacenController;
use App\Http\Controllers\Produccion\TipoProductoStockController;
use App\Http\Controllers\Produccion\LineasProductoController;
use App\Http\Controllers\Produccion\ProductController;
use App\Http\Controllers\Produccion\ArticulosNewController;
use App\Http\Controllers\Mlibre\MlibreVariantesController;
use App\Http\Controllers\Mlibre\MlibreSyncController;
use App\Http\Controllers\Mlibre\MlibreItemsController;
use App\Http\Controllers\Mlibre\MeliAuthController;
use App\Http\Controllers\Mlibre\PublicacionesController;
use App\Http\Controllers\Sku\SkuVarianteController;
use App\Http\Controllers\Mlibre\MlSyncController;
require __DIR__.'/auth.php';

// Rutas públicas necesarias
Route::middleware(['auth'])->group(function () {

    Route::view('/', 'home');
    Route::view('/home', 'home')->name('home');
    Route::view('/profile/edit', 'Editar perfil (en construcción)')->name('profile.edit');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu');


 
Route::prefix('mlibre/sync')->name('mlibre.sync.')->group(function () {
    Route::post('/sync-filtrados', [MlSyncController::class, 'sincronizarFiltrados'])->name('sync-filtrados');
    Route::post('filtrados', [MlSyncController::class, 'syncFiltrados'])->name('filtrados');
    Route::post('seleccionados', [MlSyncController::class, 'syncSeleccionados'])->name('seleccionados');
    Route::post('pendientes', [MlSyncController::class, 'syncPendientes'])->name('pendientes');
});
Route::prefix('sku')->name('sku.')->group(function () {
    Route::get('sku_variantes', [SkuVarianteController::class, 'index'])->name('sku_variantes.index');
    Route::get('sku_variantes/{id}', [SkuVarianteController::class, 'show'])->name('sku_variantes.show');
    Route::get('sku_variantes/exportar', [SkuVarianteController::class, 'exportar'])->name('sku_variantes.exportar');
});
Route::post('mlibre/variantes/guardar-scf', [MlSyncController::class, 'guardarSCFs'])->name('mlibre.variantes.guardar-scf');

    Route::prefix('mlibre')->group(function () {
        Route::get('/auth', [MeliAuthController::class, 'redirect'])->name('mlibre.auth');
        Route::get('/callback', [MeliAuthController::class, 'callback'])->name('mlibre.callback');
        Route::get('/test-publicar-pow', [MlibreItemsController::class, 'testPowSkateb']);

     

        Route::prefix('variantes')->name('mlibre.variantes.')->group(function () {




            Route::get('/', [MlibreVariantesController::class, 'index'])->name('index');
          Route::post('sincronizar-filtrados', [MlibreVariantesController::class, 'sincronizarFiltrados'])->name('sincronizar-filtrados');
            Route::post('sync-seleccionados', [MlibreVariantesController::class, 'sincronizarSeleccionados'])->name('sync-seleccionados');
            Route::get('/exportar', [MlibreVariantesController::class, 'exportar'])->name('exportar');
            Route::post('/guardar-individual/{id}', [MlibreVariantesController::class, 'guardarIndividual'])->name('guardar_individual');
            Route::post('/{id}/sync', [MlibreVariantesController::class, 'syncIndividual'])->name('sync');
            Route::post('/{id}/publicar-scf', [MlibreVariantesController::class, 'publicarSCF'])->name('publicar_scf');
            Route::post('/guardar', [MlibreVariantesController::class, 'guardar'])->name('guardar');
            Route::get('/verificar-scf/{id}', [MlibreVariantesController::class, 'verificarSCF'])->name('verificar_scf');
        });

        Route::prefix('publicaciones')->group(function () {
            Route::get('/', [PublicacionesController::class, 'index'])->name('mlibre.publicaciones.index');
            Route::get('/{id}/edit', [PublicacionesController::class, 'edit'])->name('mlibre.publicaciones.edit');
            Route::put('/{id}', [PublicacionesController::class, 'update'])->name('mlibre.publicaciones.update');
            Route::post('/{ml_id}/sync', [MlibreVariantesController::class, 'syncPublicacion'])->name('mlibre.publicacion.sync');
        });

        Route::get('/publicar', [MlibreItemsController::class, 'formPublicar'])->name('mlibre.publicar');
        Route::post('/publicar', [MlibreItemsController::class, 'generarPublicaciones'])->name('mlibre.publicar.enviar');
    });

    Route::prefix('meli/items')->group(function () {
        Route::get('/', [MlibreItemsController::class, 'listar']);
        Route::get('{id}', [MlibreItemsController::class, 'ver']);
        Route::put('{id}/activar', [MlibreItemsController::class, 'activar']);
        Route::put('{id}/pausar', [MlibreItemsController::class, 'pausar']);
        Route::post('{id}/actualizar', [MlibreItemsController::class, 'actualizar']);
    });

    Route::prefix('produccion/abms')->name('produccion.abms.')->group(function () {
        Route::resources([
            'rutas_produccion' => RutasProduccionController::class,
            'curvas' => CurvaController::class,
            'secciones_produccion' => SeccionesProduccionController::class,
            'familias_producto' => FamiliasProductoController::class,
            'hormas' => HormaController::class,
            'marcas' => MarcaController::class,
            'colores_por_articulo' => ColoresPorArticuloController::class,
            'articulos' => ArticuloController::class,
            'pasos_rutas_produccion' => PasosRutasProduccionController::class,
            'almacenes' => AlmacenController::class,
            'articulo' => ArticuloController::class,
            'tipo_producto_stock' => TipoProductoStockController::class,
            'lineas_producto' => LineasProductoController::class,
        ]);

        Route::put('articulos_new/restaurar/{id}', [ArticulosNewController::class, 'restaurar'])->name('articulos_new.restaurar');
        Route::post('familias_producto/{id}/restaurar', [FamiliasProductoController::class, 'restaurar'])->name('familias_producto.restaurar');
        Route::post('hormas/{id}/restaurar', [HormaController::class, 'restaurar'])->name('hormas.restaurar');
        Route::post('marcas/{id}/restaurar', [MarcaController::class, 'restaurar'])->name('marcas.restaurar');
        Route::post('colores_por_articulo/{id}/restaurar', [ColoresPorArticuloController::class, 'restaurar'])->name('colores_por_articulo.restaurar');
        Route::post('articulos/{id}/restaurar', [ArticuloController::class, 'restaurar'])->name('articulos.restaurar');
        Route::post('pasos_rutas_produccion/{id}/restaurar', [PasosRutasProduccionController::class, 'restaurar'])->name('pasos_rutas_produccion.restaurar');
        Route::post('almacenes/{id}/restaurar', [AlmacenController::class, 'restaurar'])->name('almacenes.restaurar');
        Route::post('tipo_producto_stock/{id}/restaurar', [TipoProductoStockController::class, 'restaurar'])->name('tipo_producto_stock.restaurar');
        Route::post('lineas_producto/{id}/restaurar', [LineasProductoController::class, 'restaurar'])->name('lineas_producto.restaurar');
    });

    Route::resource('produccion/abms/articulos_new', ArticulosNewController::class)->names('produccion.abms.articulos_new');

    Route::get('products/{id}/with-colors', [ProductController::class, 'showWithColors'])->name('products.showWithColors');
    Route::put('products/{id}', fn() => redirect()->back()->with('status', 'Guardado de prueba.'))->name('products.update');

    Route::prefix('sistemas/abms')->group(function () {
        Route::get('/crear', [AbmCreatorController::class, 'index'])->name('sistemas.abms.crear');
        Route::match(['get', 'post'], '/preview/{modelo}', [AbmCreatorController::class, 'preview'])->name('sistemas.abms.preview');
        Route::post('/configurar', [AbmCreatorController::class, 'configurar'])->name('sistemas.abms.configurar');
        Route::post('/guardar-subformulario', [AbmCreatorController::class, 'guardarSubformulario'])->name('sistemas.abms.guardar_subformulario');
    });

    Route::prefix('sistemas/importar')->name('sistemas.importar.')->group(function () {
        Route::get('/form', [ImportarController::class, 'form'])->name('form');
        Route::post('/importar', [ImportarController::class, 'importar'])->name('importar');
    });
});

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: User - Generado el 2025-06-19 04:13:52
use App\Http\Controllers\Sistemas\UserController;

Route::prefix('sistemas/abms/user')->name('sistemas.abms.user.')->group(function () {
    Route::resource('', UserController::class)
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

    Route::post('{id}/restaurar', [UserController::class, 'restaurar'])->name('restaurar');
});