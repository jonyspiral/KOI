


<div class="container-fluid px-0">
    <!-- <h2 class="mb-4">Listado de Horma</h2>

    {{-- Botón para abrir el modal --}}
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalCreate">
        ➕ Nuevo
    </button>

    {{-- Aquí podrías tener la tabla o resultados --}}
 -->
    {{-- 🎬 Modal Create --}}
    <div class="modal fade" id="modalCreate" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('produccion.abms.hormas.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateLabel">Nuevo Horma</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                    @include('components.partials.form-campos', [
    'registro' => [],
    'campos' => $campos ?? [],
    'defaults' => $defaults ?? [],
    'labels' => $labels ?? [],
    'opciones' => $opciones ?? []
])
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">💾 Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

