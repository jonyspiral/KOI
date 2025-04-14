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
    $modelos = [];

    // 📁 Buscar modelos dentro de los paths definidos
    foreach ([app_path('Models')] as $path) {
        if (!File::exists($path)) continue;

        foreach (File::allFiles($path) as $file) {
            $relativePath = $file->getRelativePathname();
            $class = "App\\Models\\" . str_replace(['/', '.php'], ['\\', ''], $relativePath);

            // 🧠 Validar que sea un modelo válido
            if (class_exists($class) && method_exists($class, 'fieldsMeta')) {
                $modelos[] = class_basename($class);
            }
        }
    }

    // 🧠 Prellenar campos si vienen en el request o sesión previa
    $modeloSeleccionado = $request->modelo;
    $namespaceSeleccionado = $request->namespace;
    $carpetaSeleccionada = $request->carpeta_vistas;
    $campos = [];

    // 🧪 Validar si se enviaron datos
    if ($modeloSeleccionado || $namespaceSeleccionado || $carpetaSeleccionada) {
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

    // 📦 Obtener campos desde el modelo si está definido
    if ($modeloSeleccionado && class_exists("App\\Models\\$modeloSeleccionado")) {
        $modelo = app("App\\Models\\$modeloSeleccionado");
        $campos = method_exists($modelo, 'fieldsMeta') ? $modelo->fieldsMeta() : [];
    }

    return view('sistemas.abms.index', compact(
        'modelos', 'modeloSeleccionado', 'namespaceSeleccionado', 'carpetaSeleccionada', 'campos'
    ));
}

