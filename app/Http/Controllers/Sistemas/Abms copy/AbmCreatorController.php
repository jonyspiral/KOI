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


    public function configurar(Request $request)
    {
        $modeloCompleto = $request->input('modelo');
        $partes = explode('.', $modeloCompleto);
        $modelo = array_pop($partes);
    
        $tabla = Str::snake(Str::pluralStudly($modelo));
        $namespaceControlador = $request->input('namespace');
        $carpetaVistas = $request->input('carpeta_vistas') ;
        
        // Validación de modelo
        if (empty($modelo)) {
            throw new \Exception("El nombre del modelo está vacío.");
        }
    
        // Verificar si el modelo existe
        $modeloClass = "App\\Models\\$modelo";
        if (!class_exists($modeloClass)) {
            throw new \Exception("El modelo $modeloClass no existe. Verifica que el archivo esté en app/Models y el nombre coincida.");
        }
    
        // Obtener la tabla y la conexión del modelo
        $tablaReal = (new $modeloClass)->getTable();
        $conexion = (new $modeloClass)->getConnection();
    
        // Obtener columnas desde INFORMATION_SCHEMA
        $camposRaw = $conexion->select("SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY, EXTRA 
                                        FROM INFORMATION_SCHEMA.COLUMNS 
                                        WHERE TABLE_NAME = ?", [$tablaReal]);
    
        $campos = [];
        foreach ($camposRaw as $col) {
            $campo = $col->COLUMN_NAME;
            $tipo = $col->DATA_TYPE;
            $key = $col->COLUMN_KEY;
            $esAutoIncremental = strpos($col->EXTRA, 'auto_increment') !== false;
            $esNumerico = in_array($tipo, ['int', 'bigint', 'smallint', 'tinyint']);
    
            $campos[$campo] = [
                'tipo' => $tipo,
                'incluir' => !$esAutoIncremental,
                'tipo_input' => 'input',
                'autoincremental' => $esAutoIncremental,
                'editable' => !$esAutoIncremental,
                'es_numerico' => $esNumerico,
                'key' => $key,
            ];
        }
    
        // Guardar datos en sesión para usar en finalizar()
        session([
            'abm.modelo' => $modelo,
            'abm.namespace' => $namespaceControlador,
            'abm.carpeta_vistas' => $carpetaVistas,
            'abm.campos' => $campos,
        ]);
    
        $namespace = $namespaceControlador;
    
        return view('sistemas.abms.preview', compact('modelo', 'tabla', 'namespace', 'carpetaVistas', 'campos'));
    }

    public function preview(Request $request)
    {
        $namespaceControlador = 'namespaceControlador';
        $modelo = $request->input('modelo');
        $carpetaVistas = $request->input('carpeta_vistas') ?? strtolower($modelo);
        $namespace = $request->input('namespace_controlador') ?? '';
        
        $modeloClass = "App\\Models\\$modelo";
        if (!class_exists($modeloClass)) {
            return back()->with('error', "El modelo {$modeloClass} no existe.");
        }

        $tabla = (new $modeloClass)->getTable();
        $conexion = (new $modeloClass)->getConnection();

        $columnas = $conexion->select("SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY, EXTRA 
                                        FROM INFORMATION_SCHEMA.COLUMNS 
                                        WHERE TABLE_NAME = ?", [$tabla]);

        return view('sistemas.abms.preview', compact('modelo', 'namespace', 'carpetaVistas', 'columnas'));
    }

    public function generar(Request $request)
{
    Log::info('✅ Entrando al método generar()');

    $request->validate([
        'tabla' => 'required|string',
        'namespace_controlador' => 'required|string',
        'carpeta_vistas' => 'required|string',
        'campos' => 'required|array',
    ]);

    $tabla = $request->input('tabla');
    $namespace = $request->input('namespace_controlador');
    $carpetaVistas = $request->input('carpeta_vistas');
    $campos = $request->input('campos');
    $modelo = ucfirst(Str::camel($tabla));

    $request->merge([
        'modelo' => $modelo,
        'tabla' => $tabla,
        'namespace_controlador' => $namespace,
        'carpeta_vistas' => $carpetaVistas,
        'campos' => $campos,
    ]);

    $modeloPath = app_path("Models/{$modelo}.php");

    $fillable = [];
    foreach ($campos as $campo => $conf) {
        if (!empty($conf['incluir'])) {
            $fillable[] = $campo;
        }
    }

    if (!File::exists($modeloPath)) {
        $fillableString = implode(', ', array_map(fn($campo) => "'{$campo}'", $fillable));
        $modelCode = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$modelo} extends Model
{
    protected \$table = '{$tabla}';
    protected \$primaryKey = 'id';
    public \$incrementing = true;
    public \$timestamps = true;

    protected \$fillable = [{$fillableString}];
}
PHP;
        File::put($modeloPath, $modelCode);
    } else {
        $currentCode = File::get($modeloPath);
        $pattern = "/protected\s+\$fillable\s*=\s*\[[^\]]*\];/s";

        if (preg_match($pattern, $currentCode, $matches)) {
            $existentes = array_map('trim', explode(',', str_replace("'", '', $matches[1])));
            $nuevos = array_unique(array_merge($existentes, $fillable));
            $nuevoFillable = implode(', ', array_map(fn($campo) => "'{$campo}'", $nuevos));
            $newCode = preg_replace($pattern, "protected \$fillable = [{$nuevoFillable}];", $currentCode);
        } else {
            $nuevoFillable = implode(', ', array_map(fn($campo) => "'{$campo}'", $fillable));
            $newCode = str_replace("}", "    protected \$fillable = [{$nuevoFillable}];\n}", $currentCode);
        }

        File::put($modeloPath, $newCode);
    }

    session([
        'abm.tabla' => $tabla,
        'abm.modelo' => $modelo,
        'abm.namespace_controlador' => $namespace,
        'abm.carpeta_vistas' => $carpetaVistas,
        'abm.campos' => $campos,
        'abm.ruta_redireccion' => str_replace('/', '.', strtolower($carpetaVistas)),
    ]);

    $this->generarControlador($request);
    $this->generarVistas($request);
    $this->agregarRutaWeb($namespace, $modelo, $carpetaVistas);

    return view('sistemas.abms.resultado_modelo', [
        'modelo' => $modelo,
        'modeloPath' => $modeloPath,
        'modeloCode' => File::get($modeloPath),
        'campos' => $campos,
        'namespace' => $namespace,
        'carpetaVistas' => $carpetaVistas
    ]);
}


    

    public function generarControlador(Request $request)
    {  
        $modelo = $request->input('modelo');
        $tabla = $request->input('tabla');
        $campos = $this->decodificarCamposJson($request);
        $namespace = $request->input('namespace_controlador');

        $controllerName = $modelo . 'Controller';
        $controllerDir = app_path('Http/Controllers/' . str_replace('.', '/', $namespace));
        $controllerPath = $controllerDir . '/' . $controllerName . '.php';

        $modeloClass = 'App\\Models\\' . $modelo;
        $routeName = str_replace('/', '.', strtolower($namespace)) . '.' . Str::snake($modelo);
        $vistaPath = str_replace('/', '.', $request->input('carpeta_vistas'));

        // Filtrar campos que no son autoincrementales y están marcados para incluir
        $camposOnly = implode(', ', array_map(fn($c) => "'$c'", array_keys(array_filter($campos, fn($c) => !empty($c['incluir']) && empty($c['autoincremental'])))));

        $displayModelo = Str::headline($modelo);

        // Generar el contenido del controlador
        $controllerCode = <<<PHP
<?php

namespace App\Http\Controllers\\$namespace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use $modeloClass;

class $controllerName extends Controller
{
    public function index()
    {
        \$registros = $modelo::all();
        \$modelo = '$displayModelo';
        return view('$vistaPath.index', compact('registros', 'modelo'));
    }

    public function create()
    {
        \$modelo = '$displayModelo';
        return view('$vistaPath.form', compact('modelo'));
    }

    public function store(Request \$request)
    {
        $modelo::create(\$request->only([$camposOnly]));
        return redirect()->route('$vistaPath.index');
    }

    public function edit(\$id)
    {
        \$registro = $modelo::findOrFail(\$id);
        \$modelo = '$displayModelo';
        return view('$vistaPath.form', compact('registro', 'modelo'));
    }

    public function update(Request \$request, \$id)
    {
        \$registro = $modelo::findOrFail(\$id);
        \$registro->update(\$request->only([$camposOnly]));
        return redirect()->route('$vistaPath.index');
    }

    public function destroy(\$id)
    {
        $modelo::destroy(\$id);
        return redirect()->route('$vistaPath.index');
    }
}
PHP;
//dd("entro");
// Crear carpeta si no existe
if (!File::exists($controllerDir)) {
    File::makeDirectory($controllerDir, 0755, true);
    Log::info("📂 Carpeta creada: $controllerDir");
}

// ✅ Verificar si debe sobrescribir o no
if (!File::exists($controllerPath) || $request->input('sobrescribir', false)) {
    try {
        File::put($controllerPath, $controllerCode);
        Log::info("✅ Controlador creado o sobrescrito en: $controllerPath");
    } catch (\Exception $e) {
        Log::error("❌ No se pudo escribir el controlador: " . $e->getMessage());
        throw new \Exception("❌ No se pudo escribir el controlador. Verificá permisos de escritura en: $controllerPath");
    }
} else {
    Log::info("⚠️ Controlador existente. No se sobrescribió: $controllerPath");
}
            }

    public function generarVistas(Request $request)
{
    $modelo = $request->input('modelo');
    $carpetaVistas = $request->input('carpeta_vistas') ?? strtolower($modelo);
    $campos = $this->decodificarCamposJson($request);
  
    // Crear carpeta de vistas si no existe
    $carpeta = resource_path('views/' . $carpetaVistas);
    File::ensureDirectoryExists($carpeta);

    // ✅ Corrige la forma del nombre de ruta para route(...)
    $nombreRuta = str_replace('/', '.', $carpetaVistas); // ej: 'produccion.rutas_produccion'
   
    // INDEX
    $thead = '';
    $tbody = '';
    foreach ($campos as $campo => $conf) {
        if (isset($conf['incluir'])) {
            $thead .= "<th>$campo</th>\n";
            $tbody .= "<td>{{ \$r->$campo }}</td>\n";
        }
    }

    $indexView = <<<BLADE
@extends('layouts.app')

@section('content')
<div class="container">
  <h2>$modelo</h2>
  <a href="{{ route('$nombreRuta.create') }}" class="btn btn-success mb-2">Nuevo</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        $thead
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach(\$registros as \$r)
        <tr>
          $tbody
          <td>
            <a href="{{ route('$nombreRuta.edit', \$r->id) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('$nombreRuta.destroy', \$r->id) }}" style="display:inline;">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger">Eliminar</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
BLADE;

    File::put("$carpeta/index.blade.php", $indexView);

    // FORM
    $formFields = '';
    foreach ($campos as $campo => $conf) {
        if (!isset($conf['incluir'])) continue;

        $label = ucfirst(str_replace('_', ' ', $campo));
        $input = "<input type=\"text\" name=\"$campo\" value=\"{{ \$registro->$campo ?? old('$campo') }}\" class=\"form-control\">";

        switch ($conf['tipo_input']) {
            case 'textarea':
                $input = "<textarea name=\"$campo\" class=\"form-control\">{{ \$registro->$campo ?? old('$campo') }}</textarea>";
                break;

            case 'select':
                if (!empty($conf['tabla_ref']) && !empty($conf['campo_mostrar'])) {
                    $var = $conf['tabla_ref'];
                    $campoMostrar = $conf['campo_mostrar'];
                    $input = <<<HTML
<select name="$campo" class="form-control">
  @foreach(\${$var} as \$item)
    <option value="{{ \$item->$campo }}" {{ (isset(\$registro) && \$registro->$campo == \$item->$campo) ? 'selected' : '' }}>
      {{ \$item->$campoMostrar }}
    </option>
  @endforeach
</select>
HTML;
                }
                break;

            case 'checkbox':
                $input = <<<HTML
<input type="checkbox" name="$campo" value="1" {{ (!empty(\$registro) && \$registro->$campo) ? 'checked' : '' }}>
HTML;
                break;

            case 'date':
                $input = <<<HTML
<input type="date" name="$campo" value="{{ isset(\$registro) ? \Illuminate\Support\Carbon::parse(\$registro->$campo)->format('Y-m-d') : old('$campo') }}" class="form-control">
HTML;
                break;
        }

        $formFields .= <<<HTML
<div class="mb-3">
  <label for="$campo" class="form-label">$label</label>
  $input
</div>

HTML;
    }

    $formView = <<<BLADE
@extends('layouts.app')

@section('content')
<div class="container">
  <h2>{{ isset(\$registro) ? 'Editar' : 'Nuevo' }} $modelo</h2>

  <form method="POST" action="{{ isset(\$registro) ? route('$nombreRuta.update', \$registro->id) : route('$nombreRuta.store') }}">
    @csrf
    @if(isset(\$registro)) @method('PUT') @endif

    $formFields

    <button class="btn btn-primary">Guardar</button>
    <a href="{{ route('$nombreRuta.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection
BLADE;


Log::info('Generar controlador request:', $request->all());
    File::put("$carpeta/form.blade.php", $formView);

    return redirect(url($carpetaVistas));
}

public function finalizar(Request $request)
{
    // 1. Recuperar los datos de sesión
    $tabla = session('abm.tabla');
    $modelo = session('abm.modelo');
    $namespace = session('abm.namespace_controlador');
    $carpetaVistas = session('abm.carpeta_vistas');
    $campos = session('abm.campos');

  
   // VISTA INDEX
  // $indexCode = "@extends('layouts.app')\n\n@section('content')\n<div class=\"container\">\n<h3>Listado de {$modelo}</h3>\n</div>\n@endsection";
 //  File::put("{$carpetaVistas}/index.blade.php", $indexCode);

   // VISTA FORM
   //$formCode = "@extends('layouts.app')\n\n@section('content')\n<div class=\"container\">\n<h3>Formulario de {$modelo}</h3>\n</div>\n@endsection";
   //File::put("{$carpetaVistas}/form.blade.php", $formCode);

    // 2. Validar que los campos existan
    if (!$campos || !is_array($campos)) {
        return back()->with('error', 'No hay campos válidos en sesión.');
    }

    // 3. Generar controlador
    $request->merge([
        'tabla' => $tabla,
        'namespace_controlador' => $namespace,
        'carpeta_vistas' => $carpetaVistas,
        'campos' => $campos,
    ]);
    
    $this->generarControlador($request);

    // 4. Generar vistas
    $this->generarVistas($request);

    // 5. Agregar ruta en web.php
    $this->agregarRutaWeb($namespace, $modelo, $carpetaVistas);
 
    // 6. Redirigir al index generado
    try {
        return redirect()->route(strtolower(str_replace('/', '.', $carpetaVistas)) . '.index');
    } catch (\Exception $e) {
        return view('sistemas.abms.resultado', [
            'modelo' => $modelo,
            'modeloPath' => app_path("Models/$modelo.php"),
            'modeloCode' => File::get(app_path("Models/$modelo.php")),
            'campos' => $campos,
            'rutaError' => $e->getMessage(),
            'namespace_controlador' => $namespace ?? '',
            'indexView' => $indexView ?? '',
            'formView' => $formformView ?? '',
        ]);

 


    }
}


private function agregarRutaWeb(string $namespace, string $modeloNombre, string $carpetaVistas)
{
    $rutaArchivo = base_path('routes/web.php');
    $ruta = strtolower(str_replace('_', '-', $carpetaVistas));
    $controlador = "{$modeloNombre}Controller";
    $namespaceCompleto = "App\\Http\\Controllers\\" . trim($namespace, '\\') . "\\{$controlador}";
    $lineaUse = "use {$namespaceCompleto};";
    $nombreRuta = strtolower(str_replace('/', '.', $carpetaVistas));

    $partes = explode('.', $nombreRuta);
    if (count($partes) >= 2) {
        $prefix = implode('/', array_slice($partes, 0, -1));
        $grupoNombre = implode('.', array_slice($partes, 0, -1));
        $subruta = end($partes);

        $lineaRoute = <<<PHP
            Route::prefix('$prefix')->name('$grupoNombre.')->group(function () {
                Route::resource('$subruta', {$controlador}::class)->names('$subruta');
            });
            PHP;
    } else {
        $lineaRoute = "Route::resource('$nombreRuta', {$controlador}::class)->names('$nombreRuta');";
    }

    $contenido = File::get($rutaArchivo);
    $lineas = explode("\n", $contenido);

    $controladorBase = class_basename($controlador);
    $yaExiste = collect($lineas)->contains(function ($linea) use ($controladorBase) {
        return preg_match("/Route::resource\\(.*?,\\s*{$controladorBase}::class\\)/", $linea);
    });

    if ($yaExiste) {
        return;
    }

    if (!str_contains($contenido, $lineaUse)) {
        $nuevasLineas = [];
        $phpEncontrado = false;
        foreach ($lineas as $linea) {
            $nuevasLineas[] = $linea;
            if (trim($linea) === '<?php' && !$phpEncontrado) {
                $nuevasLineas[] = $lineaUse;
                $phpEncontrado = true;
            }
        }
        $lineas = $nuevasLineas;
    }

    $timestamp = now()->format('Y-m-d H:i:s');
    $lineas[] = "\n// 🧩 Ruta generada automáticamente por ABM Creator";
    $lineas[] = "// Modelo: {$modeloNombre} - Generado el {$timestamp}";
    $lineas[] = $lineaRoute;

    File::put($rutaArchivo, implode("\n", $lineas));
}




private function decodificarCamposJson(Request $request)
{
    // 🧠 Si viene como array directamente
    if ($request->has('campos') && is_array($request->input('campos'))) {
        return $request->input('campos');
    }

    // 🧠 Si viene como JSON (caso desde el input hidden)
    if ($request->has('campos') && is_string($request->input('campos'))) {
        $json = $request->input('campos');
        $decoded = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
    }

    // 🔥 Si todo falla, lanzamos excepción
    throw new \Exception("Error al procesar los campos: formato inválido.");
}

}
