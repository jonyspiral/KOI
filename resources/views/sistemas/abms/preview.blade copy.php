@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">🛠 Campos para el modelo: {{ $modelo }}</h2>
    <form action="{{ route('sistemas.abms.configurar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modelo" value="{{ $modelo }}">
                    <input type="hidden" name="namespace" value="{{ $namespace }}">
                    <input type="hidden" name="carpeta_vistas" value="{{ $carpeta_vistas }}">
                    <input type="hidden" name="primary_key" value="{{ $primary_key }}">
                    <input type="hidden" name="primary_key_sql" value="{{ implode(',', $primary_key_sql ?? []) }}">
                    <div class="table-responsive">
        <table class="table table-bordered table-sm">
                        <thead class="table-light">
                
                    <tr>
                    <th>Campo</th> 
                    <th>Tipo SQL</th> 
                    <th>Label</th>
                    <th>Tipo Input</th>
                    <th>Default</th>
                    <th class="text-center">
                        Incluir
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-incluir ms-2"
                                onclick="toggleIncluir(this)" title="Activar/Desactivar todos los incluir">
                            🔁
                        </button>
                    </th>
                    <th>Sync</th> {{-- 🆕 --}}
                    <th>Nullable</th>
                    <th>Tabla FK</th>
                    <th>Columna FK</th>
                    <th>Label FK</th>
                    <th>
                        Valores
                        <span tabindex="0" class="ms-1 text-muted" data-bs-toggle="tooltip" data-bs-html="true"
                            title="<code>Etiqueta=Valor</code> separados por coma<br>
                                Ejemplos:<br>
                                🔘 Checkbox: <code>Sí=S,No=N</code><br>
                                🔽 Select list: <code>Interna=1,Externa=2</code>">
                            ℹ️
                        </span>
                    </th>
                    <th>Tab</th> {{-- 🆕 Nuevo --}}
                    </tr>
                    </tr>
                    </thead>

            <tbody>
                @php
                    $modelSql = "\\App\\Models\\Sql\\{$modelo}";
                    $fieldsMeta = method_exists($modelSql, 'fieldsMeta') ? $modelSql::fieldsMeta() : [];
                @endphp
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
                    <td class="text-muted">
                        {{ $fieldsMeta[$campo]['type'] ?? 'n/a' }}
                    </td>
                    <td><input type="text" name="campos[{{ $campo }}][label]" class="form-control form-control-sm" value="{{ $label }}"></td>
                    <td>
                        <select name="campos[{{ $campo }}][input_type]" class="form-select form-select-sm">
                            @foreach (['text','number','decimal','moneda','date','checkbox','textarea','select','select_list','hidden','email','password','file','color','url','tel','autonumerico'] as $tipo)
                                <option value="{{ $tipo }}" @selected($inputType === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="campos[{{ $campo }}][default]" class="form-control form-control-sm" value="{{ $default }}"></td>

                    <td class="text-center">
                        <input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" @checked($incluir)>
                    </td>

                    <td class="text-center">
                        <input type="hidden" name="campos[{{ $campo }}][sync]" value="0">
                        <input type="checkbox" name="campos[{{ $campo }}][sync]" value="1" @checked(!empty($meta['sync']))>
                    </td>

                    <td class="text-center">
                        <input type="checkbox" name="campos[{{ $campo }}][nullable]" value="1" @checked($nullable)>
                    </td>

                    <td><input type="text" name="campos[{{ $campo }}][referenced_table]" class="form-control form-control-sm" value="{{ $referenced_table }}"></td>
                    <td><input type="text" name="campos[{{ $campo }}][referenced_column]" class="form-control form-control-sm" value="{{ $referenced_column }}"></td>
                    <td><input type="text" name="campos[{{ $campo }}][referenced_label]" class="form-control form-control-sm" value="{{ $referenced_label }}"></td>
                    <td><input type="text" name="campos[{{ $campo }}][select_list_data]" class="form-control form-control-sm" value="{{ $select_list_data }}"></td>
                   
                </tr>
                @endforeach
                </tbody>
                @extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">🛠 Campos para el modelo: {{ $modelo }}</h2>
    <form action="{{ route('sistemas.abms.configurar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modelo" value="{{ $modelo }}">
                    <input type="hidden" name="namespace" value="{{ $namespace }}">
                    <input type="hidden" name="carpeta_vistas" value="{{ $carpeta_vistas }}">
                    <input type="hidden" name="primary_key" value="{{ $primary_key }}">
                    <input type="hidden" name="primary_key_sql" value="{{ implode(',', $primary_key_sql ?? []) }}">
                    <div class="table-responsive">
        <table class="table table-bordered table-sm">
                        <thead class="table-light">
                
                    <tr>
                    <th>Campo</th> 
                    <th>Tipo SQL</th> 
                    <th>Label</th>
                    <th>Tipo Input</th>
                    <th>Default</th>
                    <th class="text-center">
                        Incluir
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-incluir ms-2"
                                onclick="toggleIncluir(this)" title="Activar/Desactivar todos los incluir">
                            🔁
                        </button>
                    </th>
                    <th>Sync</th> {{-- 🆕 --}}
                    <th>Nullable</th>
                    <th>Tabla FK</th>
                    <th>Columna FK</th>
                    <th>Label FK</th>
                    <th>
                        Valores
                        <span tabindex="0" class="ms-1 text-muted" data-bs-toggle="tooltip" data-bs-html="true"
                            title="<code>Etiqueta=Valor</code> separados por coma<br>
                                Ejemplos:<br>
                                🔘 Checkbox: <code>Sí=S,No=N</code><br>
                                🔽 Select list: <code>Interna=1,Externa=2</code>">
                            ℹ️
                        </span>
                    </th>
                    <th>Tab</th> {{-- 🆕 Nuevo --}}
                    </tr>
                    </tr>
                    </thead>

            <tbody>
                @php
                    $modelSql = "\\App\\Models\\Sql\\{$modelo}";
                    $fieldsMeta = method_exists($modelSql, 'fieldsMeta') ? $modelSql::fieldsMeta() : [];
                @endphp
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
                    <td class="text-muted">
                        {{ $fieldsMeta[$campo]['type'] ?? 'n/a' }}
                    </td>
                    <td><input type="text" name="campos[{{ $campo }}][label]" class="form-control form-control-sm" value="{{ $label }}"></td>
                    <td>
                        <select name="campos[{{ $campo }}][input_type]" class="form-select form-select-sm">
                            @foreach (['text','number','decimal','moneda','date','checkbox','textarea','select','select_list','hidden','email','password','file','color','url','tel','autonumerico'] as $tipo)
                                <option value="{{ $tipo }}" @selected($inputType === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="campos[{{ $campo }}][default]" class="form-control form-control-sm" value="{{ $default }}"></td>

                    <td class="text-center">
                        <input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" @checked($incluir)>
                    </td>

                    <td class="text-center">
                        <input type="hidden" name="campos[{{ $campo }}][sync]" value="0">
                        <input type="checkbox" name="campos[{{ $campo }}][sync]" value="1" @checked(!empty($meta['sync']))>
                    </td>

                    <td class="text-center">
                        <input type="checkbox" name="campos[{{ $campo }}][nullable]" value="1" @checked($nullable)>
                    </td>

                    <td><input type="text" name="campos[{{ $campo }}][referenced_table]" class="form-control form-control-sm" value="{{ $referenced_table }}"></td>
                    <td><input type="text" name="campos[{{ $campo }}][referenced_column]" class="form-control form-control-sm" value="{{ $referenced_column }}"></td>
                    <td><input type="text" name="campos[{{ $campo }}][referenced_label]" class="form-control form-control-sm" value="{{ $referenced_label }}"></td>
                    <td><input type="text" name="campos[{{ $campo }}][select_list_data]" class="form-control form-control-sm" value="{{ $select_list_data }}"></td>
                    <td>
                    {{-- Select dinámico de Tabs --}}
                
                    <select name="campos[{{ $campo }}][tab]" class="form-select form-select-sm">
                        <option value="general" {{ (old("campos.$campo.tab", $meta['tab'] ?? 'general') == 'general') ? 'selected' : '' }}>General</option>
                        <option value="tecnicos" {{ (old("campos.$campo.tab", $meta['tab'] ?? '') == 'tecnicos') ? 'selected' : '' }}>Atributos Técnicos</option>
                        <option value="produccion" {{ (old("campos.$campo.tab", $meta['tab'] ?? '') == 'produccion') ? 'selected' : '' }}>Producción</option>
                        <option value="ecommerce" {{ (old("campos.$campo.tab", $meta['tab'] ?? '') == 'ecommerce') ? 'selected' : '' }}>E-commerce</option>
                    </select>
                    </td>
                </tr>
                @endforeach
                </tbody>

    </table>
</div>

        
        <fieldset class="border rounded p-3 mt-4">
            

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">🧱 Formato del Índice</label>
                    <select name="form_config[index_view_type]" class="form-select">
                        <option value="default" {{ ($form_config['index_view_type'] ?? 'default') == 'default' ? 'selected' : '' }}>Clásico</option>
                        <option value="inline" {{ ($form_config['index_view_type'] ?? 'default') == 'inline' ? 'selected' : '' }}>Inline</option>
                        <option value="inline-tab" {{ ($form_config['index_view_type'] ?? 'default') == 'inline-tab' ? 'selected' : '' }}>Inline Tab</option>
                        <option value="tab" {{ ($form_config['index_view_type'] ?? 'default') == 'tab' ? 'selected' : '' }}>Pestañas</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="form_view_type" class="form-label">🧩 Formato del Create/Edit</label>
                    <select name="form_config[form_view_type]" id="form_view_type" class="form-select">
                        <option value="default" {{ ($form_config['form_view_type'] ?? 'default') == 'default' ? 'selected' : '' }}>Pantalla Completa</option>
                        <option value="inline" {{ ($form_config['form_view_type'] ?? 'default') == 'inline' ? 'selected' : '' }}>Inline (en tabla)</option>
                        <option value="modal" {{ ($form_config['form_view_type'] ?? 'default') == 'modal' ? 'selected' : '' }}>Modal (experimental)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="usa_paginador" class="form-label">📚 Usar paginador</label>
                    <select name="form_config[usa_paginador]" id="usa_paginador" class="form-select">
                        <option value="1" {{ old('form_config.usa_paginador', $form_config['usa_paginador'] ?? '1') == '1' ? 'selected' : '' }}>✅ Sí</option>
                        <option value="0" {{ old('form_config.usa_paginador', $form_config['usa_paginador'] ?? '1') == '0' ? 'selected' : '' }}>❌ No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="per_page" class="form-label">📦 Registros por página</label>
                    <input type="number" name="form_config[per_page]" id="per_page" class="form-control"
                        min="1" max="500" value="{{ old('form_config.per_page', $form_config['per_page'] ?? 100) }}">
                </div>

                <div class="col-md-6">
                    <label for="form_name" class="form-label">🆔 Nombre del Formulario</label>
                    <input type="text" name="form_config[form_name]" id="form_name" class="form-control"
                        value="{{ old('form_config.form_name', $form_config['form_name'] ?? $modelo) }}"
                        placeholder="Ej: ABM_Marcas_v1">
                    <small class="text-muted">Nombre técnico o identificador del formulario para uso interno.</small>
                </div>

                <div class="col-md-6">
                    <label for="form_route" class="form-label">📍 Ruta del Formulario <span class="text-danger">*</span></label>
                    <input type="text" name="form_config[form_route]" id="form_route" class="form-control" required
                        value="{{ old('form_config.form_route', $form_config['form_route'] ?? '') }}"
                        placeholder="Ej: produccion/abms/marcas">
                    <small class="text-muted">Usada para generar automáticamente las rutas web.</small>
                </div>
            </div>
        </fieldset>



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
        <legend class="w-auto px-2">🧾 Configuración del Formulario Principal</legend>

{{-- Checkbox visible --}}
<div class="form-check mt-3">
<input type="hidden" name="force_controlador" value="0">
    <input class="form-check-input" type="checkbox" name="force_controlador" id="force_controlador" value="1" {{ old('force_controlador') ? 'checked' : '' }}>
    <label class="form-check-label" for="force_controlador">
        Reemplazar controlador existente
    </label>
</div>
      

        <div class="mt-3">
            <label><input type="checkbox" name="timestamps" value="1" @checked(session('timestamps', true))> Usar timestamps</label><br>
            <label><input type="checkbox" name="sincronizable" value="1" @checked(session('sincronizable', false))> Es sincronizable</label>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">📏 Guardar configuración</button>
        </div>
    </form>

    <hr>
<h4 class="mt-4">🧭 Configuración de Menú</h4>

<div id="menu-configuracion">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mostrar</th>
                <th>Label</th>
                <th>Grupo</th>
                <th>Módulo</th>
                <th>Ícono</th>
                <th>Posición</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="menu-entries">
            {{-- Esto se completará dinámicamente vía JS --}}
        </tbody>
    </table>
    <button class="btn btn-sm btn-primary" onclick="agregarEntradaMenu()">➕ Agregar entrada</button>
</div>

@php
    $menuDefault = [
        [
            'mostrar' => true,
            'label' => $config['modelo'] ?? 'SinNombre',
            'icon' => '📄',
            'modulo' => strtolower($config['namespace'] ?? 'general'),
            'grupo' => ucfirst($config['namespace'] ?? 'General'),
            'posicion' => 99,
        ]
    ];
    $menuData = $config['menu'] ?? $menuDefault;
    
@endphp

<script>
    let entradasMenu = @json($menuData);


    function renderMenuEntries() {
        const tbody = document.getElementById('menu-entries');
        tbody.innerHTML = '';
        entradasMenu.forEach((entrada, index) => {
            tbody.innerHTML += `
                <tr>
                    <td><input type="checkbox" ${entrada.mostrar ? 'checked' : ''} onchange="actualizarCampo(${index}, 'mostrar', this.checked)"></td>
                    <td><input class="form-control form-control-sm" value="${entrada.label}" onchange="actualizarCampo(${index}, 'label', this.value)"></td>
                    <td><input class="form-control form-control-sm" value="${entrada.grupo}" onchange="actualizarCampo(${index}, 'grupo', this.value)"></td>
                    <td><input class="form-control form-control-sm" value="${entrada.modulo}" onchange="actualizarCampo(${index}, 'modulo', this.value)"></td>
                    <td><input class="form-control form-control-sm" value="${entrada.icon}" onchange="actualizarCampo(${index}, 'icon', this.value)"></td>
                    <td><input type="number" class="form-control form-control-sm" value="${entrada.posicion}" onchange="actualizarCampo(${index}, 'posicion', this.value)"></td>
                    <td><button class="btn btn-sm btn-danger" onclick="eliminarEntradaMenu(${index})">🗑️</button></td>
                </tr>
            `;
        });
        guardarJSONMenu();
    }

    function actualizarCampo(index, campo, valor) {
        entradasMenu[index][campo] = valor;
        guardarJSONMenu();
    }

    function agregarEntradaMenu() {
        entradasMenu.push({
            mostrar: true,
            label: 'Nuevo',
            icon: '📄',
            modulo: '{{ strtolower($config["namespace"] ?? "general") }}',
            grupo: '{{ ucfirst($config["namespace"] ?? "General") }}',
            posicion: 99
        });
        renderMenuEntries();
    }

    function eliminarEntradaMenu(index) {
        if (confirm('¿Eliminar esta entrada de menú?')) {
            entradasMenu.splice(index, 1);
            renderMenuEntries();
        }
    }

    function guardarJSONMenu() {
        // Sobrescribe el contenido oculto que ya usamos en el ABM Creator
        const input = document.querySelector('input[name="menu_json"]');
        if (input) {
            input.value = JSON.stringify(entradasMenu);
        } else {
            const nuevoInput = document.createElement('input');
            nuevoInput.type = 'hidden';
            nuevoInput.name = 'menu_json';
            nuevoInput.value = JSON.stringify(entradasMenu);
            document.forms[0].appendChild(nuevoInput);
        }
    }

    renderMenuEntries();



</script>
<script>
function toggleIncluir(btn) {
    const checkboxes = document.querySelectorAll('input[name^="campos"][name$="[incluir]"]');
    const algunoMarcado = Array.from(checkboxes).some(cb => cb.checked);

    checkboxes.forEach(cb => cb.checked = !algunoMarcado);
}
</script>
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
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltips].forEach(el => new bootstrap.Tooltip(el));
    });
