// 📄 resources/views/livewire/product-color-grid.blade.php

<table class="min-w-full text-sm">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-2 py-1">Color</th>
            <th class="px-2 py-1">Precio Mayorista</th>
            <th class="px-2 py-1">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($colors as $color)
        <tr class="border-b hover:bg-gray-50">
            <td class="px-2 py-1">{{ $color->name }}</td>
            <td class="px-2 py-1">
                <input type="number" wire:change="updateColor({{ $color->id }}, 'wholesale_price', $event.target.value)" value="{{ $color->wholesale_price }}" class="border px-1 py-0.5 rounded w-full">
            </td>
            <td class="px-2 py-1">
                <button wire:click="deleteColor({{ $color->id }})" class="text-red-600 hover:underline">🗑 Eliminar</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
