<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOI - @yield('title', 'Producción')</title>

    @livewireStyles

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    max-width: 100vw;
    overflow-x: hidden;
}
        svg.w-5.h-5 {
            display: none !important;
        }
        .fila-eliminada {
            background-color: #f8f9fa !important;
            color: #6c757d;
        }
        .container-fluid {
            padding-left: 2% !important;
            padding-right: 2% !important;
        }
        table {
            width: 100% !important;
        }
        .tr-nuevo-registro {
            background-color: #f4f8fb;
            border-top: 2px solid #0d6efd;
        }
        .tr-nuevo-registro input,
        .tr-nuevo-registro select {
            height: 32px;
            font-size: 0.9rem;
            padding: 2px 6px;
        }
        [x-cloak] {
            display: none !important;
        }
        tr[x-show] {
            display: table-row !important;
        }
        .card,
        .card-body,
        .card .container,
.card .container,
        .card .row,
        .card .col,
        .card form,
        .card form > * {
            max-width: 100% !important;
        }
    </style>
</head>
<body>
    <!-- Menú principal como NAV -->
    <nav x-data="{ open: false }" class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="#">KOI</a>

        <button class="navbar-toggler" type="button" @click="open = !open">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" :class="{ 'show': open }">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @php
                    $menu = config('menu', []);
                    if (isset($menu['Mlibre'])) {
                        $menu['Mlibre'][] = ['nombre' => 'SKU Variantes', 'ruta' => '/sku/sku_variantes'];
                    }
                    if (isset($menu['Eshop'])) {
                        $menu['Eshop'][] = ['nombre' => 'SKU Eshop', 'ruta' => '/eshop/sku'];
                    }
                @endphp

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
            @auth
    <form method="POST" action="{{ route('logout') }}" class="d-flex align-items-center ms-auto" style="margin-left: auto;">
        @csrf
        <span class="text-white me-2">{{ Auth::user()->name }}</span>
        <button type="submit" class="btn btn-sm btn-outline-light">Cerrar sesión</button>
    </form>
@endauth
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="w-100" style="padding: 2%; margin: 0; max-width: 100% !important; @if(app()->environment('development')) background-color: #e3f9f8; @endif">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script defer>
        document.addEventListener('alpine:init', () => {
            console.log('✅ AlpineJS cargado');
        });
        document.addEventListener("DOMContentLoaded", function () {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Seleccione una opción'
            });
        });
    </script>

    @stack('scripts')
    @livewireScripts

    <!-- Banner de entorno -->
    @if (app()->environment('production'))
        <div style="position: fixed; top: 10px; right: 10px; width: 32px; height: 32px; background-color: #dc3545; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; z-index: 9999; box-shadow: 0 0 6px rgba(0,0,0,0.3);">
            P
        </div>
    @else
        <div style="position: fixed; top: 10px; right: 10px; width: 32px; height: 32px; background-color: #0dcaf0; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; z-index: 9999; box-shadow: 0 0 6px rgba(0,0,0,0.3);">
            D
        </div>
    @endif
</body>
</html>
