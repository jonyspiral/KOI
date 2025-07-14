{{-- 📦 Filtros activos visibles --}}
@if (!empty($activeFilters))
    <div class="alert alert-light border shadow-sm py-2 px-3 mb-4">
        <strong>🔎 Filtros aplicados:</strong>
        @foreach ($activeFilters as $label)
            <span class="badge bg-primary me-1">{{ $label }}</span>
        @endforeach
        <a href="?reset=1" class="btn btn-sm btn-outline-secondary float-end">❌ Borrar filtros</a>
    </div>
@endif
