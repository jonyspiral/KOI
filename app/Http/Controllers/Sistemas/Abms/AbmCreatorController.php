<?php

namespace App\Http\Controllers\Sistemas\Abms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class AbmCreatorController extends Controller
{
    /**
 * ABM Creator Controller
 * Version: 1.1.0
 * Última actualización: 2025-03-31
 * Cambios:
 * - Filtrado de modelos para mostrar solo los que usan conexión MySQL en el método index()
 */

public function index()
{
    // Obtener modelos desde app/Models (solo los que usan conexión MySQL)
    $carpetaModelos = app_path('Models');
    $modelos = [];

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($carpetaModelos)
    );

    foreach ($iterator as $archivo) {
        if ($archivo->isFile() && $archivo->getExtension() === 'php') {
            $rutaRelativa = str_replace($carpetaModelos . DIRECTORY_SEPARATOR, '', $archivo->getPathname());
            $sinExtension = str_replace('.php', '', $rutaRelativa);
            $modelo = str_replace(DIRECTORY_SEPARATOR, '\\', $sinExtension);
            $modeloClass = "App\\Models\\$modelo";

            if (class_exists($modeloClass)) {
                $instancia = new $modeloClass;
                $conexion = $instancia->getConnectionName() ?? config('database.default');
                if ($conexion === 'mysql') {
                    $modelos[] = $modelo;
                }
            }
        }
    }

    // Carpetas disponibles para vistas y controladores
    $carpetasControlador = collect(File::directories(app_path('Http/Controllers')))
        ->map(function ($dir) {
            return trim(str_replace(app_path('Http/Controllers'), '', $dir), '/');
        })
        ->filter()
        ->values()
        ->all();

    $carpetasVistas = collect(File::directories(resource_path('views')))
        ->map(function ($dir) {
            return trim(str_replace(resource_path('views'), '', $dir), '/');
        })
        ->filter()
        ->values()
        ->all();

    return view('sistemas.abms.index', compact('modelos', 'carpetasControlador', 'carpetasVistas'));
}



public function redirectToPreview(Request $request)
{
    $request->validate([
        'modelo' => 'required|string',
        'namespace' => 'required|string',
        'carpeta_vistas' => 'required|string',
    ]);

    // Guardamos temporalmente en la sesión la configuración inicial
    session([
        'abm.modelo' => $request->input('modelo'),
        'abm.namespace' => $request->input('namespace'),
        'abm.carpeta_vistas' => $request->input('carpeta_vistas'),
    ]);

    // Redirigimos a la vista preview del modelo
    return redirect()->route('sistemas.abms.preview', [
        'modelo' => $request->input('modelo')
    ]);
}

