@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">📄 ABM de __MODELO__</h2>

    @php
        $rutaNombre = '__NOMBRE_RUTA__';
        $modelo = '__MODELO__';
        $carpeta_vistas = '__CARPETA_VISTAS__';
        $primaryKey = $primaryKey ?? 'id';
        $esModal = true;
    @endphp

    {{-- 🔘 Botón de creación --}}
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCreate">
        ➕ Nuevo {{ $modelo }}
    </button>

    {{-- 📦 Tabla --}}
    @if ($registros->count())
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    @foreach ($columnas as $col)
                        <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                    @endforeach
                    <th class="text-center">⚙️</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registros as $registro)
                    <tr>
                        @foreach ($columnas as $col)
                            <td>{{ $registro->$col }}</td>
                        @endforeach
                        <td class="text-center">
                            {{-- ✏️ Editar --}}
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEdit-{{ $registro->$primaryKey }}">✏️</button>
                            
                            {{-- 🗑️ Eliminar --}}
                            <form action="{{ route("{$rutaNombre}.destroy", $registro->$primaryKey) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>

                    {{-- ✏️ Modal de edición --}}
                    <div class="modal fade" id="modalEdit-{{ $registro->$primaryKey }}" tabindex="-1" aria-labelledby="modalEditLabel-{{ $registro->$primaryKey }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">✏️ Editar {{ $modelo }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    @include("{$carpeta_vistas}.edit", ['registro' => $registro, 'desde_modal' => true])
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $registros->links() }}
    @else
        <div class="alert alert-info">⚠️ No hay registros para mostrar.</div>
    @endif


    {{-- ➕ Modal de creación --}}
    <div class="modal fade" id="modalCreate" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateLabel">➕ Crear nuevo {{ $modelo }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @include("{$carpeta_vistas}.create", ['desde_modal' => true])
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
