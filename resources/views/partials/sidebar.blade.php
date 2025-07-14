<aside class="main-sidebar sidebar-dark-primary elevation-4">
    {{-- Logo --}}
    <a href="{{ url('/') }}" class="brand-link">
        <span class="brand-text font-weight-light">KOI</span>
    </a>

    {{-- Menú lateral --}}
    <div class="sidebar">
        {{-- Usuario --}}
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name ?? 'Invitado' }}</a>
            </div>
        </div>

        {{-- Menú dinámico --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">

             @foreach(config('menu') as $modulo => $items)
                <li class="nav-header">{{ strtoupper($modulo) }}</li>
                @foreach($items as $item)
                    @php
                        $routeName = $item['ruta'];
                        $url = Route::has($routeName) ? route($routeName) : '#';
                    @endphp
                    <li class="nav-item">
                        <a href="{{ $url }}" class="nav-link {{ request()->routeIs($routeName) ? 'active' : '' }}">
                            <i class="nav-icon {{ $item['icono'] ?? 'fas fa-circle' }}"></i>

                            <p>{{ $item['nombre'] }}</p>
                        </a>
                    </li>
                @endforeach
            @endforeach


            </ul>
        </nav>
    </div>
</aside>