public function preview($modelo)
{
    $clase = "App\\Models\\" . ucfirst(Str::camel($modelo));

    if (!class_exists($clase)) {
        return redirect()->route('sistemas.abms.crear')
            ->withErrors("El modelo {$modelo} no existe.");
    }

    if (!method_exists($clase, 'fieldsMeta')) {
        return redirect()->route('sistemas.abms.crear')
            ->withErrors("El modelo {$modelo} no tiene definido el método fieldsMeta().");
    }

    // Obtenemos los campos definidos en el modelo
    $fields = $clase::fieldsMeta();

    return view('sistemas.abms.preview', [
        'modelo' => $modelo,
        'fields' => $fields,
        'namespace' => session('abm.namespace'),
        'carpeta_vistas' => session('abm.carpeta_vistas'),
    ]);
}
public function configurar(Request $request)
{
    $modelo = $request->input('modelo');
    $namespace = $request->input('namespace');
    $carpetaVistas = $request->input('carpeta_vistas');
    $campos = $request->input('campos');
    $force = $request->filled('force_controlador');

    $modelClass = "App\\Models\\{$modelo}";
    $controllerName = "{$modelo}Controller";
    $controllerNamespace = "App\\Http\\Controllers\\{$namespace}";
    $controllerPath = app_path("Http/Controllers/{$namespace}/{$controllerName}.php");
    $viewsPath = resource_path("views/{$carpetaVistas}");

    // Crear carpetas si no existen
    if (!file_exists(dirname($controllerPath))) {
        mkdir(dirname($controllerPath), 0755, true);
    }
    if (!file_exists($viewsPath)) {
        mkdir($viewsPath, 0755, true);
    }
   
    if (file_exists($controllerPath) && !$force) {
        return back()->withErrors("El controlador ya existe. Activá 'Reemplazar controlador' para sobreescribirlo.");
    }
   logger("✅ Pasa la validación y continúa el proceso...");
    
       // 🧠 Procesar campos antes de guardar el JSON
       $camposProcesados = [];
       $camposIncluir = [];
   
       foreach ($campos as $campo => $meta) {
        $configCampo = [
            'label' => $meta['label'] ?? ucwords(str_replace('_', ' ', $campo)),
            'default' => $meta['default'] ?? null,
            'visible' => !empty($meta['visible']),
            'input_type' => $meta['input_type'] ?? 'text',
            'is_boolean' => !empty($meta['is_boolean']),
            'auto_increment_plus' => !empty($meta['auto_increment_plus']),
            'foreign' => !empty($meta['foreign']),
            'referenced_table' => $meta['referenced_table'] ?? null,
            'referenced_column' => $meta['referenced_column'] ?? 'id',
            'referenced_label' => $meta['referenced_label'] ?? 'nombre',
            'incluir' => !empty($meta['incluir']),
        ];
   
           if ($configCampo['incluir']) {
               $camposIncluir[] = $campo;
           }
   
           $camposProcesados[$campo] = $configCampo;
       }
     // 🧾 Guardar configuración extendida en JSON
     $config = [
        'modelo' => $modelo,
        'namespace' => $namespace,
        'carpeta_vistas' => $carpetaVistas,
        'timestamps' => $request->has('timestamps'),
        'sincronizable' => $request->has('sincronizable'),
        'campos' => $camposProcesados,
        
    ]; 

    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");
    if (!file_exists(dirname($jsonPath))) {
        mkdir(dirname($jsonPath), 0755, true);
    }
    file_put_contents($jsonPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
 
    // ✅ ACTUALIZAR EL FILLABLE EN EL MODELO
    $camposIncluir = [];
    foreach ($campos as $campo => $conf) {
        if (!empty($conf['incluir'])) {
            $camposIncluir[] = $campo;
        }
    }
    $this->actualizarFillableModelo($modelo, $namespace, $camposIncluir);

    // Reemplazos comunes
    $nombres = Str::snake(Str::pluralStudly($modelo)); // ej: rutas_produccion
    $snakeModel = Str::snake($modelo); // rutas_produccion
    $routeName = strtolower("{$namespace}.abms." . Str::plural($snakeModel));
    
    $replacements = [
        '__MODELO__' => $modelo,
        '__NOMBRE_RUTA__' => $routeName, // <- ya viene bien
        '__NOMBRES__' => $nombres,
        '__NAMESPACE__' => $namespace,
        '__CARPETA_VISTAS__' => $carpetaVistas,
    ];

    // Cargar y renderizar controller.stub.php
    $controllerStub = file_get_contents(resource_path("stubs/abm/controller.stub.php"));
    $controllerContent = str_replace(array_keys($replacements), array_values($replacements), $controllerStub);
    file_put_contents($controllerPath, $controllerContent);

    // Cargar y renderizar vistas
    $vistas = ['index', 'create', 'edit'];
    foreach ($vistas as $vista) {
        $stubPath = resource_path("stubs/abm/{$vista}.stub.blade.php");
        if (file_exists($stubPath)) {
            $contenido = str_replace(array_keys($replacements), array_values($replacements), file_get_contents($stubPath));
            file_put_contents("{$viewsPath}/{$vista}.blade.php", $contenido);
        }
    }
    // Si se pidió generar el Request (validación)
    if ($request->filled('generar_request')) {
        $requestPath = app_path("Http/Requests/{$modelo}Request.php");
        if (!file_exists(dirname($requestPath))) {
            mkdir(dirname($requestPath), 0755, true);
        }
    
        $requestStub = file_get_contents(resource_path("stubs/abm/request.stub.php"));
        $requestContent = str_replace(array_keys($replacements), array_values($replacements), $requestStub);
        file_put_contents($requestPath, $requestContent);
        $this->info("🛡️ FormRequest generado: {$modelo}Request.php");
    }

        // Agregar ruta automáticamente al web.php
        if ($request->filled('agregar_ruta')) {
            $this->agregarRutaWeb($namespace, $modelo, $carpetaVistas);
        }
        return view('sistemas.abms.resultado', [
            'modelo' => $modelo,
            'carpeta_vistas' => $carpetaVistas,
            'controller_path' => $controllerPath,
            'request_path' => $request->filled('generar_request') ? $requestPath ?? null : null,
        ]);
}

protected function actualizarFillableModelo($modelo, $namespace, array $camposIncluir)
{
    $rutaModelo = app_path("Models/{$modelo}.php");

    if (!File::exists($rutaModelo)) {
        throw new \Exception("No se encontró el modelo {$modelo}");
    }

    $contenido = File::get($rutaModelo);

    // Construir el array $fillable
    $fillableArray = "['" . implode("', '", $camposIncluir) . "']";
    $lineaFillable = "    protected \$fillable = {$fillableArray};";

    // Reemplazar o insertar el fillable
    if (preg_match('/protected \$fillable = \[.*?\];/s', $contenido)) {
        $contenido = preg_replace('/protected \$fillable = \[.*?\];/s', $lineaFillable, $contenido);
    } else {
        // Insertar después de la declaración de clase
        $contenido = preg_replace(
            '/class ' . $modelo . ' extends Model\s*\{/',
            "class {$modelo} extends Model {\n{$lineaFillable}\n",
            $contenido
        );
    }

    File::put($rutaModelo, $contenido);
    \Log::info("🛠 Modelo {$modelo} actualizado con \$fillable: " . json_encode($camposIncluir));
}
  
protected function agregarRutaWeb($namespace, $modelo, $carpetaVistas)
{
    $rutaArchivo = base_path('routes/web.php');
    $contenidoActual = file_get_contents($rutaArchivo);

    $fecha = now()->format('Y-m-d H:i:s');

    // Convertimos modelo a plural (para nombres de rutas)
    $modeloPlural = Str::pluralStudly($modelo);   // ej: FamiliasProductos
    $modeloSnake = Str::snake($modeloPlural);     // ej: familias_productos

    // Derivamos prefijo y nombre del grupo desde la carpeta de vistas
    $partes = explode('/', $carpetaVistas);
    $prefijo = implode('/', array_slice($partes, 0, 2));     // ej: produccion/abms
    $nombreGrupo = implode('.', array_slice($partes, 0, 2)); // ej: produccion.abms

    $controladorCompleto = "App\\Http\\Controllers\\{$namespace}\\{$modelo}Controller";
    $uso = "use {$controladorCompleto};";

    // ✅ Solo agregamos el use si no existe
    $usoFinal = Str::contains($contenidoActual, $uso) ? '' : $uso;

    // ✅ Generamos el bloque de ruta
    $bloqueRuta = <<<PHP

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: {$modelo} - Generado el {$fecha}
{$usoFinal}

Route::prefix('{$prefijo}')->name('{$nombreGrupo}.')->group(function () {
    Route::resource('{$modeloSnake}', {$modelo}Controller::class)->names('{$modeloSnake}');
});

PHP;

    // 🔍 Evitamos duplicado del resource
    if (!Str::contains($contenidoActual, "Route::resource('{$modeloSnake}'")) {
        file_put_contents($rutaArchivo, $bloqueRuta, FILE_APPEND);
        Log::info("✅ Ruta agregada al web.php para {$modelo}");
    } else {
        Log::info("ℹ️ La ruta para {$modelo} ya existe, no se duplicó.");
    }
}

}
