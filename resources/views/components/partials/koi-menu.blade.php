@php
    $menu = config('menu');
@endphp

<nav x-data="{ open: false }" class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="#">KOI</a>

    <button class="navbar-toggler" type="button" @click="open = !open">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" :class="{ 'show': open }">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            @foreach ($menu as $modulo => $items)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-uppercase" href="#" role="button" data-bs-toggle="dropdown">
                        {{ $modulo }}
                    </a>
                    <ul class="dropdown-menu">
                        @foreach ($items as $item)
                            <li>
                                <a class="dropdown-item" href="{{ url($item['ruta']) }}">
                                    {{ $item['nombre'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
