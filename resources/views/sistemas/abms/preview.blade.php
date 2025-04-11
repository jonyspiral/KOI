@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🛠 Configurar campos para el modelo: {{ $modelo }}</h2>
    <form action="{{ route('sistemas.abms.configurar') }}" method="POST">
        @csrf
        <input type="hidden" name="modelo" value="{{ $modelo }}">
        <input type="hidden" name="namespace" value="{{ $namespace }}">
        <input type="hidden" name="carpeta_vistas" value="{{ $carpeta_vistas }}">

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Campo</th> 
                        <th>Label</th>
                        <th>Tipo Input</th>
                        <th>Default</th>
                        <th>Incluir</th>
                        <th>Nullable</th>
                        <th>Tabla FK</th>
                        <th>Columna FK</th>
                        <th>Label FK</th>
                        <th>Valores (select_list / checkbox)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fields as $campo => $meta)
                        @php
                            $label = $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo));
                            $inputType = $meta['input_type'] ?? 'text';
                            $default = $meta['default'] ?? '';
                            $incluir = $meta['incluir'] ?? false;
                            $nullable = $meta['nullable'] ?? false;
                            $referenced_table = $meta['referenced_table'] ?? '';
                            $referenced_column = $meta['referenced_column'] ?? 'id';
                            $referenced_label = $meta['referenced_label'] ?? 'nombre';
                            $select_list_data = $meta['select_list_data'] ?? '';
                        @endphp
                        <tr>
                            <td>{{ $campo }}</td>
                            <td><input type="text" name="campos[{{ $campo }}][label]" class="form-control form-control-sm" value="{{ $label }}"></td>
                            <td>
                                <select name="campos[{{ $campo }}][input_type]" class="form-select form-select-sm">
                                    @foreach (['text','number','date','checkbox','textarea','select','select_list','hidden','email','password','file','color','url','tel','autonumerico'] as $tipo)
                                        <option value="{{ $tipo }}" @selected($inputType === $tipo)>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="campos[{{ $campo }}][default]" class="form-control form-control-sm" value="{{ $default }}"></td>
                            <td class="text-center"><input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" @checked($incluir)></td>
                            <td class="text-center"><input type="checkbox" name="campos[{{ $campo }}][nullable]" value="1" @checked($nullable)></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_table]" class="form-control form-control-sm" value="{{ $referenced_table }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_column]" class="form-control form-control-sm" value="{{ $referenced_column }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_label]" class="form-control form-control-sm" value="{{ $referenced_label }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][select_list_data]" class="form-control form-control-sm" value="{{ $select_list_data }}"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- 📂 Subformularios relacionados --}}
        @php
    // Detecta el archivo JSON si existe para este modelo
    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");
    $subformulariosFromJson = [];

    if (File::exists($jsonPath)) {
        $json = json_decode(File::get($jsonPath), true);
        $subformulariosFromJson = $json['subformularios'] ?? [];
    }

    // Precedencia: datos reenviados → desde el json → array vacío por defecto
    $subformulariosData = old('subformularios') ?? $subformulariosFromJson ?? [[
        'modelo' => '',
        'tabla' => '',
        'foreign_key' => '',
        'modo' => 'inline',
        'titulo' => '',
        'carpeta_vistas' => ''
    ]];
@endphp

        <fieldset class="border rounded p-3 mt-4">
            <legend class="w-auto px-2">📂 Subformularios relacionados</legend>
            <div x-data="{ subformularios: @js($subformulariosData) }">
                <template x-for="(sub, index) in subformularios" :key="index">
                    <div class="border rounded p-2 mb-3 bg-light">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label>🧬 Modelo hijo</label>
                                <input type="text" class="form-control" :name="`subformularios[${index}][modelo]`" x-model="sub.modelo">
                            </div>
                            <div class="col-md-3">
                                <label>📂 Tabla</label>
                                <input type="text" class="form-control" :name="`subformularios[${index}][tabla]`" x-model="sub.tabla">
                            </div>
                            <div class="col-md-3">
                                <label>🔗 Foreign Key</label>
                                <input type="text" class="form-control" :name="`subformularios[${index}][foreign_key]`" x-model="sub.foreign_key">
                            </div>
                            <div class="col-md-3">
                                <label>🧽 Modo</label>
                                <select class="form-select" :name="`subformularios[${index}][modo]`" x-model="sub.modo">
                                    <option value="inline">Inline</option>
                                    <option value="modal">Modal</option>
                                    <option value="tab">Tab</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>🏷️ Título (opcional)</label>
                                <input type="text" class="form-control" :name="`subformularios[${index}][titulo]`" x-model="sub.titulo">
                            </div>
                            <div class="col-md-6">
                                <label>📁 Carpeta Vistas</label>
                                <input type="text" class="form-control" :name="`subformularios[${index}][carpeta_vistas]`" x-model="sub.carpeta_vistas">
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" @click="subformularios.splice(index, 1)">🗑️ Eliminar subformulario</button>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="text-end">
                    <button type="button" class="btn btn-outline-primary" @click="subformularios.push({modelo: '', tabla: '', foreign_key: '', modo: 'inline', titulo: '', carpeta_vistas: ''})">
                        ➕ Agregar Subformulario
                    </button>
                </div>
            </div>
        </fieldset>

        <input type="checkbox" name="force_controlador" value="1"> Reemplazar controlador existente

        <div class="mt-3">
            <label><input type="checkbox" name="timestamps" value="1" @checked(session('timestamps', true))> Usar timestamps</label><br>
            <label><input type="checkbox" name="sincronizable" value="1" @checked(session('sincronizable', false))> Es sincronizable</label>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">📏 Guardar configuración</button>
        </div>
    </form>
</div>
{{-- 🧭 Instrucciones del módulo --}}
<div class="mt-4">
    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#infoPreview" aria-expanded="false">
        ¿Cómo funciona esta pantalla?
    </button>
    <div class="collapse" id="infoPreview">
        <div class="card card-body mt-2">
            <h5 class="mb-2">🛠 Configurar campos del ABM</h5>
            <ul class="mb-1">
                <li>Desde aquí podés ajustar manualmente cómo se verá el formulario del modelo <strong>{{ $modelo }}</strong>.</li>
                <li>Podés definir subformularios relacionados en diferentes modos: <strong>Inline</strong>, <strong>Modal</strong> o <strong>Tab</strong>. <span class="text-muted">(modal/tab en construcción)</span></li>
                <li>Activando “Reemplazar controlador existente”, se generará un nuevo controlador sin regenerar todo el ABM. <span class="text-muted">(en construcción)</span></li>
                <li>Los campos `$fillable` también pueden generarse automáticamente desde acá. <span class="text-muted">(en construcción)</span></li>
            </ul>
            <small class="text-muted">Esta sección forma parte de la herramienta de generación modular y avanzada de ABMs en KOI.</small>
        </div>
    </div>
</div>

@endsection
