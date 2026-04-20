<?php
require($_SERVER["DOCUMENT_ROOT"] . '/content/api/funciones.php');
// Notificar E_NOTICE también puede ser bueno (para informar de variables
// no inicializadas o capturar errores en nombres de variables ...)
error_reporting(E_ALL ^ E_NOTICE);

$distribuidor = Usuario::logueado()->cliente->listaAplicable == 'D';

// Favoritos
$favoritos = Base::getListObject('FavoritoCliente', 'cod_cliente = ' . Datos::objectToDB($_POST['usuario_id']));
//$formasComercializacion = array();
$prontoArticulo = array();
// Stock de favoritos
$stock = array();
$where = '';
foreach ($favoritos as $favorito) {
    /** @var FavoritoCliente $favorito */
    $where .= '(cod_articulo = ' . Datos::objectToDB($favorito->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($favorito->idColorPorArticulo) . ') OR ';
    //$formasComercializacion[$favorito->idArticulo . $favorito->idColorPorArticulo] = $favorito->colorPorArticulo->formaDeComercializacion;
}

//var_dump($prontoArticulo);die;

$where = 'cod_almacen = ' . Datos::objectToDB('01') . ($where ? ' AND (' . trim($where, ' OR ') . ')' : '');
$stocks = Factory::getInstance()->getArrayFromView('stock_menos_pendiente_vw', $where);
foreach ($stocks as $item) {
    for ($j = 1; $j <= 10; $j++) {
        if (!array_key_exists($item['cod_articulo'], $stock)) {
            $stock[$item['cod_articulo']] = array();
        }
        if (!array_key_exists($item['cod_color_articulo'], $stock[$item['cod_articulo']])) {
            $stock[$item['cod_articulo']][$item['cod_color_articulo']] = array();
        }
        $cant = Funciones::toInt(Funciones::keyIsSet($item, 'S' . $j, 0));
        $stock[$item['cod_articulo']][$item['cod_color_articulo']][$j] = Funciones::toNatural($cant);
    }
}



$arrayFavoritos = array();
foreach ($favoritos as $favorito) {
    /** @var FavoritoCliente $favorito */

    $lin = $favorito->articulo->lineaProducto;
    if (!array_key_exists($lin->id, $arrayFavoritos)) {
        $arrayFavoritos[$lin->id] = array(
            'nombre' => $lin->tituloCatalogo,
            'items' => array()
        );
    }
    //echo 'art:' . $favorito->idArticulo . $favorito->idColorPorArticulo . ', forma:' . $favorito->colorPorArticulo->formaDeComercializacion . '<-------<br>';

    // Datos básicos
    $fav = array(
        'idArticulo' => $favorito->idArticulo,
        'idColorPorArticulo' => $favorito->idColorPorArticulo,
        'articulo' => array(
            'nombre' => $favorito->articulo->nombre
        ),
        'colorPorArticulo' => array(
            'nombre' => $favorito->colorPorArticulo->nombre,
            'tipoProductoStock' => array(
                'id' => $favorito->colorPorArticulo->idTipoProductoStock,
                'nombre' => $favorito->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                'descuentoPorc' => $favorito->colorPorArticulo->tipoProductoStock->descuentoPorc
            )
        ),
        'idLinea' => $lin->id,
        'precioMayorista' => $distribuidor ? $favorito->colorPorArticulo->precioDistribuidor : $favorito->colorPorArticulo->precioMayoristaDolar,
        'precioMinorista' => $distribuidor ? $favorito->colorPorArticulo->precioDistribuidorMinorista : $favorito->colorPorArticulo->precioMinoristaDolar,
        'formaDeComercializacion' => $favorito->colorPorArticulo->formaDeComercializacion,
        'stock' => Funciones::keyIsSet(Funciones::keyIsSet($stock, $favorito->idArticulo, array()), $favorito->idColorPorArticulo, array())
    );

    // Talles / posiciones
  $fav['talles'] = array();
  foreach ($favorito->articulo->rangoTalle->posicion as $pos) {
      if (isset($pos)) {
            $fav['talles'][] = $pos;
        }
    }

    // Curvas de comercializacion y pares libres
    $fav['curvas'] = array();
    $fav['paresLibres'] = array();
  
    if ($favorito->colorPorArticulo->formaDeComercializacion == 'M') {
        //var_dump($favorito->colorPorArticulo->curvas);die;
        foreach ($favorito->colorPorArticulo->curvas as $curva) {
            $unidadesSeleccionadas = 0;
            //if ($favorito->curvas != NULL) {
              $unidadesSeleccionadas = array_key_exists($curva->idCurva, $favorito->curvas) ? $favorito->curvas[$curva->idCurva] : 0;
            //}
            $infoCurva = array(
                'id' => $curva->idCurva,
                'cantidades' => array(),
                'unidadesSeleccionadas' => $unidadesSeleccionadas
            );
            $isAllZero = true;
            $i = 0;
            foreach ($curva->curva->cantidad as $cant) {
                $i++;
                ($cant != 0) && $isAllZero = false;
                $infoCurva['cantidades'][] = Funciones::iIsSet($cant, '0');
                if ($i > 7) {
                    break;
                }
            }
            if (!$isAllZero) {
                $fav['curvas'][] = $infoCurva;
            }
        }

        if (count($fav['curvas'])) {
            $arrayFavoritos[$lin->id]['items'][] = $fav;
        }
    } else {
        $fav['paresLibres'] = array();
        for ($i = 0; $i <= 7; $i++) {
            $fav['paresLibres'][$i] = $favorito->cantidades[$i + 1] ? $favorito->cantidades[$i + 1] : 0;
        }
        $arrayFavoritos[$lin->id]['items'][] = $fav;
    }
}

//die;
$imagesUrl = 'http://www.spiralshoes.com/zapatillas/jpg/';

foreach ($arrayFavoritos as &$favPorTipo) {
  if (!isset($favPorTipo['items'])) continue;

  $totalMonto = 0;
  $totalPares = 0;

  foreach ($favPorTipo['items'] as &$item) {
    //echo 'item-> ' . $item['articulo']['nombre'] . ', forma:' . $item['formaDeComercializacion'];
    $imagenUrl = $imagesUrl . $item['idArticulo'] . $item['idColorPorArticulo'] . '_e.jpg';
    $dest = $_SERVER["DOCUMENT_ROOT"] . '/img/zapatillas/tmp/' . $item['idArticulo'] . $item['idColorPorArticulo'] . '_e.jpg';
    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . '/img/zapatillas/tmp/')) {
      mkdir($_SERVER["DOCUMENT_ROOT"] . '/img/zapatillas/tmp/', 0777, true);
    }
    $pngImage = $_SERVER["DOCUMENT_ROOT"] . '/img/zapatillas/tmp/' . $item['idArticulo'] . $item['idColorPorArticulo'] . '_e.png';
    if (!file_exists($pngImage)) {
      //copy ($imagenUrl , $dest);
      $contents=file_get_contents($imagenUrl);
      file_put_contents($dest,$contents);

      $new_pic = imagecreatefromjpeg($dest);

      // Create a new true color image with the same size
      //$w = imagesx($new_pic);
      //$h = imagesy($new_pic);
      //$white = imagecreatetruecolor($w, $h);
      // Fill the new image with white background
      //$bg = imagecolorallocate($white, 255, 255, 255);
      //imagefill($white, 0, 0, $bg);
      // Copy original transparent image onto the new image
      //imagecopy($white, $new_pic, 0, 0, 0, 0, $w, $h);

      $white  = imagecreatetruecolor(150, 30);
      $fondo = imagecolorallocate($white, 255, 255, 255);
      $ct  = imagecolorallocate($white, 0, 0, 0);

      imagefilledrectangle($white, 0, 0, 150, 30, $fondo);

      $new_pic = $white;
      imagepng($new_pic, $pngImage);
      imagedestroy($new_pic);
    }
    $prontoArticulo = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0);

    try {
      $prontoArticulo = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0
        , '8' => 0, '9' => 0, '10' => 0);
      $totalPronto = 0;
      $temp = array();
      $temp = getStockEnProduccion($item['idArticulo'], $item['idColorPorArticulo']);
      if (isset($temp['data'])) {
        $pos = 0 ;
        foreach ($temp['data'] as $prop => $value) {
          if(is_integer(strpos($prop, 'cant_'))){
            $prontoArticulo[++$pos] = $value;
            $totalPronto += $value;
          }
        }
      }
    } catch (Exception $e) {}

    $item['pronto'] = $prontoArticulo;
    $item['totalPronto'] = $totalPronto;
    $item['img'] = $dest;
    //echo '<hr>imagen:' . $dest;

    if ($item['formaDeComercializacion'] == 'M') {
        foreach ($item['curvas'] as $curva) {
          if (intval($curva['unidadesSeleccionadas']) > 0) {
            foreach ($curva['cantidades'] as $cantidad) {
              $totalPares += $cantidad * $curva['unidadesSeleccionadas'];
            }
          }
        }
    } else {
        foreach ($item['paresLibres'] as $parLibre) {
          $totalPares += $parLibre;
        }
    }
    
  }
  $favPorTipo['totalMonto'] = $totalMonto;
  $favPorTipo['totalPares'] = $totalPares;
}

