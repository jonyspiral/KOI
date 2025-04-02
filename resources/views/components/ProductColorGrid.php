// 📄 app/Livewire/ProductColorGrid.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProductColor;

class ProductColorGrid extends Component
{
    public $productId;
    public $colors;

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->loadColors();
    }

    public function loadColors()
    {
        $this->colors = ProductColor::where('product_id', $this->productId)->get();
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

    public function render()
    {
        return view('livewire.product-color-grid');
    }
}

