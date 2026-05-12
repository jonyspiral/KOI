<?php
require_once 'includes.php';
$articulo='3040';
$codColorPorArticulo='05';
$stock = array();
$where = '';

$where = 'cod_articulo = ' . Datos::objectToDB($articulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($codColorPorArticulo) . ' ';
// $where = 'cod_almacen = ' . Datos::objectToDB('01') . ' AND (' . trim($where, ' OR ') . ')';
// $stocks = Factory::getInstance()->getArrayFromView('stock_menos_pendiente_vw', $where);
// $where='';
$stocks = Factory::getInstance()->getArrayFromView('stock14y20_por_talle_v', $where);
// foreach ($stocks as $item) {
//     for ($j = 1; $j <= 10; $j++) {
//         if (!array_key_exists($item['cod_articulo'], $stock)) {
//             $stock[$item['cod_articulo']] = array();
//         }
//         if (!array_key_exists($item['cod_color_articulo'], $stock[$item['cod_articulo']])) {
//             $stock[$item['cod_articulo']][$item['cod_color_articulo']] = array();
//         }
//         $cant = Funciones::toInt(Funciones::keyIsSet($item, 'S' . $j, 0));
//
//         $stock[$item['cod_articulo']][$item['cod_color_articulo']][$j] = Funciones::toNatural(Funciones::keyIsSet($item, 'S' . $j, 0));
//     }

deliver_response(200,"",$stocks);
// }
function deliver_response($status, $status_message, $data)
    {
        header("HTTP/1.1 $status $status_message");
        $response['status'] = $status;
        $response['status_message'] = $status_message;
        $response['data'] = $data;
        $json_response = json_encode($response);
        echo "$json_response";
    }
// $json_response = json_encode($stocks);
// echo "$json_response";
