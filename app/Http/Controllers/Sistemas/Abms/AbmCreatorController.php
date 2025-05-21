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

    // 🔍 Buscar modelos válidos (sin subcarpeta Sql/)
    foreach ([app_path('Models')] as $path) {
        if (!File::exists($path)) continue;

        foreach (File::allFiles($path) as $file) {
            $relativePath = $file->getRelativePathname();
            if (Str::startsWith($relativePath, 'Sql/')) continue;

            $class = "App\\Models\\" . str_replace(['/', '.php'], ['\\', ''], $relativePath);
            if (class_exists($class) && method_exists($class, 'fieldsMeta')) {
                $modelos[] = class_basename($class);
            }
        }
    }
  
    $modeloSeleccionado = $request->modelo;
    $namespaceSeleccionado = $request->namespace;
    $carpetaSeleccionada = $request->carpeta_vistas;
    $configJson = [];

    if ($modeloSeleccionado) {
        
        $jsonPath = resource_path("meta_abms/config_form_{$modeloSeleccionado}.json");

        if (File::exists($jsonPath)) {
            // ✅ JSON existente → cargarlo
            $configJson = json_decode(File::get($jsonPath), true);
            $namespaceSeleccionado = $configJson['namespace'] ?? $namespaceSeleccionado;
            $carpetaSeleccionada = $configJson['carpeta_vistas'] ?? $carpetaSeleccionada;

        } else {
            // 🧠 Generar config mínima con datos del modelo
            $claseModelo = "App\\Models\\{$modeloSeleccionado}";

            if (class_exists($claseModelo) && method_exists($claseModelo, 'fieldsMeta')) {
                $instancia = new $claseModelo;
                    // ✅ Obtener metadata completa del modelo
                    $fieldsMeta = $claseModelo::fieldsMeta();

                    $camposIniciales = [];
                    foreach ($fieldsMeta as $campo => $meta) {
                        $camposIniciales[$campo] = [
                            'label' => ucfirst(str_replace('_', ' ', $campo)),
                            'input_type' => $meta['input_type'] ?? 'text',
                            'incluir' => true,
                            'nullable' => $meta['nullable'] ?? false,
                            'readonly' => $meta['readonly'] ?? false,
                            'orden' => 0,
                            'sync' => true,
                        ];
                    }




                $dataInicial = [
                    'modelo' => $modeloSeleccionado,
                    'namespace' => $namespaceSeleccionado ?? 'App',
                    'carpeta_vistas' => $carpetaSeleccionada ?? 'abms',
                    'timestamps' => property_exists($instancia, 'timestamps') ? $instancia->timestamps : true,
                    'sincronizable' => property_exists($claseModelo, 'sincronizable') ? $claseModelo::$sincronizable : true,
                    'campos' => $camposIniciales,
                    'form_config' => [],
                    'subformularios' => [],
                    'menu_json' => '',
                ];

                $configJson = $this->generarJsonAbm($dataInicial);

                // 🧾 Guardar JSON generado
                File::put($jsonPath, json_encode($configJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                return back()->withErrors("❌ El modelo {$modeloSeleccionado} no es válido o no tiene fieldsMeta().");
            }
        }
    }

    return view('sistemas.abms.index', [
        'modelos' => $modelos,
        'modeloSeleccionado' => $modeloSeleccionado,
        'namespaceSeleccionado' => $namespaceSeleccionado,
        'carpetaSeleccionada' => $carpetaSeleccionada,
        'campos' => $configJson['campos'] ?? [],
        'configJson' => $configJson,
    ]);
}
public function preview(Request $request, string $modelo)
{
    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");

    if (!File::exists($jsonPath)) {
        return redirect()->route('sistemas.abms.crear')->withErrors("❌ No existe configuración para el modelo {$modelo}. Primero generá el ABM.");
    }

    $json = json_decode(File::get($jsonPath), true);

    // 🆕 Agregar nuevo subformulario (desde formulario superior)
    if ($request->input('accion') === 'agregar_subform') {
        $nuevoSubform = $request->input('nuevo_subform', []);
        if (!empty($nuevoSubform['modelo']) && !empty($nuevoSubform['foreign_key']) && !empty($nuevoSubform['nombre'])) {
            $nuevoSubform['campos'] = [];
            $nuevoSubform['carpeta_vistas'] = $json['carpeta_vistas'] ?? 'abms';
            $nuevoSubform['titulo'] = $nuevoSubform['titulo'] ?? $nuevoSubform['modelo'];
            $nuevoSubform['view_type'] = $nuevoSubform['view_type'] ?? 'inline';
            $nuevoSubform['modo'] = $nuevoSubform['modo'] ?? 'inline';

            $json['subformularios'][] = $nuevoSubform;
            File::put($jsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return redirect()->route('sistemas.abms.preview', ['modelo' => $modelo]);
    }

    // 🧩 Cargar campos de subformulario existente y hacer MERGE si ya hay campos definidos
    $subformIndex = $request->input('subform_index');
    $modeloHijo = $request->input('modelo_hijo');
    $camposSubform = null;

    if ($modeloHijo && is_numeric($subformIndex) && isset($json['subformularios'][$subformIndex])) {
        $subformExistente = $json['subformularios'][$subformIndex];
        $camposSubform = $subformExistente['campos'] ?? [];

        // Si faltan campos, cargar de fieldsMeta y mergear
        $clase = "App\\Models\\Sql\\$modeloHijo";
        if (class_exists($clase) && method_exists($clase, 'fieldsMeta')) {
            $nuevosCampos = $clase::fieldsMeta();
            foreach ($nuevosCampos as $campo => $meta) {
                if (!isset($camposSubform[$campo])) {
                    $camposSubform[$campo] = [
                        'label' => ucfirst(str_replace('_', ' ', $campo)),
                        'input_type' => $meta['input_type'] ?? 'text',
                        'incluir' => true,
                        'nullable' => $meta['nullable'] ?? false,
                        'readonly' => $meta['readonly'] ?? false,
                        'sync' => true,
                        'orden' => 0,
                    ];
                }
            }
        }

        // Actualizar el JSON con los campos mergeados
        $json['subformularios'][$subformIndex]['campos'] = $camposSubform;
        File::put($jsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // 🧾 Datos base
    $campos = $json['campos'] ?? [];
    $form_config = $json['form_config'] ?? [];
    $namespace = $json['namespace'] ?? 'App';
    $carpeta_vistas = $json['carpeta_vistas'] ?? '';
    $primaryKey = $json['primary_key'] ?? 'id';
    $primaryKeySql = $json['primary_key_sql'] ?? [$primaryKey];
    $subformularios = $json['subformularios'] ?? [];

    // 🎨 Normalizar campos padre
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
        $meta['readonly'] = $meta['readonly'] ?? false;
        $meta['orden'] = $meta['orden'] ?? 0;
    }

    return view('sistemas.abms.preview', [
        'modelo' => $modelo,
        'fields' => $campos,
        'namespace' => $namespace,
        'carpeta_vistas' => $carpeta_vistas,
        'primary_key' => $primaryKey,
        'primary_key_sql' => $primaryKeySql,
        'form_config' => $form_config,
        'configJson' => $json,
        'subformularios' => $subformularios,
        'subformIndex' => $subformIndex,
        'modeloHijo' => $modeloHijo,
        'camposSubform' => $camposSubform,
    ]);
}



public function configurar(Request $request)

{ 

    // 🧠 1. Recuperar datos base desde el formulario
    $modelo = $request->input('modelo');
    $namespace = $request->input('namespace');
    $carpeta_vistas = $request->input('carpeta_vistas');
    $force = $request->filled('force_controlador');

    // 🧱 2. Rutas de destino: controlador y vistas
    $controllerName = "{$modelo}Controller";
    $controllerPath = app_path("Http/Controllers/{$namespace}/{$controllerName}.php");
    $viewsPath = resource_path("views/{$carpeta_vistas}");

    // 🧱 3. Crear carpetas si no existen
    if (!file_exists(dirname($controllerPath))) { 
        mkdir(dirname($controllerPath), 0755, true);
    }
    if (!file_exists($viewsPath)) {
        mkdir($viewsPath, 0755, true);
    }

    // ⛔ 4. Prevenir sobreescritura de controlador si no se indicó "force"
   if (!$request->boolean('force_controlador') && file_exists($controllerPath)) {
    return back()->withErrors("El controlador ya existe. Activá 'Reemplazar controlador' para sobreescribirlo.");
}


    // 🧾 5. Cargar configuración anterior si existe (para no romper JSON en acciones parciales)
    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");
    $configAnterior = File::exists($jsonPath) ? json_decode(File::get($jsonPath), true) : [];

    // 🔁 Si viene un subform_index, asegurar su integración con los demás subformularios
    $subformIndex = $request->input('subform_index');
    if ($subformIndex !== null) {
        $subform = [
            'modelo' => $request->input("subformularios.{$subformIndex}.modelo"),
            'foreign_key' => $request->input("subformularios.{$subformIndex}.foreign_key"),
            'nombre' => $request->input("subformularios.{$subformIndex}.nombre"),
            'titulo' => $request->input("subformularios.{$subformIndex}.titulo"),
            'view_type' => $request->input("subformularios.{$subformIndex}.view_type"),
            'modo' => $request->input("subformularios.{$subformIndex}.modo"),
            'carpeta_vistas' => $request->input("subformularios.{$subformIndex}.carpeta_vistas"),
            'campos' => $request->input("subformularios.{$subformIndex}.campos", []),
        ];

        $todos = $request->input('subformularios', []);
        $todos[$subformIndex] = $subform;
        $request->merge(['subformularios' => $todos]);
    }

    // 🧩 6. Procesar subformularios y autocompletar campos si es necesario
$subformulariosConCampos = collect($request->input('subformularios', []))->map(function ($sub, $i) use ($configAnterior) {
    $camposRecibidos = data_get($sub, 'campos', []);

    // 🧠 Si no se enviaron campos desde el request, usá los guardados en el JSON anterior
    if (empty($camposRecibidos) && isset($configAnterior['subformularios'][$i]['campos'])) {
        $camposRecibidos = $configAnterior['subformularios'][$i]['campos'];
    }

    // 🧩 Asegurar metadata base del subform
    // 🔧 Normalizar valores booleanos
    $sub['campos'] = collect($camposRecibidos)->map(function ($meta) {
        foreach (['incluir', 'nullable', 'readonly', 'sync'] as $clave) {
            if (isset($meta[$clave])) {
                $meta[$clave] = filter_var($meta[$clave], FILTER_VALIDATE_BOOLEAN);
            }
        }
        return $meta;
    })->toArray();


    $sub['modelo'] = $sub['modelo'] ?? $configAnterior['subformularios'][$i]['modelo'] ?? null;
    $sub['foreign_key'] = $sub['foreign_key'] ?? $configAnterior['subformularios'][$i]['foreign_key'] ?? null;
    $sub['nombre'] = $sub['nombre'] ?? $configAnterior['subformularios'][$i]['nombre'] ?? "subform_{$i}";
    $sub['titulo'] = $sub['titulo'] ?? $sub['nombre'];
    $sub['view_type'] = $sub['view_type'] ?? 'inline';
    $sub['modo'] = $sub['modo'] ?? 'inline';
    $sub['carpeta_vistas'] = $sub['carpeta_vistas'] ?? $configAnterior['subformularios'][$i]['carpeta_vistas'] ?? 'abms';

    return $sub;
})->toArray();
    // 🧱 7. Preparar datos para generar el JSON (restaurando campos si no se recibieron)
    $data = [
        'modelo' => $modelo,
        'namespace' => $namespace,
        'carpeta_vistas' => $carpeta_vistas,
        'timestamps' => $request->boolean('timestamps'),
        'sincronizable' => $request->boolean('sincronizable'),
        'force_controlador' => $request->boolean('force_controlador'),
        'primary_key' => $request->input('primary_key'),
        'primary_key_sql' => explode(',', $request->input('primary_key_sql', '')),
        'campos' => $request->has('campos') ? $request->input('campos') : ($configAnterior['campos'] ?? []),
        'form_config' => $request->input('form_config', []),
        'subformularios' => $subformulariosConCampos,
        'menu_json' => $request->input('menu_json', ''),
    ];

    // 🧩 8. Generar el nuevo JSON completo
    $config = $this->generarJsonAbm($data);

    // 💾 9. Guardar el archivo JSON en disco
    if (!file_exists(dirname($jsonPath))) {
        mkdir(dirname($jsonPath), 0755, true);
    }
    file_put_contents($jsonPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    logger()->info("📄 JSON generado:", $config);

    // 🧠 10. Actualizar el $fillable del modelo con campos marcados como 'incluir'
    $camposIncluir = array_keys(array_filter($config['campos'], fn($c) => !empty($c['incluir'])));
    $this->actualizarFillableModelo($modelo, $namespace, $camposIncluir);

    // 🛠️ 11. Variables para reemplazos en los stubs
    $nombres = Str::snake(Str::pluralStudly($modelo));
    $snakeModel = Str::snake($modelo);
    $formRoute = $config['form_config']['form_route'] ?? strtolower("produccion/abms/{$snakeModel}");
    $routeName = str_replace('/', '.', strtolower($formRoute));

    $replacements = [
        '__MODELO__' => $modelo,
        '__NOMBRE_RUTA__' => $routeName,
        '__NOMBRES__' => $nombres,
        '__NAMESPACE__' => $namespace,
        '__CARPETA_VISTAS__' => $carpeta_vistas,
        '__FORM_VIEW_TYPE__' => $config['form_config']['form_view_type'] ?? 'default',
    ];

    // 🧩 12. Generar el controlador usando stub
    $controllerStub = file_get_contents(resource_path("stubs/abm/controller.stub.php"));
    $controllerContent = str_replace(array_keys($replacements), array_values($replacements), $controllerStub);
    file_put_contents($controllerPath, $controllerContent);

    // 🧩 13. Generar vistas desde stubs
    $this->generarVistasAbm($modelo, $namespace, $carpeta_vistas, $routeName, $config, $replacements);

    // 📥 14. Crear FormRequest si fue indicado
    if ($request->filled('generar_request')) {
        $requestPath = app_path("Http/Requests/{$modelo}Request.php");
        if (!file_exists(dirname($requestPath))) {
            mkdir(dirname($requestPath), 0755, true);
        }
        $requestStub = file_get_contents(resource_path("stubs/abm/request.stub.php"));
        $requestContent = str_replace(array_keys($replacements), array_values($replacements), $requestStub);
        file_put_contents($requestPath, $requestContent);
    }

    // 🔗 15. Agregar la ruta automáticamente al archivo web.php
    $this->agregarRutaWeb($modelo, $carpeta_vistas, $namespace);

    // ✅ 16. Redirección según modo de guardado
$modoGuardado = $request->input('modo_guardado');

if ($modoGuardado === 'subform') {
    return redirect()
        ->route('sistemas.abms.preview', ['modelo' => $modelo])
        ->with('success', '✅ Subformulario actualizado correctamente');
}

    return view('sistemas.abms.resultado', [
        'modelo' => $modelo,
        'carpeta_vistas' => $carpeta_vistas,
        'controller_path' => $controllerPath,
        'request_path' => $request->filled('generar_request') ? $requestPath ?? null : null,
        'ruta_logica' => $formRoute,
    ]);
}




/**
 * 🧠 Construye el JSON completo de configuración del ABM basado en datos ingresados por el usuario
 *
 * @param array $data Información del formulario (campos, subformularios, etc.)
 * @return array JSON estructurado listo para guardar
 * @throws \Exception si el modelo no existe o no tiene fieldsMeta()
 */
private function generarJsonAbm(array $data): array
{
    // 1. ✅ Validaciones mínimas requeridas
    if (empty($data['modelo']) || empty($data['namespace']) || empty($data['carpeta_vistas'])) {
        throw new \InvalidArgumentException("Faltan datos obligatorios: modelo, namespace o carpeta_vistas.");
    }

    $modelo = $data['modelo'];
    $namespace = $data['namespace'];
    $carpetaVistas = $data['carpeta_vistas'];
    $camposRaw = $data['campos'] ?? [];
    $subformularios = $data['subformularios'] ?? [];
    $formConfig = $data['form_config'] ?? [];
    $menuJson = $data['menu_json'] ?? '';

    // 2. 🧩 Determinar clase principal del modelo
    $clase = "App\\Models\\{$modelo}";
    if (!class_exists($clase) || !method_exists($clase, 'fieldsMeta')) {
        throw new \Exception("El modelo {$modelo} no tiene fieldsMeta()");
    }

    // 3. 📦 Metadata base del modelo
    $metaFields = $clase::fieldsMeta();
    $primaryKey = property_exists($clase, 'primaryKey') ? (new $clase)->getKeyName() : 'id';

    // 4. 🗝️ Buscar clave primaria real para SQL Server (si existe)
    $primaryKeySql = [$primaryKey]; // por defecto
    foreach (["App\\Models\\{$modelo}", "App\\Models\\Sql\\{$modelo}"] as $claseCandidata) {
        if (class_exists($claseCandidata) && property_exists($claseCandidata, 'primaryKeySql')) {
            $primaryKeySql = $claseCandidata::$primaryKeySql;
            break;
        }
    }

    // 5. 🧠 Indices definidos en el modelo (opcional)
    $allIndices = $metaFields['indices'] ?? [];

    // 6. 🔁 Procesamiento de campos
    $camposProcesados = [];
    foreach ($camposRaw as $campo => $meta) {
        $inputType = $meta['input_type'] ?? 'text';
        $camposProcesados[$campo] = [
            'label' => $meta['label'] ?? ucwords(str_replace('_', ' ', $campo)),
            'default' => $meta['default'] ?? null,
            'input_type' => $inputType,
            'incluir' => !empty($meta['incluir']) || $campo === $primaryKey,
            'nullable' => !empty($meta['nullable']),
            'readonly' => $meta['readonly'] ?? false,
            'orden' => $meta['orden'] ?? 0,
            'select_list_data' => $meta['select_list_data'] ?? null,
            'referenced_table' => $meta['referenced_table'] ?? null,
            'referenced_column' => $meta['referenced_column'] ?? 'id',
            'referenced_label' => $meta['referenced_label'] ?? 'nombre',
            'is_boolean' => $inputType === 'checkbox',
            'auto_increment_plus' => $inputType === 'autonumerico',
            'sync' => $meta['sync'] ?? true,
        ];

        // ✅ Valores personalizados para checkbox
        if ($inputType === 'checkbox') {
            $valores = explode(',', $meta['select_list_data'] ?? '');
            if (count($valores) === 2) {
                [$labelCheck, $checkedValue] = array_pad(explode('=', trim($valores[0])), 2, '');
                [$labelUncheck, $uncheckedValue] = array_pad(explode('=', trim($valores[1])), 2, '');
                if ($checkedValue && $uncheckedValue) {
                    $camposProcesados[$campo]['checkbox_checked_value'] = $checkedValue;
                    $camposProcesados[$campo]['checkbox_unchecked_value'] = $uncheckedValue;
                }
            }

            // Default fallback
            $camposProcesados[$campo]['checkbox_checked_value'] ??= 'S';
            $camposProcesados[$campo]['checkbox_unchecked_value'] ??= 'N';
        }
    }

    // 7. 👁️ Agregar campo ID si no está pero Laravel lo usa
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
            'sync' => false,
        ];
    }

// 8. 📁 Subformularios: validar, enriquecer y normalizar campos
foreach ($subformularios as $i => &$subform) {
    if (empty($subform['carpeta_vistas'])) {
        throw new \Exception("Falta definir 'carpeta_vistas' en el subformulario de índice {$i}");
    }

    $subform['ruta'] = strtolower(basename($subform['carpeta_vistas']));

    // 🆕 Asegurar campos clave adicionales
    $subform['nombre'] = $subform['nombre'] ?? 'subform_' . $i;
    $subform['view_type'] = $subform['view_type'] ?? 'default';

    // Si tiene campos declarados, procesarlos como en el padre
    $subformCamposRaw = $subform['campos'] ?? [];
    $subformCamposProcesados = [];

    foreach ($subformCamposRaw as $campo => $meta) {
        $inputType = $meta['input_type'] ?? 'text';
        $subformCamposProcesados[$campo] = [
            'label' => $meta['label'] ?? ucwords(str_replace('_', ' ', $campo)),
            'default' => $meta['default'] ?? null,
            'input_type' => $inputType,
            'incluir' => !empty($meta['incluir']),
            'nullable' => !empty($meta['nullable']),
            'readonly' => $meta['readonly'] ?? false,
            'orden' => $meta['orden'] ?? 0,
            'select_list_data' => $meta['select_list_data'] ?? null,
            'referenced_table' => $meta['referenced_table'] ?? null,
            'referenced_column' => $meta['referenced_column'] ?? 'id',
            'referenced_label' => $meta['referenced_label'] ?? 'nombre',
            'is_boolean' => $inputType === 'checkbox',
            'auto_increment_plus' => $inputType === 'autonumerico',
            'sync' => $meta['sync'] ?? true,
        ];

        if ($inputType === 'checkbox') {
            $valores = explode(',', $meta['select_list_data'] ?? '');
            if (count($valores) === 2) {
                [$labelCheck, $checkedValue] = array_pad(explode('=', trim($valores[0])), 2, '');
                [$labelUncheck, $uncheckedValue] = array_pad(explode('=', trim($valores[1])), 2, '');
                if ($checkedValue && $uncheckedValue) {
                    $subformCamposProcesados[$campo]['checkbox_checked_value'] = $checkedValue;
                    $subformCamposProcesados[$campo]['checkbox_unchecked_value'] = $uncheckedValue;
                }
            }

            $subformCamposProcesados[$campo]['checkbox_checked_value'] ??= 'S';
            $subformCamposProcesados[$campo]['checkbox_unchecked_value'] ??= 'N';
        }
    }

    $subform['campos'] = $subformCamposProcesados;
}


   // 9. 🧾 Config del formulario: valores por defecto
        $formConfig['form_name'] ??= $modelo;
        $formConfig['form_route'] ??= strtolower("produccion/abms/" . Str::snake($modelo));
        $formConfig['tipo_formulario'] ??= 'default';
        $formConfig['usa_paginador'] ??= true;
        $formConfig['per_page'] ??= 100;
        $formConfig['form_view_type'] ??= 'default';
        $formConfig['index_view_type'] ??= 'default';


    // 🔗 Menú: decode si viene como string
    $menu = [];
    if (!empty($menuJson)) {
        $decoded = json_decode($menuJson, true);
        if (is_array($decoded)) {
            $menu = $decoded;
        }
    }

    // Si no hay menú definido, generar uno mínimo
    if (empty($menu)) {
        $menu[] = [
            'mostrar' => true,
            'label' => $modelo,
            'icon' => '📄',
            'modulo' => strtolower($namespace),
            'grupo' => ucfirst($namespace),
            'posicion' => 99,
        ];
    }

    // ✅ Salida final
    return [
        'modelo' => $modelo,
        'namespace' => $namespace,
        'carpeta_vistas' => $carpetaVistas,
        'timestamps' => $data['timestamps'] ?? false,
        'sincronizable' => $data['sincronizable'] ?? true,
        'force_controlador' => true,
        'primary_key' => $primaryKey,
        'primary_key_sql' => $primaryKeySql,
        'indices' => $allIndices,
        'campos' => $camposProcesados,
        'form_config' => $formConfig,
        'subformularios' => $subformularios,
        'menu' => $menu,
    ];
}
public function guardarSubformulario(Request $request)
{
    $modelo = $request->input('modelo');
    $subformIndex = $request->input('subform_index');
    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");

    if (!File::exists($jsonPath)) {
        return back()->withErrors("❌ No existe archivo de configuración JSON para el modelo {$modelo}.");
    }

    $json = json_decode(File::get($jsonPath), true);

    // 🧩 Normalizar campos del subformulario
    $campos = $request->input("subformularios.{$subformIndex}.campos", []);
    $camposNormalizados = [];

    foreach ($campos as $campo => $meta) {
        $camposNormalizados[$campo] = [
            'label' => $meta['label'] ?? '',
            'input_type' => $meta['input_type'] ?? 'text',
            'incluir' => filter_var($meta['incluir'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'nullable' => filter_var($meta['nullable'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'readonly' => filter_var($meta['readonly'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'sync' => filter_var($meta['sync'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'orden' => intval($meta['orden'] ?? 0),
        ];
    }

    // 🧩 Guardar el subformulario con campos normalizados
    $json['subformularios'][$subformIndex] = [
        'modelo' => $request->input("subformularios.{$subformIndex}.modelo"),
        'foreign_key' => $request->input("subformularios.{$subformIndex}.foreign_key"),
        'nombre' => $request->input("subformularios.{$subformIndex}.nombre"),
        'titulo' => $request->input("subformularios.{$subformIndex}.titulo"),
        'view_type' => $request->input("subformularios.{$subformIndex}.view_type"),
        'modo' => $request->input("subformularios.{$subformIndex}.modo"),
        'carpeta_vistas' => $request->input("subformularios.{$subformIndex}.carpeta_vistas"),
        'campos' => $camposNormalizados,
    ];

    File::put($jsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return redirect()
        ->route('sistemas.abms.preview', ['modelo' => $modelo])
        ->with('success', '✅ Subformulario guardado correctamente.');
}






/**
 * 🧩 Genera automáticamente las vistas blade para un ABM
 */
protected function generarVistasAbm(string $modelo, string $namespace, string $carpeta_vistas, string $routeName, array $config, array $replacements): void
{
    // 🧱 Crear la carpeta si no existe
    $viewsPath = resource_path("views/{$carpeta_vistas}");
    if (!file_exists($viewsPath)) {
        mkdir($viewsPath, 0755, true);
    }

    // 🧠 Preferencias del formulario
    $formViewType = $config['form_config']['form_view_type'] ?? 'default';
    $indexViewType = $config['form_config']['index_view_type'] ?? 'default';
    $campos = $config['campos'] ?? [];

    // 🧩 Preparar tabs y labels para los stubs avanzados
    $tabs = collect($campos)->groupBy(fn($meta) => $meta['tab'] ?? 'general');
    $fieldLabels = collect($campos)->mapWithKeys(fn($meta, $campo) => [$campo => $meta['label'] ?? Str::headline($campo)]);
    $replacements['__TABS__'] = var_export($tabs->toArray(), true);
    $replacements['__FIELD_LABELS__'] = var_export($fieldLabels->toArray(), true);

    // 🧾 Vista INDEX (con lógica condicional)
    $subformularios = $config['subformularios'] ?? [];
    $tieneSubformInline = collect($subformularios)->contains(fn($s) => ($s['modo'] ?? null) === 'inline');

    $indexStub = match ($indexViewType) {
        'ficha'      => 'index-ficha.stub.blade.php',
        'inline-tab' => 'index-inline-tab.stub.blade.php',
        'inline'     => 'index-inline.stub.blade.php',
        'tab'        => 'index-tab.stub.blade.php',
        default      => $tieneSubformInline ? 'index-inline.stub.blade.php' : 'index.stub.blade.php',
    };
    
    // Generar archivo principal de index según su tipo
    $indexBlade = match ($indexViewType) {
        'ficha'      => 'index-ficha.blade.php',
        'inline-tab' => 'index-inline-tab.blade.php',
        'inline'     => 'index-inline.blade.php',
        'tab'        => 'index-tab.blade.php',
        default      => 'index.blade.php',
    };
    
    // 1. Guardar como vista específica
    $this->copiarVistaDesdeStub($indexStub, "{$viewsPath}/{$indexBlade}", $replacements);
    
    // 2. También dejar fallback como index.blade.php para retrocompatibilidad
    if ($indexBlade !== 'index.blade.php') {
        $this->copiarVistaDesdeStub($indexStub, "{$viewsPath}/index.blade.php", $replacements);
    }
    

    // 🧾 Vistas CREATE / EDIT
    if ($formViewType === 'modal') {
        // Usamos modal.stub y también generamos los archivos individuales
        foreach (['create', 'edit'] as $tipo) {
            $stub = "{$tipo}-modal.stub.blade.php";
            $this->copiarVistaDesdeStub($stub, "{$viewsPath}/{$tipo}-modal.blade.php", $replacements);
        }
    } else {
        // En formulario clásico: create.blade.php y edit.blade.php
        foreach (['create', 'edit'] as $tipo) {
            $stub = "{$tipo}.stub.blade.php";
            $this->copiarVistaDesdeStub($stub, "{$viewsPath}/{$tipo}.blade.php", $replacements);
        }
    }

    // 📄 Vista SHOW (siempre)
    $this->copiarVistaDesdeStub('show.stub.blade.php', "{$viewsPath}/show.blade.php", $replacements);
}
public function cargarCamposSubformulario(Request $request)
{
    $modelo = $request->input('modelo');
    if (!$modelo) return response()->json(['error' => 'Falta modelo'], 400);

    $modeloSql = "\\App\\Models\\Sql\\{$modelo}";
    if (!class_exists($modeloSql) || !method_exists($modeloSql, 'fieldsMeta')) {
        return response()->json(['error' => 'Modelo o método no válido'], 400);
    }

    $fields = $modeloSql::fieldsMeta();

    $campos = collect($fields)->map(function ($meta, $campo) {
        return [
            'label' => ucfirst(str_replace('_', ' ', $campo)),
            'input_type' => 'text',
            'incluir' => true,
            'nullable' => $meta['nullable'] ?? false,
            'readonly' => $meta['readonly'] ?? false,
            'sync' => true,
            'orden' => 0,
        ];
    });

    return response()->json(['campos' => $campos]);
}


protected function copiarVistaDesdeStub(string $stub, string $destino, array $replacements): void
{
    $stubPath = resource_path("stubs/abm/{$stub}");

    if (!file_exists($stubPath)) {
        $mensaje = "❌ Stub no encontrado: {$stubPath}. No se pudo generar la vista: {$destino}.";

        // 1. Registrar en logs
        logger()->warning($mensaje);

        // 2. Mostrar en consola si es Artisan
        if (app()->runningInConsole()) {
            echo $mensaje . "\n";
        } else {
            // 3. Si es ejecución web, lanzar excepción
            throw new \RuntimeException($mensaje);
        }

        return;
    }

    // ✅ Si todo está bien, generar vista
    $contenido = file_get_contents($stubPath);
    $contenido = str_replace(array_keys($replacements), array_values($replacements), $contenido);
    file_put_contents($destino, $contenido);
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

    // 🧠 Leer configuración JSON
    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");
    if (!File::exists($jsonPath)) {
        throw new \Exception("No se encontró el archivo de configuración JSON para {$modelo}");
    }

    $config = json_decode(File::get($jsonPath), true);
    $formRoute = $config['form_config']['form_route'] ?? null;

    if (empty($formRoute)) {
        throw new \Exception("La ruta lógica ('form_route') no fue definida en la configuración de {$modelo}");
    }

    // 🧠 Derivar nombre de grupo y controlador
    $prefix = $formRoute; // ej: produccion/abms/marcas
    $modeloSnake = basename($formRoute); // ej: marcas
    $nombreGrupo = str_replace('/', '.', $prefix); // ej: produccion.abms.marcas

    // 👮 Controlador completo
    $controladorCompleto = "App\\Http\\Controllers\\{$namespace}\\{$modelo}Controller";
    $uso = "use {$controladorCompleto};";
    $usoFinal = Str::contains($contenidoActual, $uso) ? '' : $uso;

    // 🛑 Evitar duplicados
    if (!Str::contains($contenidoActual, "Route::prefix('{$prefix}')->name('{$nombreGrupo}.')->group")) {
        $bloqueRuta = <<<PHP

// 🧩 Ruta generada automáticamente por ABM Creator
// Modelo: {$modelo} - Generado el {$fecha}
{$usoFinal}

Route::prefix('{$prefix}')->name('{$nombreGrupo}.')->group(function () {
    Route::resource('', {$modelo}Controller::class)
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

    Route::post('{id}/restaurar', [{$modelo}Controller::class, 'restaurar'])->name('restaurar');
});
PHP;

        file_put_contents($rutaArchivo, $bloqueRuta, FILE_APPEND);
        \Log::info("✅ Ruta agregada al web.php para {$modelo}");
    } else {
        \Log::info("ℹ️ La ruta para {$modelo} ya existía y no fue duplicada.");
    }
}




}