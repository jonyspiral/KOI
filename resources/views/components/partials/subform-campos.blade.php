{{-- resources/views/components/partials/subform-campos.blade.php --}}
@props([
    'modelo' => '',
    'config' => [],
])

@php
    $campos = collect($config['campos'] ?? [])
        ->filter(fn($meta) => !empty($meta['incluir']) && ($meta['input_type'] ?? '') !== 'hidden')
        ->sortBy(fn($meta) => $meta['orden'] ?? 0)
        ->toArray();

    $nombre = $config['nombre'] ?? Str::snake($modelo);
    $titulo = $config['titulo'] ?? ucfirst($nombre);
@endphp

<div class="card border shadow-sm my-3" x-data="{ filas: [{}] }">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <strong>📄 {{ $titulo }}</strong>
        <button type="button" class="btn btn-sm btn-outline-primary" @click="filas.push({})">➕ Agregar</button>
    </div>
    <div class="card-body p-3">
        <template x-for="(fila, index) in filas" :key="index">
            <div class="border rounded p-3 mb-3">
                <div class="row g-2">
                    @foreach ($campos as $campo => $meta)
                        <div class="col-md-{{ $meta['ancho'] ?? 4 }}">
                            <label class="form-label">{{ $meta['label'] ?? ucfirst($campo) }}</label>
                            <input
                                :name="'{{ $nombre }}[' + index + '][{{ $campo }}]'"
                                type="{{ $meta['input_type'] ?? 'text' }}"
                                class="form-control form-control-sm"
                                :readonly="{{ json_encode(!empty($meta['readonly'])) }}"
                                :required="{{ json_encode(empty($meta['nullable'])) }}"
                            >
                        </div>
                    @endforeach
                    <div class="col-md-1 d-flex align-items-end justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-danger" @click="filas.splice(index, 1)">🗑️</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
