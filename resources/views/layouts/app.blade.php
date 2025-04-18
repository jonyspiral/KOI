<!DOCTYPE html>
<html lang="es">
<head>
    @livewireStyles
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOI - @yield('title', 'Producción')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: hidden;
        }
        svg.w-5.h-5 {
            .fila-eliminada {
    background-color: #f8f9fa !important;
    color: #6c757d;
}        
    display: none !important;
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

/* <!-- FIX PARA MOSTRAR TR CON x-show -->

    [x-cloak] {
        display: none !important;
    }

    tr[x-show] {
        display: table-row !important;
    }
 */</style>

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">KOI</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/articulos">Artículos</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid px-0">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>






    <!-- jQuery debe ir primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle JS (con Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<!-- Alpine.js -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

   <!-- Confirmación de carga de AlpineJS -->
<script defer>
    document.addEventListener('alpine:init', () => {
        console.log('✅ AlpineJS cargado');
    });
</script>
    <!-- Inicializar Select2 -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Seleccione una opción'
            });
        });
    </script>
    @livewireScripts
</body>
</html>
