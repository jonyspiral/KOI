// 📄 resources/views/layouts/components/master_detail_livewire.blade.php

@extends('layouts.app')

@section('title', $title ?? 'Master-Detail Form')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- Formulario principal (cabecera) --}}
    @yield('header')
</div>

<div class="bg-white border rounded shadow p-4">
    {{-- Subformulario relacionado (detalle en Livewire) --}}
    @yield('detail')
</div>

{{-- Navegación opcional o búsqueda --}}
@yield('footer')
@endsection

