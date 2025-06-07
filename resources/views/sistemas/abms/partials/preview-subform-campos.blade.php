{{-- 🧩 Vista previa de los subformularios y configuración de campos --}}
@if (!empty($configJson['subformularios']))
    <hr class="my-4">
    <h5>🔀 Subformularios: configuración y campos</h5>

    @foreach ($configJson['subformularios'] as $i => $subform)
        @php
            $modeloSub = $subform['modelo'] ?? 'ModeloSinNombre';
            $camposSub = $subform['campos'] ?? [];

            // Intentar cargar campos automáticamente si están vacíos
            if (empty($camposSub)) {
                $jsonPath = resource_path("meta_abms/config_form_{$modeloSub}.json");
                if (\Illuminate\Support\Facades\File::exists($jsonPath)) {
                    $contenido = json_decode(\Illuminate\Support\Facades\File::get($jsonPath), true);
                    $camposSub = $contenido['campos'] ?? [];
                }
            }
        @endphp

        {{-- 🧱 Bloque de subformulario --}}
        <div class="card mb-4 border-secondary">
            <div class="card-header bg-light">
                <strong>📦 Subformulario: {{ $modeloSub }}</strong>
            </div>

            {{-- Config general --}}
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">🧩 Modelo</label>
                        <input type="text" name="subformularios[{{ $i }}][modelo]" value="{{ $modeloSub }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">🔗 Foreign Key</label>
                        <input type="text" name="subformularios[{{ $i }}][foreign_key]" value="{{ $subform['foreign_key'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">🧱 Carpeta Vistas</label>
                        <input type="text" name="subformularios[{{ $i }}][carpeta_vistas]" value="{{ $subform['carpeta_vistas'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">📛 Nombre lógico</label>
                        <input type="text" name="subformularios[{{ $i }}][nombre]" value="{{ $subform['nombre'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">📋 Título</label>
                        <input type="text" name="subformularios[{{ $i }}][titulo]" value="{{ $subform['titulo'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">⚙️ Modo</label>
                        <select name="subformularios[{{ $i }}][modo]" class="form-select form-select-sm">
                            @foreach (['inline', 'modal', 'tab'] as $modo)
                                <option value="{{ $modo }}" @selected(($subform['modo'] ?? '') === $modo)>{{ ucfirst($modo) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">🎛 View Type</label>
                        <select name="subformularios[{{ $i }}][view_type]" class="form-select form-select-sm">
                            @foreach (['default', 'inline', 'tab', 'modal'] as $type)
                                <option value="{{ $type }}" @selected(($subform['view_type'] ?? 'default') === $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Campos --}}
            <div class="card-footer p-0">
                <button type="button" class="btn btn-link w-100 text-start" data-bs-toggle="collapse" data-bs-target="#camposSubform{{ $i }}">
                    🧬 Ver campos del modelo {{ $modeloSub }}
                </button>
                <div class="collapse" id="camposSubform{{ $i }}">
                    @include('sistemas.abms.partials.subform-campos-editor', [
                        'camposSub' => $camposSub,
                        'indexSubform' => $i
                    ])
                </div>
            </div>
        </div>
    @endforeach
@endif
