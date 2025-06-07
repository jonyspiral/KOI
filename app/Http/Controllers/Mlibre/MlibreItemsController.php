<?php

namespace App\Http\Controllers\Mlibre;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MlItems;
use App\Services\Integraciones\PublicadorMercadoLibre;
use Illuminate\Http\Request;

class MlibreItemsController extends Controller
{
public function formPublicar()
{
    $productos = DB::connection('sqlsrv_koi')
        ->table('ecomexperts_articulos_update_v')
        ->select([
            'sku',
            'titulo',
            'precio_valor1 as precio',
            'cantidad as stock_total',
            'var_sku as agrupador',
            'variante2 as nombre_color',
            'imagen1 as imagen_1_url'
        ])
        ->orderBy('sku')
        ->limit(50)
        ->get();

    return view('mlibre.publicar', compact('productos'));
}




    public function generarPublicaciones(Request $request)
    {
        $seleccion = $request->input('seleccionados', []);

        $datos = collect($seleccion)->map(function ($valor) {
            [$sku] = explode('|', $valor);

            return MlItems::where('sku', $sku)->first();
        })->filter()->values()->toArray();

        $publicador = new PublicadorMercadoLibre();
        $resultado = $publicador->publicarLote($datos);

        return response()->json($resultado);
    }

    // Método de prueba para lote estático
    public function testPowSkateb()
    {
        $datos = [
            [
                'cod_articulo' => '869',
                'cod_color_articulo' => '01',
                'nombre_color' => 'BLACK',
                'curva_talle' => '35–39',
                'stock_total' => 25,
                'precio' => 49000,
                'agrupador' => 'pow_skateb',
                'imagen_1_url' => 'https://spiralshoes.com/images/pow_869_01_a.jpg',
            ],
            [
                'cod_articulo' => '3062',
                'cod_color_articulo' => '01',
                'nombre_color' => 'BLACK',
                'curva_talle' => '40–45',
                'stock_total' => 30,
                'precio' => 49000,
                'agrupador' => 'pow_skateb',
                'imagen_1_url' => 'https://spiralshoes.com/images/pow_3062_01_a.jpg',
            ],
        ];

        $publicador = new PublicadorMercadoLibre();
        $resultado = $publicador->publicarLote($datos);

        return response()->json($resultado);
    }
    public function verVariantes($sku)
{
    // Separar el SKU en artículo y color
    $cod_articulo = substr($sku, 0, -2);
    $cod_color_articulo = substr($sku, -2);

    // Producto base (una fila del resumen)
    $producto = DB::table('vista_publicacion_ml')
        ->where('cod_articulo', $cod_articulo)
        ->where('cod_color_articulo', $cod_color_articulo)
        ->first();

    if (!$producto) {
        abort(404, 'Producto no encontrado');
    }

    // Traer variantes (talles)
    $variantes = DB::connection('sqlsrv_koi')
        ->table('ecomexperts_articulos_update_v')
        ->where('cod_articulo', $cod_articulo)
        ->where('cod_color_articulo', $cod_color_articulo)
        ->whereNotNull('Talle')
        ->where('Talle', '<>', '')
        ->where('Talle', '<>', 'X')
        ->orderBy('Talle')
        ->get();

    return view('mlibre.variantes', compact('producto', 'variantes'));
}
public function publicarVariantes(Request $request, $sku)
{
    $seleccion = $request->input('variantes', []);

   if (empty($seleccion)) {
    return back()->with('error', 'No seleccionaste ninguna variante');
}

$datos = collect($seleccion)->map(function ($sku_variante) {
    return DB::connection('sqlsrv_koi')
        ->table('ecomexperts_articulos_update_v')
        ->whereRaw("cod_articulo + cod_color_articulo + Talle = ?", [$sku_variante])
        ->first();
})->filter()->values()->toArray();

$publicador = new \App\Services\Integraciones\PublicadorMercadoLibre();
$resultado = $publicador->publicarLote($datos);

// 🔁 Redirigimos a la misma vista con un mensaje
return back()->with('success', '✅ Variantes publicadas con éxito (' . count($datos) . ')');
}
public function formPublicarVariantes()
{
    $variantes = DB::connection('sqlsrv_koi')
        ->table('ecomexperts_articulos_update_v')
        ->select([
            'sku',
            'var_sku as sku_variante',
            'cantidad',
            'precio_valor1',
            'variante1',
            'variante2',
        ])
        ->orderBy('sku')
        ->limit(10)
        ->get();

    return view('mlibre.variantes', compact('variantes'));
}


}
