@props(['registro', 'subform'])

@php
    use Illuminate\Support\Facades\DB;

    $tabla = $subform['tabla'];
    $modeloHijo = $subform['modelo'];
    $foreignKey = $subform['foreign_key'];
    $modo = $subform['modo'] ?? 'inline';
    $titulo = $subform['titulo'] ?? ucfirst(str_replace('_', ' ', $tabla));
    $rutaBase = $subform['ruta'] ?? throw new \Exception("Falta definir 'ruta' en el subformulario de $modeloHijo");

    $camposSub = [];
    try {
        $config = json_decode(file_get_contents(resource_path("meta_abms/config_form_{$modeloHijo}.json")), true);
        $camposRaw = $config['campos'] ?? [];
        $camposSub = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $primaryKey = $config['primary_key'] ?? 'id';
    } catch (\Throwable $e) {
        echo "<div class='alert alert-danger'>Error cargando campos del subformulario: {$e->getMessage()}</div>";
    }

    $registrosSub = collect();
    try {
        $modeloClass = "App\\Models\\{$modeloHijo}";
        $valorFK = $registro->{$foreignKey};
        $registrosSub = $modeloClass::where($foreignKey, $valorFK)->get();
    } catch (\Throwable $e) {
        echo "<div class='alert alert-danger'>Error cargando subregistros: {$e->getMessage()}</div>";
    }
@endphp

<div class="bg-light border-start border-2 border-secondary p-2 mt-1">
    <h6 class="text-muted small mb-3">{{ $titulo }}</h6>

    <div x-data="{ showForm: true, showTable: true }">
        
        {{-- Formulario de creación --}}
        <div x-show="showForm" class="mb-3">
            <form action="{{ route("produccion.abms.{$rutaBase}.store") }}" method="POST">
                @csrf
                <input type="hidden" name="{{ $foreignKey }}" value="{{ $registro->$foreignKey }}">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            @foreach ($camposSub as $campo => $meta)
                                @if ($campo !== $foreignKey)
                                    <th>{{ $meta['label'] }}</th>
                                @endif
                            @endforeach
                            <th>💾</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach ($camposSub as $campo => $meta)
                                @if ($campo === $foreignKey) @continue @endif
                                <td>
                                    @include('components.koi-subformulario-create-field', [
                                        'campo' => $campo,
                                        'meta' => $meta,
                                        'registro' => $registro
                                    ])
                                </td>
                            @endforeach
                            <td class="text-center">
                                <button type="submit" class="btn btn-success btn-sm">💾</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

        {{-- Tabla de subregistros --}}
        <div x-show="showTable">
            @if ($registrosSub->count())
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                @foreach ($camposSub as $campo => $meta)
                                    @if ($campo !== $foreignKey)
                                        <th>{{ $meta['label'] }}</th>
                                    @endif
                                @endforeach
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrosSub as $sub)
                                <tr x-data="{ editing: false }">
                                    @foreach ($camposSub as $campo => $meta)
                                        @if ($campo === $foreignKey) @continue @endif
                                        <td>
                                            <div x-show="!editing">
                                                <span>
                                                    @include('components.koi-subformulario-view-field', [
                                                        'campo' => $campo,
                                                        'meta' => $meta,
                                                        'sub' => $sub
                                                    ])
                                                </span>
                                            </div>
                                            <div x-show="editing">
                                                @include('components.koi-subformulario-edit-field', [
                                                    'campo' => $campo,
                                                    'meta' => $meta,
                                                    'sub' => $sub
                                                ])
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="text-center">
                                        <div x-show="!editing">
                                            <div class="d-flex gap-1">
                                                <button @click="editing = true" class="btn btn-sm btn-primary" type="button">✏️</button>
                                                <form action="{{ route("produccion.abms.{$rutaBase}.destroy", $sub->{$primaryKey}) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmar eliminación?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="{{ $foreignKey }}" value="{{ $registro->$foreignKey }}">
                                                    <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div x-show="editing">
                                            <form id="edit-form-{{ $sub->{$primaryKey} }}" action="{{ route("produccion.abms.{$rutaBase}.update", $sub->{$primaryKey}) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="{{ $foreignKey }}" value="{{ $registro->$foreignKey }}">
                                                <div class="d-flex gap-1">
                                                    <button type="submit" class="btn btn-sm btn-success">💾</button>
                                                    <button type="button" class="btn btn-sm btn-secondary" @click="editing = false">❌</button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $sub->{$primaryKey} }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
