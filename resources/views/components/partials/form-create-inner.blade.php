<form action="{{ route('__NOMBRE_RUTA__.store') }}" method="POST">
    @csrf

    {{-- Campos del formulario --}}
    @include('components.partials.form-campos')

    {{-- Botones de acción --}}
    <div class="mt-3 d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-success">💾 Guardar</button>
        @if(!request()->ajax())
            <a href="{{ route('__NOMBRE_RUTA__.index') }}" class="btn btn-secondary">❌ Cancelar</a>
        @endif
    </div>
</form>
s