public function redirectToPreview(Request $request)
{
    $request->validate([
        'modelo' => 'required|string',
        'namespace' => 'required|string',
        'carpeta_vistas' => 'required|string',
    ]);

    // 💾 Guardar valores iniciales en la sesión
    session([
        'abm.modelo' => $request->input('modelo'),
        'abm.namespace' => $request->input('namespace'),
        'abm.carpeta_vistas' => $request->input('carpeta_vistas'),
    ]);

    // 🚀 Redirigir a la vista preview
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
        // 🧩 Forzar inclusión del campo 'id' si no vino desde el JSON
        if (!isset($campos['id'])) {
            $campos['id'] = [
            'input_type' => 'text',
            'label_custom' => 'ID Laravel',
            'default' => null,
            'incluir' => true,
            'is_boolean' => false,
            'auto_increment_plus' => false,
            'referenced_table' => null,
            'referenced_label' => 'nombre',
            'referenced_column' => 'id',
        ];
        }

        
        if (!isset($json['primary_key'])) {
            return back()->withErrors("❌ El archivo JSON de configuración para el modelo {$modelo} no contiene la clave 'primary_key'. Revisá la configuración.");
        }

        $modeloClase = "App\\Models\\$modelo";
        $primaryKey = property_exists($modeloClase, 'primaryKey') ? (new $modeloClase)->getKeyName() : 'id';
        $primaryKeySql = property_exists($modeloClase, 'primaryKeySql') ? $modeloClase::$primaryKeySql : [$primaryKey];
       
        $namespace = session('abm.namespace') ?? $json['namespace'] ?? 'App';
        $carpeta_vistas = session('abm.carpeta_vistas') ?? $json['carpeta_vistas'] ?? '';

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

        $form_config = $json['form_config'] ?? [];

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
        $primaryKey = collect($fields)->filter(fn($f) => $f['primary'] ?? false)->keys()->first() ?? 'id';
        $primaryKeySql = collect($fields)->filter(fn($f) => $f['primary'] ?? false)->keys()->toArray();

        $campos = [];

        foreach ($fields as $campo => $tipo) {
            $input_type = match ($tipo) {
                'int', 'bigint', 'smallint' => 'number',
                'date', 'datetime' => 'date',
                'text', 'memo' => 'textarea',
                'boolean', 'bit', 'char(1)' => 'checkbox',
                default => 'text',
            };

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

        $form_config = array_merge([
            'form_name' => $modelo,
            'form_route' => strtolower("produccion/abms/" . Str::snake($modelo)),
            'tipo_formulario' => 'default',
            'usa_paginador' => true,
            'per_page' => 100,
            'form_view_type' => 'default',
            'index_view_type' => 'default',
        ], $json['form_config'] ?? []);
    }

    return view('sistemas.abms.preview', [
        'modelo' => $modelo,
        'fields' => $campos,
        'namespace' => $namespace,
        'carpeta_vistas' => $carpeta_vistas,
        'primary_key' => $primaryKey,
        'primary_key_sql' => $primaryKeySql,
        'form_config' => $form_config,
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
   // $routeName = strtolower("{$namespace}.abms." . basename($carpeta_vistas));
  
    $routeName = str_replace('/', '.', strtolower($carpeta_vistas));
   // dd($routeName);
   $formViewType = $config['form_config']['form_view_type'] ?? 'default';
   
    $replacements = [
        '__MODELO__' => $modelo,
        '__NOMBRE_RUTA__' => $routeName,
        '__NOMBRES__' => $nombres,
        '__NAMESPACE__' => $namespace,
        '__CARPETA_VISTAS__' => $carpeta_vistas,
        '__FORM_VIEW_TYPE__' => $config['form_config']['form_view_type'] ?? 'default', // ✅ agregado
    ];
    
    // 🧩 Generar controlador desde stub
    $controllerStub = file_get_contents(resource_path("stubs/abm/controller.stub.php"));
    $controllerContent = str_replace(array_keys($replacements), array_values($replacements), $controllerStub);
    file_put_contents($controllerPath, $controllerContent);

    // 🧾 Obtenemos la ruta desde el JSON para armar el nombre lógico
$formRoute = $config['form_config']['form_route'] ?? null;
$routeName = str_replace('/', '.', strtolower($formRoute));

// 🧩 Generar vistas desde stub
$this->generarVistasAbm($modelo, $namespace, $carpeta_vistas, $routeName, $config, $replacements);



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
        'ruta_logica' => $config['form_config']['form_route'] ?? 'N/D',
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

    // ✅ Obtener primaryKey de Laravel
    $primaryKey = property_exists($clase, 'primaryKey') ? (new $clase)->getKeyName() : 'id';

    // ✅ Obtener clave compuesta real del modelo (SQL Server)
    $clasesCandidatas = [
        "App\\Models\\{$modelo}",
        "App\\Models\\Sql\\{$modelo}",
    ];
    
    foreach ($clasesCandidatas as $clase) {
        if (class_exists($clase)) {
            if (property_exists($clase, 'primaryKeySql')) {
                $primaryKeySql = $clase::$primaryKeySql;
                break;
            }
        }
    }
    
    // Fallback si ninguna clase lo tiene
    $primaryKeySql ??= [$primaryKey];

    // ✅ Obtener todos los índices definidos (si existen)
    $allIndices = $metaFields['indices'] ?? [];

    $camposProcesados = [];

    foreach ($camposRaw as $campo => $meta) {
        $inputType = $meta['input_type'] ?? 'text';

        $campoProcesado = [
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

        // 🎯 Checkbox: valores personalizados desde select_list_data
        if ($inputType === 'checkbox' && !empty($meta['select_list_data'])) {
            $valores = explode(',', $meta['select_list_data']);
            if (count($valores) === 2) {
                [$labelCheck, $checkedValue] = array_pad(explode('=', trim($valores[0])), 2, '');
                [$labelUncheck, $uncheckedValue] = array_pad(explode('=', trim($valores[1])), 2, '');
                if ($checkedValue && $uncheckedValue) {
                    $campoProcesado['checkbox_checked_value'] = $checkedValue;
                    $campoProcesado['checkbox_unchecked_value'] = $uncheckedValue;
                }
            }
        }

        // ✅ Por defecto para checkboxes
        if ($inputType === 'checkbox') {
            $campoProcesado['checkbox_checked_value'] = $meta['checkbox_checked_value'] ?? 'S';
            $campoProcesado['checkbox_unchecked_value'] = $meta['checkbox_unchecked_value'] ?? 'N';
        }

        $camposProcesados[$campo] = $campoProcesado;
    }

    // 🧩 Validar subformularios
    foreach ($subformularios as $nombre => &$subform) {
        if (empty($subform['carpeta_vistas'])) {
            throw new \Exception("Falta definir 'carpeta_vistas' en el subformulario de {$nombre}");
        }
        $subform['ruta'] = strtolower(basename($subform['carpeta_vistas']));
    }

    // 👁️ Agregar campo 'id' si no es primary key pero se usa como clave en Laravel
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
    $formConfig = $data['form_config'] ?? [];

    // Asignar valores por defecto si no fueron enviados
    $formConfig = array_merge([
        'form_name' => $formConfig['form_name'] ?? $modelo,
        'form_route' => $formConfig['form_route'] ?? strtolower("produccion/abms/" . Str::snake($modelo)),
        'tipo_formulario' => $formConfig['tipo_formulario'] ?? 'default',
        'usa_paginador' => $formConfig['usa_paginador'] ?? true,
        'per_page' => $formConfig['per_page'] ?? 100,
        'form_view_type' => $formConfig['form_view_type'] ?? 'default',
        'index_view_type' => $formConfig['index_view_type'] ?? 'default',
    ], $formConfig);
    


    return [
        'modelo' => $data['modelo'],
        'namespace' => $data['namespace'],
        'carpeta_vistas' => $data['carpeta_vistas'],
        'timestamps' => $data['timestamps'] ?? false,
        'sincronizable' => $data['sincronizable'] ?? true,
        'force_controlador' => true,
        'primary_key' => $primaryKey,
        'primary_key_sql' => $primaryKeySql,
        'indices' => $allIndices,
        'campos' => $camposProcesados,
        'form_config' => $formConfig,
        'subformularios' => $subformularios,
        
    ];
}

/**
 * 🧩 Genera automáticamente las vistas blade para un ABM
 *
 * @param string $modelo            Nombre del modelo (ej: Marca)
 * @param string $namespace         Namespace Laravel (ej: Produccion)
 * @param string $carpeta_vistas    Carpeta donde se almacenarán las vistas (ej: produccion/abms/marcas)
 * @param string $routeName         Nombre de la ruta (ej: produccion.abms.marcas)
 * @param array  $config            Configuración completa del formulario, incluyendo form_config
 */
    protected function generarVistasAbm(string $modelo, string $namespace, string $carpeta_vistas, string $routeName, array $config, array $replacements): void
    {
        $viewsPath = resource_path("views/{$carpeta_vistas}");

        // 🧱 Crear la carpeta si no existe
        if (!file_exists($viewsPath)) {
            mkdir($viewsPath, 0755, true);
        }

        // 🔁 Variables para reemplazar en los stubs
        $nombres = Str::snake(Str::pluralStudly($modelo));

        $formViewType = $config['form_config']['form_view_type'] ?? 'default';
        $indexViewType = $config['form_config']['index_view_type'] ?? 'default';

        foreach (['index', 'create', 'edit', 'show'] as $vista) {
         
            if ($vista === 'index') { 
                // 🔍 Si hay subformularios inline, usamos el stub adecuado
                $subformularios = $config['subformularios'] ?? [];
              
                $tieneSubformInline = collect($subformularios)->contains(fn($s) => ($s['modo'] ?? null) === 'inline');

                $stubFilename = match ($indexViewType) {
                    'inline' => 'index-inline.stub.blade.php',
                    'tab' => 'index-tab.stub.blade.php',
                    default => $tieneSubformInline ? 'index-inline.stub.blade.php' : 'index.stub.blade.php',
                };

           
            
            }
            elseif ($vista === 'create' || $vista === 'edit') {
                
                // 🧩 Stub específico para create/edit según form_view_type
                $stubFilename = match ($formViewType) {
                    'modal' => "{$vista}-modal.stub.blade.php",
                    default => "{$vista}.stub.blade.php",
                };
            } else {
                $stubFilename = "{$vista}.stub.blade.php";
            }

            $stubPath = resource_path("stubs/abm/{$stubFilename}");

            if (file_exists($stubPath)) {
                $contenido = str_replace(array_keys($replacements), array_values($replacements), file_get_contents($stubPath));
                file_put_contents("{$viewsPath}/{$vista}.blade.php", $contenido);
            }

            if ($formViewType === 'modal') {
                $modalStubPath = resource_path("stubs/abm/create-modal.stub.blade.php");
                if (file_exists($modalStubPath)) {
                    $contenido = str_replace(array_keys($replacements), array_values($replacements), file_get_contents($modalStubPath));
                    file_put_contents("{$viewsPath}/create-modal.blade.php", $contenido);
                }
            }
            
        }

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
  
protected function agregarRutaWeb(string $modelo, string $carpeta_vistas, string $namespace): void
{
    $rutaArchivo = base_path('routes/web.php');
    $contenidoActual = file_get_contents($rutaArchivo);
    $fecha = now()->format('Y-m-d H:i:s');

    // ✅ Leer JSON del modelo
    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");

    if (!File::exists($jsonPath)) {
        throw new \Exception("No se encontró el archivo de configuración JSON para {$modelo}");
    }

    $config = json_decode(File::get($jsonPath), true);
    $formRoute = $config['form_config']['form_route'] ?? null;

    // 🛑 Validación estricta: ruta lógica obligatoria
    if (empty($formRoute)) {
        throw new \Exception("La ruta lógica ('form_route') no fue definida en la configuración de {$modelo}");
    }

    // 🧠 Derivar componentes de la ruta
    $prefix = $formRoute; // ej: produccion/abms/marcas
    $modeloSnake = basename($formRoute); // ej: marcas
    $nombreGrupo = str_replace('/', '.', $prefix); // ej: produccion.abms.marcas

    // 📦 Controlador
    $controladorCompleto = "App\\Http\\Controllers\\{$namespace}\\{$modelo}Controller";
    $uso = "use {$controladorCompleto};";
    $usoFinal = Str::contains($contenidoActual, $uso) ? '' : $uso;

    // 🧩 Bloque de ruta
    $bloqueRuta = <<<PHP

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: {$modelo} - Generado el {$fecha}
{$usoFinal}

Route::prefix('{$prefix}')->name('{$nombreGrupo}.')->group(function () {
    Route::resource('{$modeloSnake}', {$modelo}Controller::class)->names('{$modeloSnake}');
});

PHP;

    // 🔍 Evitar duplicados antes de agregar
    if (!Str::contains($contenidoActual, "Route::resource('{$modeloSnake}'")) {
        file_put_contents($rutaArchivo, $bloqueRuta, FILE_APPEND);
        \Log::info("✅ Ruta agregada al web.php para {$modelo}");
    } else {
        \Log::info("ℹ️ La ruta para {$modelo} ya existía y no fue duplicada.");
    }
}


}