</script>
@endpush
    </table>
</div>

        
        <fieldset class="border rounded p-3 mt-4">
            

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">🧱 Formato del Índice</label>
                    <select name="form_config[index_view_type]" class="form-select">
                        <option value="default" {{ ($form_config['index_view_type'] ?? 'default') == 'default' ? 'selected' : '' }}>Clásico</option>
                        <option value="inline" {{ ($form_config['index_view_type'] ?? 'default') == 'inline' ? 'selected' : '' }}>Inline</option>
                        <option value="inline-tab" {{ ($form_config['index_view_type'] ?? 'default') == 'inline-tab' ? 'selected' : '' }}>Inline Tab</option>
                        <option value="tab" {{ ($form_config['index_view_type'] ?? 'default') == 'tab' ? 'selected' : '' }}>Pestañas</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="form_view_type" class="form-label">🧩 Formato del Create/Edit</label>
                    <select name="form_config[form_view_type]" id="form_view_type" class="form-select">
                        <option value="default" {{ ($form_config['form_view_type'] ?? 'default') == 'default' ? 'selected' : '' }}>Pantalla Completa</option>
                        <option value="inline" {{ ($form_config['form_view_type'] ?? 'default') == 'inline' ? 'selected' : '' }}>Inline (en tabla)</option>
                        <option value="modal" {{ ($form_config['form_view_type'] ?? 'default') == 'modal' ? 'selected' : '' }}>Modal (experimental)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="usa_paginador" class="form-label">📚 Usar paginador</label>
                    <select name="form_config[usa_paginador]" id="usa_paginador" class="form-select">
                        <option value="1" {{ old('form_config.usa_paginador', $form_config['usa_paginador'] ?? '1') == '1' ? 'selected' : '' }}>✅ Sí</option>
                        <option value="0" {{ old('form_config.usa_paginador', $form_config['usa_paginador'] ?? '1') == '0' ? 'selected' : '' }}>❌ No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="per_page" class="form-label">📦 Registros por página</label>
                    <input type="number" name="form_config[per_page]" id="per_page" class="form-control"
                        min="1" max="500" value="{{ old('form_config.per_page', $form_config['per_page'] ?? 100) }}">
                </div>

                <div class="col-md-6">
                    <label for="form_name" class="form-label">🆔 Nombre del Formulario</label>
                    <input type="text" name="form_config[form_name]" id="form_name" class="form-control"
                        value="{{ old('form_config.form_name', $form_config['form_name'] ?? $modelo) }}"
                        placeholder="Ej: ABM_Marcas_v1">
                    <small class="text-muted">Nombre técnico o identificador del formulario para uso interno.</small>
                </div>

                <div class="col-md-6">
                    <label for="form_route" class="form-label">📍 Ruta del Formulario <span class="text-danger">*</span></label>
                    <input type="text" name="form_config[form_route]" id="form_route" class="form-control" required
                        value="{{ old('form_config.form_route', $form_config['form_route'] ?? '') }}"
                        placeholder="Ej: produccion/abms/marcas">
                    <small class="text-muted">Usada para generar automáticamente las rutas web.</small>
                </div>
            </div>
        </fieldset>



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
        <legend class="w-auto px-2">🧾 Configuración del Formulario Principal</legend>

