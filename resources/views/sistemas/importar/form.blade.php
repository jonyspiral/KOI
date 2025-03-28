@extends('layouts.app')

@section('content')
<div class="container">
    <h2>🧩 Importar tabla desde SQL Server a MySQL</h2>

    <form action="{{ route('sistemas.importar.importar') }}" method="POST">   
        @csrf

        <div class="form-group">
            <label for="tabla">📋 Seleccioná la tabla:</label>
            <select name="tabla" id="tabla" class="form-control" required>
                @foreach($tablas as $tabla)
                    <option value="{{ $tabla }}">{{ $tabla }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="force_table" id="force_table">
            <label class="form-check-label" for="force_table">🧨 Forzar recreación de tabla (DROP + CREATE)</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="force_models" id="force_models">
            <label class="form-check-label" for="force_models">🔄 Regenerar modelos aunque ya existan</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="with_sql_model" id="with_sql_model">
            <label class="form-check-label" for="with_sql_model">📑 Generar modelo de SQL Server</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="fill_all" id="fill_all">
            <label class="form-check-label" for="fill_all">🧮 Incluir todos los campos como <code>$fillable</code></label>
        </div>

        <div class="form-group mt-3">
            <label for="unique">🔐 Clave única (opcional, separada por coma):</label>
            <input type="text" name="unique" id="unique" class="form-control" placeholder="campo1,campo2,...">
        </div>

        <button type="submit" class="btn btn-primary mt-3">🚀 Ejecutar Importación</button>
    </form>

    @if(session('output'))
    <div class="card mt-4">
        <div class="card-header">🧾 Salida del comando</div>
        <div class="card-body">
            <pre>{{ session('output') }}</pre>
        </div>
    </div>
    @endif

    <div class="mt-4">
        <details>
            <summary class="text-info">ℹ️ Información técnica para desarrolladores</summary>
            <div class="mt-2">
                Este formulario ejecuta el comando <code>php artisan importar:tabla</code>, que importa la estructura y los datos de una tabla de SQL Server 2000 a MySQL.
                <ul>
                    <li><strong>--force-table</strong>: elimina y recrea la tabla en MySQL.</li>
                    <li><strong>--force-models</strong>: regenera los modelos Eloquent en <code>App\Models</code> y <code>App\Models\Sql</code>.</li>
                    <li><strong>--with-sql-model</strong>: genera el modelo con conexión SQL Server.</li>
                    <li><strong>--fill-all</strong>: rellena todos los campos como <code>$fillable</code>.</li>
                    <li><strong>--unique</strong>: permite definir manualmente las claves únicas.</li>
                </ul>
            </div>
        </details>
    </div>
</div>


<hr class="mt-5">

<div class="accordion mt-3" id="accordionDesarrollador">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingInfo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInfo" aria-expanded="false" aria-controls="collapseInfo">
        🔧 Información técnica para desarrolladores
      </button>
    </h2>
    <div id="collapseInfo" class="accordion-collapse collapse" aria-labelledby="headingInfo" data-bs-parent="#accordionDesarrollador">
      <div class="accordion-body">
        <p>Este formulario permite ejecutar el comando Artisan <code>importar:tabla</code> desde la interfaz web.</p>

        <h6>🧩 ¿Qué hace este formulario?</h6>
        <ul>
          <li>Obtiene todas las tablas del SQL Server 2000 (conexión <code>sqlsrv_koi</code>).</li>
          <li>Permite seleccionar una tabla a importar a MySQL.</li>
          <li>Opcionalmente permite:</li>
          <ul>
            <li>☑️ Forzar regeneración de modelos con <code>--force-models</code></li>
            <li>☑️ Forzar recreación de tabla con <code>--force-table</code></li>
            <li>☑️ Crear modelo SQL Server con <code>--with-sql-model</code></li>
            <li>🗝️ Definir manualmente la clave única con <code>--unique</code></li>
          </ul>
          <li>Ejecuta el comando Artisan <code>importar:tabla nombre_tabla --flags</code>.</li>
          <li>Muestra la salida completa del proceso en pantalla.</li>
        </ul>

        <h6>📂 Ubicación de archivos relacionados:</h6>
        <ul>
          <li><code>app/Console/Commands/ImportarTablaKoi.php</code> — Comando principal</li>
          <li><code>app/Http/Controllers/Sistemas/Importar/ImportarController.php</code> — Controlador del formulario</li>
          <li><code>resources/views/sistemas/importar/form.blade.php</code> — Vista del formulario</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@endsection
