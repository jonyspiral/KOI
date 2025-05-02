<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericoExport implements FromCollection, WithHeadings
{
    protected $modelo;

    public function __construct(string $modelo)
    {
        $this->modelo = $modelo;
    }

    public function collection()
    {
        $modelo = $this->modelo;
        return $modelo::limit(50)->get();
    }

    public function headings(): array
    {
        $modelo = $this->modelo;
        return array_keys($modelo::first()->toArray());
    }
}