{{-- Checkbox visible --}}
<div class="form-check mt-3">
<input type="hidden" name="force_controlador" value="0">
    <input class="form-check-input" type="checkbox" name="force_controlador" id="force_controlador" value="1" {{ old('force_controlador') ? 'checked' : '' }}>
    <label class="form-check-label" for="force_controlador">
        Reemplazar controlador existente
    </label>
</div>
      

        <div class="mt-3">
            <label><input type="checkbox" name="timestamps" value="1" @checked(session('timestamps', true))> Usar timestamps</label><br>
            <label><input type="checkbox" name="sincronizable" value="1" @checked(session('sincronizable', false))> Es sincronizable</label>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">📏 Guardar configuración</button>
        </div>
    </form>

    <hr>
<h4 class="mt-4">🧭 Configuración de Menú</h4>

<div id="menu-configuracion">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mostrar</th>
                <th>Label</th>
                <th>Grupo</th>
                <th>Módulo</th>
                <th>Ícono</th>
                <th>Posición</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="menu-entries">
            {{-- Esto se completará dinámicamente vía JS --}}
        </tbody>
    </table>
    <button class="btn btn-sm btn-primary" onclick="agregarEntradaMenu()">➕ Agregar entrada</button>
</div>

@php
    $menuDefault = [
        [
            'mostrar' => true,
            'label' => $config['modelo'] ?? 'SinNombre',
            'icon' => '📄',
            'modulo' => strtolower($config['namespace'] ?? 'general'),
            'grupo' => ucfirst($config['namespace'] ?? 'General'),
            'posicion' => 99,
        ]
    ];
    $menuData = $config['menu'] ?? $menuDefault;
    
