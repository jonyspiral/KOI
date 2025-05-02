<?php

namespace App\Exports;

use App\Models\ColoresPorArticulo;
use Maatwebsite\Excel\Concerns\FromCollection;

class ColoresPorArticuloExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ColoresPorArticulo::all();
    }
}
