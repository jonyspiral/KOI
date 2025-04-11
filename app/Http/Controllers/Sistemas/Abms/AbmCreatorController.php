<?php

namespace App\Http\Controllers\Sistemas\Abms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Helpers\SubformManager;

class AbmCreatorController extends Controller
{
    /**
 * ABM Creator Controller
 * Version: 1.1.0
 * Última actualización: 2025-03-31
 * Cambios:
 * - Filtrado de modelos para mostrar solo los que usan conexión MySQL en el método index()
 */

// Método principal que renderiza el formulario inicial del ABM Creator
public function index(Request $request)
{
    // Array para almacenar los modelos detectados
    $modelos = [];

    // Directorios a escanear para buscar modelos
    $paths = [
        app_path('Models'),
        //app_path('Models/Sql'),
    ];

    // Buscar archivos PHP dentro de los paths configurados
    foreach ($paths as $path) {
        if (!File::exists($path)) continue;

        foreach (File::allFiles($path) as $file) {
            $relativePath = $file->getRelativePathname();
            $class = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $class = "App\\Models" . ($path === app_path('Models/Sql') ? "\\Sql" : "") . "\\$class";

            // Verifica que la clase exista y tenga el método fieldsMeta
            if (class_exists($class) && method_exists($class, 'fieldsMeta')) {
                $modelos[] = class_basename($class);
            }
        }
    }

    // Captura los valores ingresados en el formulario (si existen)
    $modeloSeleccionado = $request->modelo ?? null;
    $namespaceSeleccionado = $request->namespace ?? null;
    $carpetaSeleccionada = $request->carpeta_vistas ?? null;
    $campos = [];

    // Si se enviaron datos (POST o datos precargados), validar los campos
    if ($request->isMethod('post') || $modeloSeleccionado || $namespaceSeleccionado || $carpetaSeleccionada) {
        $request->validate([
            'modelo' => 'required|string',
            'namespace' => 'required|string',
            'carpeta_vistas' => 'required|string',
        ], [
            'modelo.required' => 'Debés seleccionar un modelo.',
            'namespace.required' => 'Debés completar el namespace.',
            'carpeta_vistas.required' => 'Debés completar la carpeta de vistas.',
        ]);
    }

    // Si se seleccionó un modelo, obtener sus campos usando fieldsMeta
    if ($modeloSeleccionado) {
        $modeloClase = "App\\Models\\$modeloSeleccionado";

        if (class_exists($modeloClase)) {
            $modelo = new $modeloClase;
            if (method_exists($modelo, 'fieldsMeta')) {
                $campos = $modelo->fieldsMeta();
            }
        }
    }

    // Renderiza la vista con los datos necesarios
    return view('sistemas.abms.index', compact('modelos', 'modeloSeleccionado', 'namespaceSeleccionado', 'carpetaSeleccionada', 'campos'));
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
    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");

    if (File::exists($jsonPath)) {
        // ✅ Si ya existe el JSON, usarlo como fuente principal
        $json = json_decode(File::get($jsonPath), true);

        $campos = $json['campos'] ?? [];
         if (!isset($json['primary_key'])) {
        return back()->withErrors("❌ El archivo JSON de configuración para el modelo {$modelo} no contiene la clave 'primary_key'. Revisá la configuración.");
    }

    $primaryKey = $json['primary_key'];
        $namespace = $json['namespace'] ?? session('abm.namespace');
        $carpeta_vistas = $json['carpeta_vistas'] ?? session('abm.carpeta_vistas');

        // Completar claves faltantes si alguna quedó sin guardar
        foreach ($campos as $campo => &$meta) {
            $meta['input_type'] = $meta['input_type'] ?? 'text';
            $meta['label_custom'] = $meta['label_custom'] ?? ucwords(str_replace('_', ' ', $campo));
            $meta['default'] = $meta['default'] ?? null;
            $meta['incluir'] = $meta['incluir'] ?? true;
            $meta['is_boolean'] = $meta['is_boolean'] ?? false;
            $meta['auto_increment_plus'] = $meta['auto_increment_plus'] ?? false;
            $meta['referenced_table'] = $meta['referenced_table'] ?? null;
            $meta['referenced_label'] = $meta['referenced_label'] ?? 'nombre';
            $meta['referenced_column'] = $meta['referenced_column'] ?? 'id';
        }

    } else {
        // 🧠 Si no hay config previa, usar el método fieldsMeta del modelo
        $clase = "App\\Models\\" . ucfirst(Str::camel($modelo));

        if (!class_exists($clase)) {
            return redirect()->route('sistemas.abms.crear')->withErrors("El modelo {$modelo} no existe.");
        }

        if (!method_exists($clase, 'fieldsMeta')) {
            return redirect()->route('sistemas.abms.crear')->withErrors("El modelo {$modelo} no tiene definido el método fieldsMeta().");
        }

        $fields = $clase::fieldsMeta();
        $namespace = session('abm.namespace');
        $carpeta_vistas = session('abm.carpeta_vistas');
        $primaryKey = collect($fields)
        ->filter(fn($f) => $f['primary'] ?? false)
        ->keys()
        ->first() ?? 'id';
    
        $tiposInputValidos = [
            'text', 'number', 'date', 'checkbox', 'textarea', 'select', 'select_list',
            'hidden', 'email', 'password', 'file', 'color', 'url', 'tel', 'autonumerico'
        ];

        $campos = [];

        foreach ($fields as $campo => $tipo) {
            $input_type = match ($tipo) {
                'int', 'bigint', 'smallint' => 'number',
                'date', 'datetime' => 'date',
                'text', 'memo' => 'textarea',
                'boolean', 'bit', 'char(1)' => 'checkbox',
                default => 'text',
            };

            if (!in_array($input_type, $tiposInputValidos)) {
                $input_type = 'text';
            }

            $campos[$campo] = [
                'input_type' => $input_type,
                'label_custom' => ucwords(str_replace('_', ' ', $campo)),
                'default' => null,
                'incluir' => true,
                'is_boolean' => $input_type === 'checkbox',
                'auto_increment_plus' => false,
                'referenced_table' => null,
                'referenced_label' => 'nombre',
                'referenced_column' => 'id',
            ];
        }
    }

    return view('sistemas.abms.preview', [
        'modelo' => $modelo,
        'fields' => $campos,
        'namespace' => $namespace,
        'carpeta_vistas' => $carpeta_vistas,
        'primary_key' => $primaryKey,
    ]);
}


public function configurar(Request $request)
{
    $modelo = $request->input('modelo');
    $namespace = $request->input('namespace');
    $carpeta_vistas = $request->input('carpeta_vistas');
    $force = $request->filled('force_controlador');

    $controllerName = "{$modelo}Controller";
    $controllerPath = app_path("Http/Controllers/{$namespace}/{$controllerName}.php");
    $viewsPath = resource_path("views/{$carpeta_vistas}");

    // 🧱 Crear carpetas necesarias si no existen
    if (!file_exists(dirname($controllerPath))) {
        mkdir(dirname($controllerPath), 0755, true);
    }
    if (!file_exists($viewsPath)) {
        mkdir($viewsPath, 0755, true);
    }

    // ⛔ Evitar sobrescritura accidental
    if (file_exists($controllerPath) && !$force) {
        return back()->withErrors("El controlador ya existe. Activá 'Reemplazar controlador' para sobreescribirlo.");
    }

    // 🧠 Armar estructura completa del JSON de configuración
    $config = $this->generarJsonAbm($request->all());

    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");
    if (!file_exists(dirname($jsonPath))) {
        mkdir(dirname($jsonPath), 0755, true);
    }
    file_put_contents($jsonPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    logger('📄 JSON generado:', $config);

    // 🛠️ Actualizar fillable en el modelo
    $camposIncluir = array_keys(array_filter($config['campos'], fn($c) => !empty($c['incluir'])));
    $this->actualizarFillableModelo($modelo, $namespace, $camposIncluir);

    // 🔁 Variables comunes para stubs
    $nombres = Str::snake(Str::pluralStudly($modelo));
    $snakeModel = Str::snake($modelo);
    $routeName = strtolower("{$namespace}.abms." . basename($carpeta_vistas));

    $replacements = [
        '__MODELO__' => $modelo,
        '__NOMBRE_RUTA__' => $routeName,
        '__NOMBRES__' => $nombres,
        '__NAMESPACE__' => $namespace,
        '__CARPETA_VISTAS__' => $carpeta_vistas,
    ];

    // 🧩 Generar controlador desde stub
    $controllerStub = file_get_contents(resource_path("stubs/abm/controller.stub.php"));
    $controllerContent = str_replace(array_keys($replacements), array_values($replacements), $controllerStub);
    file_put_contents($controllerPath, $controllerContent);

    // 🧩 Generar vistas desde stubs
    foreach (['index', 'create', 'edit'] as $vista) {
        if ($vista === 'index') {
            // 🔍 Detectar si algún subform es inline
            $jsonConfigPath = resource_path("meta_abms/config_form_{$modelo}.json");
            $jsonConfig = File::exists($jsonConfigPath) ? json_decode(File::get($jsonConfigPath), true) : [];
            $subformularios = $jsonConfig['subformularios'] ?? [];
    
            $tieneSubformInline = collect($subformularios)->contains(fn($s) => ($s['modo'] ?? null) === 'inline');
    
            // 🔁 Elegir el stub según el modo
            $stubFilename = $tieneSubformInline
                ? 'index-inline.stub.blade.php'        // ✅ VISTA INLINE
                : 'index.stub.blade.php'; // 🧱 VISTA TRADICIONAL
    
        } else {
            $stubFilename = "{$vista}.stub.blade.php";
        }
    
        $stubPath = resource_path("stubs/abm/{$stubFilename}");
    
        if (file_exists($stubPath)) {
            $contenido = str_replace(array_keys($replacements), array_values($replacements), file_get_contents($stubPath));
            file_put_contents("{$viewsPath}/{$vista}.blade.php", $contenido);
        }
    }

    // 🛡️ Opcional: generar FormRequest si se indicó
    if ($request->filled('generar_request')) {
        $requestPath = app_path("Http/Requests/{$modelo}Request.php");
        if (!file_exists(dirname($requestPath))) {
            mkdir(dirname($requestPath), 0755, true);
        }
        $requestStub = file_get_contents(resource_path("stubs/abm/request.stub.php"));
        $requestContent = str_replace(array_keys($replacements), array_values($replacements), $requestStub);
        file_put_contents($requestPath, $requestContent);
    }

    // 🧭 Registrar ruta automáticamente en web.php
    $this->agregarRutaWeb($modelo, $carpeta_vistas, $namespace);

    return view('sistemas.abms.resultado', [
        'modelo' => $modelo,
        'carpeta_vistas' => $carpeta_vistas,
        'controller_path' => $controllerPath,
        'request_path' => $request->filled('generar_request') ? $requestPath ?? null : null,
    ]);
}

// 📦 Nuevo método centralizado para armar el JSON del ABM
private function generarJsonAbm(array $data): array
{
    $camposRaw = $data['campos'] ?? [];
    $subformularios = $data['subformularios'] ?? [];
    $modelo = $data['modelo'];
    $clase = "App\\Models\\{$modelo}";

    if (!class_exists($clase) || !method_exists($clase, 'fieldsMeta')) {
        throw new \Exception("El modelo {$modelo} no tiene fieldsMeta()");
    }

    $metaFields = $clase::fieldsMeta();

    $primaryKey = collect($metaFields)
        ->filter(fn($m) => $m['primary'] ?? false)
        ->keys()
        ->first() ?? 'id';

    $camposProcesados = [];

    foreach ($camposRaw as $campo => $meta) {
        $inputType = $meta['input_type'] ?? 'text';

        $camposProcesados[$campo] = [
            'label' => $meta['label'] ?? ucwords(str_replace('_', ' ', $campo)),
            'default' => $meta['default'] ?? null,
            'input_type' => $inputType,
            'incluir' => !empty($meta['incluir']) || $campo === $primaryKey,
            'nullable' => !empty($meta['nullable']),
            'select_list_data' => $meta['select_list_data'] ?? null,
            'referenced_table' => $meta['referenced_table'] ?? null,
            'referenced_column' => $meta['referenced_column'] ?? 'id',
            'referenced_label' => $meta['referenced_label'] ?? 'nombre',
            'is_boolean' => $inputType === 'checkbox',
            'auto_increment_plus' => $inputType === 'autonumerico',
        ];
    }

    // ⚠️ Validar y completar subformularios
    foreach ($subformularios as $nombre => &$subform) {
        if (empty($subform['carpeta_vistas'])) {
            throw new \Exception("Falta definir 'carpeta_vistas' en el subformulario de {$nombre}");
        }

        $subform['ruta'] = strtolower(basename($subform['carpeta_vistas']));
    }

    // En caso que la clave no sea "id", agregamos el campo "id" como visible
    if ($primaryKey !== 'id' && !isset($camposProcesados['id'])) {
        $camposProcesados['id'] = [
            'label' => 'ID Laravel',
            'default' => null,
            'input_type' => 'text',
            'incluir' => true,
            'nullable' => true,
            'select_list_data' => null,
            'referenced_table' => null,
            'referenced_column' => null,
            'referenced_label' => null,
            'is_boolean' => false,
            'auto_increment_plus' => false,
        ];
    }

    return [
        'modelo' => $data['modelo'],
        'namespace' => $data['namespace'],
        'carpeta_vistas' => $data['carpeta_vistas'],
        'timestamps' => $data['timestamps'] ?? false,
        'sincronizable' => $data['sincronizable'] ?? true,
        'force_controlador' => true,
        'primary_key' => $primaryKey,
        'campos' => $camposProcesados,
        'subformularios' => $subformularios,
    ];
}

protected function actualizarChangelogAbm(string $modelo, string $namespace): void
{
    $fecha = now()->format('Y-m-d');
    $version = '1.1'; // podés hacerlo dinámico si querés en el futuro

    $lineas = [
        '',
        "---",
        "## 🧩 Versión {$version} - {$fecha}",
        '',
        "🔧 **ABM generado para `{$modelo}`**",
        '',
        "- Controlador: `App\\Http\\Controllers\\{$namespace}\\{$modelo}Controller`",
        "- Vistas: `resources/views/{$namespace}/abms/`",
        "- JSON: `config_form_{$modelo}.json`",
        '',
        "🧠 Campos: configurados dinámicamente por usuario.",
        "📂 Subformularios: importados si existen en el JSON.",
        "✅ Generación automática de controlador, vistas y rutas.",
        ''
    ];

    $ruta = resource_path('stubs/abm/CHANGELOG_ABM_CREATOR.md');
    if (!file_exists($ruta)) {
        file_put_contents($ruta, "# 📦 ABM Creator - Registro de Cambios\n\n");
    }

    // Agregar al final
    file_put_contents($ruta, implode("\n", $lineas), FILE_APPEND);
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
  
protected function agregarRutaWeb($modelo, $carpeta_vistas, $namespace)
{//dd ($carpeta_vistas);
  //  dd ($modelo);
  

    $rutaArchivo = base_path('routes/web.php');
    $contenidoActual = file_get_contents($rutaArchivo);

    $fecha = now()->format('Y-m-d H:i:s');

    // Convertimos modelo a plural (para nombres de rutas)
    $modeloPlural = Str::pluralStudly($modelo);   // ej: FamiliasProductos
    $modeloSnake = basename($carpeta_vistas); // exactamente lo que pones en el inout de carpeta vistas del index del abm creator.

    // Derivamos prefijo y nombre del grupo desde la carpeta de vistas
    $partes = explode('/', $carpeta_vistas);
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
