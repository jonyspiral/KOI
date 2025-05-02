{{-- 🎬 Modal Edit --}}
<div class="modal fade" id="modalEdit_{{ $registro->{$primaryKey} }}" tabindex="-1" aria-labelledby="modalEditLabel_{{ $registro->{$primaryKey} }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('produccion.abms.colores_por_articulo.update', $registro->{$primaryKey}) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel_{{ $registro->{$primaryKey} }}">✏️ Editar ColoresPorArticulo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    @include('components.partials.form-campos', [
                        'registro' => $registro,
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
