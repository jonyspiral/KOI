@php
    use App\Helpers\MenuHelper;
    $menu = MenuHelper::obtenerMenu();
    $moduloActivo = request()->get('modulo') ?? array_key_first($menu);
@endphp

<form method="GET" class="mb-3">
    <select name="modulo" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
        @foreach(array_keys($menu) as $modulo)
            <option value="{{ $modulo }}" @selected($modulo === $moduloActivo)>
                {{ ucfirst($modulo) }}
            </option>
        @endforeach
    </select>
</form>

@if(isset($menu[$moduloActivo]))
    <ul class="nav flex-column">
        @foreach ($menu[$moduloActivo] as $grupo => $items)
            <li class="nav-item mt-2">
                <strong class="text-uppercase text-muted small">{{ $grupo }}</strong>
            </li>
            @foreach ($items as $item)
                <li class="nav-item ps-3">
                    @php
                        try {
                            $url = route($item['ruta']);
                        } catch (\Throwable $e) {
                            continue;
                        }
                    @endphp
                    <a href="{{ $url }}" class="nav-link">
                        {!! $item['icon'] !!} {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        @endforeach
    </ul>
@else
    <p class="text-muted">No hay entradas disponibles para este módulo.</p>
@endif