//var_dump($arrayFavoritos);die;

?>
<style>
    .badge {
      display: inline-block;
      min-width: 10px;
      padding: 3px 7px;
      font-size: 12px;
      font-weight: 700;
      line-height: 1;
      color: #fff;
      text-align: center;
      white-space: nowrap;
      vertical-align: baseline;
      background-color: #777;
      border-radius: 10px;
    }
    .item-tipo {
      text-transform: uppercase;
      text-align: left;
    }
    .item-tipo .badge {
      background-color: #cacaca;
      margin-bottom: 2px;
    }
    .item-tipo .badge.badge-danger {
      background-color: #d9534f;
    }
    a {
        text-decoration: none;
        color: inherit;
    }
    h1 {
        margin-top: 10px;
        margin-bottom: 20px;
    }
    .well.big {
        padding: 62px 19px;
    }
    .item-inner {
        margin: 0 0 3px 0;
        padding: 0;
        position: relative;
        border: none;
    }
    .item-inner img {
        position: initial;
        margin: 0;
    }
    .item-name {
        position: initial;
    }
    .row.total>div {
        margin: 30px 0;
        padding: 10px 0;
        border-top: 5px solid gray;
        border-bottom: 1px solid gray;
    }

    /* Curvas */
    .favorito {
        margin-bottom: 20px;
        border: 1px solid #eaeaea;
        overflow: hidden;
    }
    .tabla-curvas {
        width: 100%;
        border-spacing: 1px;
    }
    .tabla-curvas th, .tabla-curvas td {
        border: 1px solid white;
    }
    .row-talles th {
        font-size: 15px;
        font-weight: bold;
        text-align: center;
        background-color: #333333;
        color: #FFFFFF;
        padding: 1px 0;
    }
    .row-stock {
        font-weight: bold;
    }
    .row-curva {
        background-color: #eaeaea;
    }
    .row-curva input {
        width: 35px;
        text-align: center;
        border: 1px solid #636363;
        padding-left: 7px;
        padding-right: 7px;
        background-color: #FFFFFF;
    }
    .row-totales td {
        font-size: 13px;
        font-weight: bold;
        background: #636363;
        color: #FFFFFF;
    }
    .col-curvas-cantidad {
        font-weight: bold;
        background: #d0d0d0;
    }
    .col-totales {
        padding-top: 30px;
        font-weight: bold;
    }
    .col-totales > table {
        width: 100%;
        height: 120px;
        border: 1px solid #d0d0d0;
    }
    .col-totales .titulo {
        width: 40%;
        border-bottom: 1px solid #d0d0d0;
    }
    .col-totales .valor {
        font-weight: normal;
        font-size: 14px;
        background: #d0d0d0;
        color: #000;
        border-bottom: 1px solid #ffffff;
    }
    .col-totales .total {
        font-weight: bold;
    }
    .col-totales .valor.total-pares {
        font-weight: bold;
        font-size: 15px;
        background: #636363;
        color: #fff;
    }
    .col-totales .no-border {
        border: none;
    }

    /* Tabla de detalles del pedido */
    .col-totales.detalle-pedido > table {
        height: 80px;
        margin: 0 auto;
        max-width: 320px;
        text-align: left;
        font-size: 17px;
    }
    .col-totales.detalle-pedido .titulo {
        padding-left: 12px;
    }
    .col-totales.detalle-pedido .valor {
        text-align: center;
        font-size: 17px;
    }
    .btn-generar-pedido {
        height: auto;
        padding: 5px 5px;
    }

    .flexibles {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 5px;
    }

   .item-favorito {
      width: 330px;
      position: relative;
      float: left;
   }
