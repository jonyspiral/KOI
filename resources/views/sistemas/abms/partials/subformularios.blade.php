{{-- resources/views/sistemas/abms/partials/subformularios.blade.php --}}

@php
    $jsonPath = resource_path("meta_abms/config_form_{$modelo}.json");
    $subformulariosFromJson = [];

    if (File::exists($jsonPath)) {
        $json = json_decode(File::get($jsonPath), true);
        $subformulariosFromJson = $json['subformularios'] ?? [];
    }

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
    <legend class="w-auto px-2">📂 Subformularios Relacionados</legend>
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
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" @click="subformularios.splice(index, 1)">
                            🗑️ Eliminar subformulario
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="text-end mt-3">
            <button type="button" class="btn btn-outline-primary" @click="subformularios.push({
                modelo: '', tabla: '', foreign_key: '', modo: 'inline', titulo: '', carpeta_vistas: ''
            })">
                ➕ Agregar Subformulario
            </button>
        </div>
    </div>
</fieldset>
