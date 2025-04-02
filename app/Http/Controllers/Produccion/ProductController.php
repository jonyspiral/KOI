<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function showWithColors($id)
    {
        $product = Product::findOrFail($id);

        return view('products.show_with_colors', [
            'product' => $product,
            'title' => 'Product: ' . $product->name
        ]);
    }
}
