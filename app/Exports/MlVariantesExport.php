<?php

namespace App\Exports;

use App\Models\MlVariante;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MlVariantesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return MlVariante::all([
            'ml_id',
            'family_id',
            'variation_id',
            'product_number',
            'seller_custom_field',
            'titulo',
            'talle',
            'color',
            'modelo',
            'seller_sku',
            'precio',
            'stock',
            'stock_flex',
            'stock_full',
            'seller_custom_field_actual',
            'nuevo_seller_custom_field',
        ]);
    }

    public function headings(): array
    {
        return [
            'ML ID',
            'Family ID',
            'Variation ID',
            'Product #',
            'SCF',
            'Título',
            'Talle',
            'Color',
            'Modelo',
            'SSKU',
            'Precio',
            'Stock',
            'Stock Flex',
            'Stock Full',
            'SCF Actual',
            'Nuevo SCF',
        ];
    }
}
