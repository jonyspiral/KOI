<?php

namespace App\Http\Controllers\Sistemas;

use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index()
    {
        return 'Menú OK'; // o return view('sistemas.menu');
    }
}

