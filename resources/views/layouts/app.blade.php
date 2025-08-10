<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>KOI - @yield('title', 'Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- AdminLTE + Bootstrap 4 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">

    {{-- Select2 + Livewire + Alpine --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @livewireStyles
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
 
        .select2-container .select2-selection--single {
            height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    {{-- Navbar superior --}}
    @include('partials.navbar')

    {{-- Sidebar (menú lateral) --}}
    @include('partials.sidebar')

    {{-- Contenido principal --}}
    <div class="content-wrapper">
        <section class="content pt-3 px-3">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </section>
    </div>

    {{-- Footer --}}
    <footer class="main-footer text-sm text-center">
        <strong>KOI</strong> &copy; {{ date('Y') }} - Spiral Shoes
    </footer>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@livewireScripts
@stack('scripts')
</body>
</html>
