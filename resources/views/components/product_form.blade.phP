
// 📄 resources/views/partials/product_form.blade.php

<form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Campo: Código del producto --}}
        <div>
            <label for="code" class="block text-sm font-medium">Código</label>
            <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}" class="w-full border px-3 py-2 rounded">
        </div>

        {{-- Campo: Denominación --}}
        <div>
            <label for="name" class="block text-sm font-medium">Nombre</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="w-full border px-3 py-2 rounded">
        </div>

        {{-- Agregar más campos si es necesario --}}
    </div>

    <div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
    </div>
</form>