</style>



<div id="favoritos">

<?php
if (count($arrayFavoritos) > 0) { ?>
    <div>
        <div class="row well">
            <div class="col-xs-12">
                <h2>Reporte de Favoritos</h2>             
            </div>
        </div>
  <?php foreach($arrayFavoritos as $linea) { ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="row total">
                    <div class="col-xs-12">
                        <h2><b>[<?php echo $linea['nombre']; ?>]</b> <?php echo $linea['totalPares']; ?> pares - <?php echo $linea['totalMonto']; ?></h2>
                    </div>
                </div>
                <div class="flexibles favorito">
    <?php foreach($linea['items'] as $articulo) { 
      $imagenUrl = '';   $articulo['img'];
      //$imagenUrl = $imagesUrl . $articulo['idArticulo'] . $articulo['idColorPorArticulo'] . '_e.jpg';
      ?>
                    <div class="item-imagen item-favorito">
                        <div class="item-inner">
                            <img width="100%" src="<?php echo $imagenUrl ?>">
                            <div class="item-tipo" style="width: 65%">
                                <span class="badge <?php echo (($articulo['colorPorArticulo']['tipoProductoStock']['id'] == '1') ? 'badge-danger' : ''); ?>"><?php echo $articulo['colorPorArticulo']['tipoProductoStock']['nombre'] ?></span>
                                <span class="badge inverted"><?php echo $articulo['formaDeComercializacion'] ?></span>
                                <?php if ($articulo['colorPorArticulo']['tipoProductoStock']['descuentoPorc']) { ?>
                                  <span class="badge badge-danger">-<?php echo $articulo['colorPorArticulo']['tipoProductoStock']['descuentoPorc']; ?>%</span>
                                <?php } ?>
                            </div>
                            <div class="item-name"><?php echo $articulo['articulo']['nombre'] ?></div>
                        </div>
                        <div>
                          <table class="tabla-curvas">
                            <thead>
                            <tr class="row-talles">
                                <th></th>
                                <?php foreach ($articulo['talles'] as $talle){ ?>
                                  <th ><?php echo $talle ?></th>
                                <?php } ?>
                                <th>T</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="row-stock">
                                <td>Stock</td>
                                <?php $total = 0; foreach ($articulo['talles'] as $i => $talle){ $total += $articulo['stock'][$i + 1]; ?>
                                  <th class="aCenter"><?php echo $articulo['stock'][$i + 1] ?></th>
                                <?php } ?>
                                <td><?php echo $total; ?></td>
                            </tr>
                            <tr class="row-stock">
                                <td>Pronto</td>
                                <?php $total = 0; foreach ($articulo['talles'] as $i => $talle){ $total += $articulo['pronto'][$i + 1]; ?>
                                  <th class="aCenter"><?php echo $articulo['pronto'][$i + 1] ?></th>
                                <?php } ?>
                                <td><?php echo $total; ?></td>
                            </tr>

                            <?php if ($articulo['formaDeComercializacion'] == 'L' || $articulo['formaDeComercializacion'] == 'T') { ?>
                            <tr class="row-curva">
                                <!-- LIBRE -->
                                <td style="padding: 3px 0;">Selec.</td>
                                <?php $total = 0; foreach ($articulo['talles'] as $i => $talle){ $total += $articulo['paresLibres'][$i]; ?>
                                  <th class="aCenter"><?php echo $articulo['paresLibres'][$i] ?></th>
                                <?php } ?>
                                <td><?php echo $total; ?></td>
                            </tr>
                            <?php } ?>

                            <?php if ($articulo['formaDeComercializacion'] == 'M') { ?>
                              <?php foreach($articulo['curvas'] as $j => $curva) { ?> 
                              <tr class="row-curva">
                                <!-- Modular -->
                                <td>Curva <?php echo $j; ?></td>
                                <?php $total = 0; foreach ($articulo['talles'] as $i => $talle){ $total += $articulo['cantidades'][$i]; ?>
                                  <th class="aCenter"><?php echo $curva['cantidades'][$i] ?></th>
                                <?php } ?>
                                <td class="col-curvas-cantidad"><?php echo $total; ?></td>
                              </tr>
                              <?php } ?>
                            <?php } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                   <?php } ?>
                </div>
            </div>
        </div>
      <?php } ?>
    </div>

<?php } else { ?>

<div>
        <div class="well big">
            <h1>Aún no se han seleccionado favoritos</h1>
        </div>
    </div>
<?php } ?>

</div>
