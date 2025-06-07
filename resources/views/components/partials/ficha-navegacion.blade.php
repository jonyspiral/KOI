<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="🔍 Buscar...">
        <button class="btn btn-outline-primary" type="submit">Buscar</button>
        @if(request('buscar'))
            <a href="{{ route(Route::currentRouteName()) }}" class="btn btn-outline-secondary">Limpiar</a>
        @endif
    </form>

    <div class="btn-group">
        <a href="{{ route(Route::currentRouteName(), ['registro_anterior' => $registro?->id]) }}" class="btn btn-outline-dark">⬅️</a>
        <a href="{{ route(Route::currentRouteName(), ['registro_siguiente' => $registro?->id]) }}" class="btn btn-outline-dark">➡️</a>
    </div>
</div>
