@extends('layouts.app')

@section('title', 'Inicio KOI2')

@section('content')
<div class="container-fluid py-5">
    <div class="text-center mb-4">
       
        <p class="text-muted">Sistema de gestión y producción de Spiral Shoes</p>
    </div>

    <div class="text-center mb-4">
        <img src="{{ asset('images/koi.png') }}" alt="Dashboard KOI" class="img-fluid" style="max-height: 300px;">
    </div>

    <div class="text-center">
        <a href="{{ route('menu') }}" class="btn btn-primary btn-lg">
            🚀 Ir al Menú Principal
        </a>
    </div>
</div>
@endsection
