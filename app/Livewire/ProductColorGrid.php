<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProductColor;

class ProductColorGrid extends Component
{
    public $productId; // ← será el cod_articulo
    public $colors;
    public $nuevoColor = '';

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->loadColors();
    }

    public function loadColors()
    {
        $this->colors = ProductColor::where('cod_articulo', $this->productId)->get();
    }

    public function updateColor($id, $field, $value)
    {
        $color = ProductColor::find($id);
        if ($color) {
            $color->$field = $value;
            $color->save();
            $this->loadColors();
        }
    }

    public function deleteColor($id)
    {
        ProductColor::destroy($id);
        $this->loadColors();
    }

    public function addColor()
    {
        if (trim($this->nuevoColor) === '') return;

        ProductColor::create([
            'cod_articulo' => $this->productId,
            'denom_color' => $this->nuevoColor,
        ]);

        $this->dispatch('focus-color-input'); // Envia evento JS

        $this->nuevoColor = '';
        $this->loadColors();
    }

    public function render()
    {
        return view('livewire.product-color-grid');
    }
}
