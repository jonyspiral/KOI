@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">✏️ Editar {{ $modelo }}</h2>

    <!-- Botón para abrir el modal -->
    <button class="btn btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#modalEdit">🛠️ Editar</button>

    <!-- Modal -->
    <div class="modal fade show" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" style="display: block;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('produccion.abms.rutas_produccion.update', $registro->{$primaryKey}) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Editar {{ $modelo }}</h5>
                        <a href="{{ route('produccion.abms.rutas_produccion.index') }}" class="btn-close"></a>
                    </div>
                    <div class="modal-body">
                    @include('components.partials.form-campos')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">💾 Guardar</button>
                        <a href="{{ route('produccion.abms.rutas_produccion.index') }}" class="btn btn-secondary">❌ Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
