<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        {{ $titulo ?? 'Ficha' }}
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach ($campos as $campo => $meta)
                @if (!empty($meta['incluir']) && ($meta['input_type'] ?? '') !== 'hidden')
                    <div class="col-md-6">
                        <label for="{{ $campo }}" class="form-label">{{ $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo)) }}</label>
                        <input type="{{ $meta['input_type'] ?? 'text' }}"
                               name="{{ $campo }}"
                               id="{{ $campo }}"
                               class="form-control"
                               value="{{ old($campo, $registro->$campo ?? '') }}"
                               {{ !empty($meta['readonly']) ? 'readonly' : '' }}>
                    </div>
                @endif
            @endforeach
        </div>
    </d