<div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left border rounded">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-2 border">Color</th>
                <th class="px-4 py-2 border">Precio Mayorista</th>
                <th class="px-4 py-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($colors as $color)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border align-middle">{{ $color->denom_color }}</td>
                <td class="px-4 py-2 border align-middle">
                    <input type="number" step="0.01"
                           wire:change="updateColor({{ $color->id }}, 'precio_mayorista', $event.target.value)"
                           value="{{ $color->precio_mayorista }}"
                           class="w-full px-2 py-1 border rounded shadow-sm">
                </td>
                <td class="px-4 py-2 border align-middle">
                    <button wire:click="deleteColor({{ $color->id }})"
                            class="bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-3 py-1 rounded shadow-sm">
                        🗑 Eliminar
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Formulario para agregar un nuevo color --}}
    <hr class="my-4">
    <h5 class="font-semibold mb-2">Agregar nuevo color</h5>
    <div class="flex gap-2 items-center">
        <input type="text" wire:model="nuevoColor" wire:keydown.enter="addColor" placeholder="Nombre del color" class="border px-2 py-1 rounded w-1/2" id="input-nuevo-color">
        <button wire:click="addColor" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">Agregar</button>
    </div>
</div>

@push('scripts')
<script>
    Livewire.on('focus-color-input', () => {
        setTimeout(() => {
            const input = document.getElementById('input-nuevo-color');
            if (input) input.focus();
        }, 100);
    });
</script>
@endpush
