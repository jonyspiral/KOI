<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ForecastEncabezado;

class ForecastEncabezadoController extends Controller
{
    public function index()
    {
        $registros = ForecastEncabezado::all();
        $modelo = 'Forecast Encabezado';
        return view('produccion/forecast_encabezado.index', compact('registros', 'modelo'));
    }

    public function create()
    {
        $modelo = 'Forecast Encabezado';
        return view('produccion/forecast_encabezado.form', compact('modelo'));
    }

    public function store(Request $request)
    {
        ForecastEncabezado::create($request->only(['IdForecast', 'Denom_Forecast', 'Autor', 'Autoriza', 'aprobado', 'anulado', 'Observaciones', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.forecast_encabezado.index');
    }

    public function edit($id)
    {
        $registro = ForecastEncabezado::findOrFail($id);
        $modelo = 'Forecast Encabezado';
        return view('produccion/forecast_encabezado.form', compact('registro', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = ForecastEncabezado::findOrFail($id);
        $registro->update($request->only(['IdForecast', 'Denom_Forecast', 'Autor', 'Autoriza', 'aprobado', 'anulado', 'Observaciones', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.forecast_encabezado.index');
    }

    public function destroy($id)
    {
        ForecastEncabezado::destroy($id);
        return redirect()->route('produccion.forecast_encabezado.index');
    }
}