@extends('layouts.app')

@section('content')
<div class="container">
    <h2>🧙 Importar tabla desde SQL Server a MySQL</h2>

    <form action="{{ route('sistemas.importar.importar') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="tabla">📋 Seleccioná la tabla:</label>
            <select name="nombre_tabla" id="nombre_tabla" class="form-control" required>
                @foreach($tablas as $tabla)
                <option value="{{ $tabla }}" {{ isset($tablaSeleccionada) && $tablaSeleccionada == $tabla ? 'selected' : '' }}>
    {{ $tabla }}
</option>my
                @endforeach
            </select>
        </div>

        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="force_table" id="force_table">
            <label class="form-check-label" for="force_table">🧸 Forzar recreación de tabla (DROP + CREATE) <code>--force-table</code></label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="force_models" id="force_models">
            <label class="form-check-label" for="force_models">🔄 Regenerar modelos aunque ya existan <code>--force-models</code></label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="with_sql_model" id="with_sql_model">
            <label class="form-check-label" for="with_sql_model">📁 Generar modelo de SQL Server <code>--with-sql-model</code></label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="fill_all" id="fill_all">
            <label class="form-check-label" for="fill_all">🧪 Incluir todos los campos como <code>$fillable</code> <code>--fill-all</code></label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="skip_data" id="skip_data">
            <label class="form-check-label" for="skip_data">⏹️ No importar registros (solo estructura y modelos) <code>--skip-data</code></label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="insert_simple" id="insert_simple">
            <label class="form-check-label" for="insert_simple">⚡ Insertar datos sin controles (solo si estás seguro) <code>--insert-simple</code></label>
        </div>

        <button type="submit" class="btn btn-primary mt-3">🚀 Ejecutar Importación</button>


        <div class="mt-4">
    <a href="{{ route('sistemas.abms.crear') }}" class="btn btn-outline-primary">
        Ir al ABM Creator
    </a>
</div>
    </form>

    @if(!empty($output))
<div class="card mt-4">
    <div class="card-header">🧾 Salida del comando</div>
    <div class="card-body">
        <pre>{{ $output }}</pre>
    </div>
</div>
@endif
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

            <ul>
              <li>🧙 Crea la tabla en MySQL desde SQL Server</li>
              <li>📁 Genera modelos Eloquent en <code>App\Models</code> y <code>App\Models\Sql</code></li>
              <li>💰 Agrega metadatos de los campos con <code>fieldsMeta()</code></li>
              <li>🤔 Opcionalmente importa datos (puede omitirse con <code>--skip-data</code>)</li>
              <li>⚡ Usa <code>--insert-simple</code> si estás seguro de que la tabla está vacía y querés optimizar</li>
            </ul>

            <p>Comando utilizado:</p>
            <code>php artisan importar:tabla nombre_tabla [--force-table] [--force-models] [--with-sql-model] [--fill-all] [--skip-data] [--insert-simple]</code>

            <p>Ubicación del comando:</p>
            <code>app/Console/Commands/ImportarTablaKoi.php</code>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