@endphp

<script>
    let entradasMenu = @json($menuData);


    function renderMenuEntries() {
        const tbody = document.getElementById('menu-entries');
        tbody.innerHTML = '';
        entradasMenu.forEach((entrada, index) => {
            tbody.innerHTML += `
                <tr>
                    <td><input type="checkbox" ${entrada.mostrar ? 'checked' : ''} onchange="actualizarCampo(${index}, 'mostrar', this.checked)"></td>
                    <td><input class="form-control form-control-sm" value="${entrada.label}" onchange="actualizarCampo(${index}, 'label', this.value)"></td>
                    <td><input class="form-control form-control-sm" value="${entrada.grupo}" onchange="actualizarCampo(${index}, 'grupo', this.value)"></td>
                    <td><input class="form-control form-control-sm" value="${entrada.modulo}" onchange="actualizarCampo(${index}, 'modulo', this.value)"></td>
                    <td><input class="form-control form-control-sm" value="${entrada.icon}" onchange="actualizarCampo(${index}, 'icon', this.value)"></td>
                    <td><input type="number" class="form-control form-control-sm" value="${entrada.posicion}" onchange="actualizarCampo(${index}, 'posicion', this.value)"></td>
                    <td><button class="btn btn-sm btn-danger" onclick="eliminarEntradaMenu(${index})">🗑️</button></td>
                </tr>
            `;
        });
        guardarJSONMenu();
    }

    function actualizarCampo(index, campo, valor) {
        entradasMenu[index][campo] = valor;
        guardarJSONMenu();
    }

    function agregarEntradaMenu() {
        entradasMenu.push({
            mostrar: true,
            label: 'Nuevo',
            icon: '📄',
            modulo: '{{ strtolower($config["namespace"] ?? "general") }}',
            grupo: '{{ ucfirst($config["namespace"] ?? "General") }}',
            posicion: 99
        });
        renderMenuEntries();
    }

    function eliminarEntradaMenu(index) {
        if (confirm('¿Eliminar esta entrada de menú?')) {
            entradasMenu.splice(index, 1);
            renderMenuEntries();
        }
    }

    function guardarJSONMenu() {
        // Sobrescribe el contenido oculto que ya usamos en el ABM Creator
        const input = document.querySelector('input[name="menu_json"]');
        if (input) {
            input.value = JSON.stringify(entradasMenu);
        } else {
            const nuevoInput = document.createElement('input');
            nuevoInput.type = 'hidden';
            nuevoInput.name = 'menu_json';
            nuevoInput.value = JSON.stringify(entradasMenu);
            document.forms[0].appendChild(nuevoInput);
        }
    }

    renderMenuEntries();



</script>
<script>
function toggleIncluir(btn) {
    const checkboxes = document.querySelectorAll('input[name^="campos"][name$="[incluir]"]');
    const algunoMarcado = Array.from(checkboxes).some(cb => cb.checked);

    checkboxes.forEach(cb => cb.checked = !algunoMarcado);
}
</script>
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
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltips].forEach(el => new bootstrap.Tooltip(el));
    });
</script>
@endpush