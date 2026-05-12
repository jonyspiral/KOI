<?php
require($_SERVER["DOCUMENT_ROOT"] . '/content/api/funciones.php');
// Notificar E_NOTICE también puede ser bueno (para informar de variables
// no inicializadas o capturar errores en nombres de variables ...)
//error_reporting(E_ALL ^ E_NOTICE);

$distribuidor = Usuario::logueado()->cliente->listaAplicable == 'D';

// Favoritos
$favoritos = Base::getListObject('FavoritoCliente', 'cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->cliente->id));
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
	$cantidadTalles = array();
    $primerTalle = '';
    $ultimoTalle = '';
    $stockInterno = 0;

    /** @var FavoritoCliente $favorito */
    $lin = $favorito->articulo->lineaProducto;

    /*if (!array_key_exists($lin->id, $arrayFavoritos)) {
        $arrayFavoritos[$lin->id] = array(
            'nombre' => $lin->tituloCatalogo,
            'items' => array()
        );
    }*/
        if (!array_key_exists($lin->tituloEcommerce, $arrayFavoritos)) {
        $arrayFavoritos[$lin->tituloEcommerce] = array(
            'nombre' => $lin->tituloEcommerce,
            'items' => array()
        );
    }
    //echo 'art:' . $favorito->idArticulo . $favorito->idColorPorArticulo . ', forma:' . $favorito->colorPorArticulo->formaDeComercializacion . '<-------<br>';

    // Create a unique key for the item
    $uniqueKey = $favorito->colorPorArticulo->referenciaWebMayorista . $favorito->colorPorArticulo->id;

    // Talles disponibles y stock interno por talle
    foreach ($favorito->articulo->rangoTalle->posicion as $key => $talle) {
        if ($talle != 'X' && $talle != '0' && $talle != '') {
            $stockTalle = 0;
            if (isset($stock[$favorito->idArticulo]) &&
                isset($stock[$favorito->idArticulo][$favorito->idColorPorArticulo]) &&
                isset($stock[$favorito->idArticulo][$favorito->idColorPorArticulo][$key])) {
                $stockTalle = Funciones::toInt($stock[$favorito->idArticulo][$favorito->idColorPorArticulo][$key]);
            }

            $cantidadTalles[] = array(
                'talle' => $talle,
                'cantidad' => $stockTalle
            );
        }
    }

    $stockInterno = Funciones::keyIsSet(
        Funciones::keyIsSet($stock, $favorito->idArticulo, array()),
        $favorito->idColorPorArticulo,
        array()
    );
    $stockInternoTotal = Funciones::sumaArray($stockInterno);

    $primerTalle = count($cantidadTalles) ? $cantidadTalles[0]['talle'] : '';
    $ultimoTalle = count($cantidadTalles) ? $cantidadTalles[count($cantidadTalles) - 1]['talle'] : '';

    // Datos básicos
    $fav = array(
        'idArticulo' => $favorito->idArticulo,
        'idColorPorArticulo' => $favorito->idColorPorArticulo,
        'nombre' => $favorito->articulo->nombre,
        'articulo' => array(
            'nombre' => $favorito->articulo->nombre
        ),
        'subArticulos' => array(),
        'colorPorArticulo' => array(
            'nombre' => $favorito->colorPorArticulo->nombre,
            'tipoProductoStock' => array(
                'id' => $favorito->colorPorArticulo->idTipoProductoStock,
                'nombre' => $favorito->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                'descuentoPorc' => $favorito->colorPorArticulo->tipoProductoStock->descuentoPorc
            )
        ),
        //'idLinea' => $lin->id,
        'idLinea' => $lin->tituloEcommerce,
        'precioMayorista' => $distribuidor ? $favorito->colorPorArticulo->precioDistribuidor : $favorito->colorPorArticulo->precioMayoristaDolar,
        'precioMinorista' => $distribuidor ? $favorito->colorPorArticulo->precioDistribuidorMinorista : $favorito->colorPorArticulo->precioMinoristaDolar,
        'formaDeComercializacion' => $favorito->colorPorArticulo->formaDeComercializacion,
        'stock' => Funciones::keyIsSet(Funciones::keyIsSet($stock, $favorito->idArticulo, array()), $favorito->idColorPorArticulo, array()),
        'stockTotal' => $stockInternoTotal,
        'primerTalle' => $cantidadTalles[0]['talle'],
        'ultimoTalle' => $cantidadTalles[count($cantidadTalles)-1]['talle'],
        'cantidadTalles' => $cantidadTalles,

        //'pronto' => array(),
        //'totalPronto' => 0,
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
            //$arrayFavoritos[$lin->id]['items'][] = $fav;
            // Add the main 'fav' and its sub-articulos to the uniqueKey
            $arrayFavoritos[$lin->tituloEcommerce]['items'][$uniqueKey]['fav'] = $fav;
        }
    } else {
        $fav['paresLibres'] = array();
        for ($i = 0; $i <= 7; $i++) {
            $fav['paresLibres'][$i] = $favorito->cantidades[$i + 1] ? $favorito->cantidades[$i + 1] : 0;
        }
        //$arrayFavoritos[$lin->id]['items'][] = $fav;
        $arrayFavoritos[$lin->tituloEcommerce]['items'][$uniqueKey]['fav'] = $fav;
    }


    // Now handle sub-articulos (example: based on a different property or condition)
    // You can push sub-articulos into 'subArticulos' array
    $subArticulo = array(
        'idArticulo' => $favorito->idArticulo,
        'idColorPorArticulo' => $favorito->idColorPorArticulo,
        //'pronto' => array(),
        //'totalPronto' => 0,
        // Add more data for sub-articulo here...
    );

    // Example: Add this subArticulo to the 'subArticulos' array under the same uniqueKey
    $arrayFavoritos[$lin->tituloEcommerce]['items'][$uniqueKey]['subArticulos'][$favorito->idArticulo . '-' . $favorito->idColorPorArticulo] = $fav;
}
//die;

foreach ($arrayFavoritos as &$favPorTipo) {
  if (!isset($favPorTipo['items'])) $totalPronto = 0;

  $totalMonto = 0;
  $totalPares = 0;

  foreach ($favPorTipo['items'] as &$subArticulos){

  	foreach($subArticulos['subArticulos'] as $key => &$item) {
	    //echo '<br>item-> ' . $item['articulo']['nombre'] . ', forma:' . $item['formaDeComercializacion'];
	    //echo '===[' . $key . '] ///' . $item['idArticulo'] . '-->' . $item['idColorPorArticulo'];
	    $prontoArticulo = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0);

	    try {
	      $prontoArticulo = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0
	        , '8' => 0, '9' => 0, '10' => 0);
	      $totalPronto = 0;
	      $temp = array();
	      $temp = getStockEnProduccion($item['idArticulo'], $item['idColorPorArticulo']);
	      //echo 'getStockEnProduccion-->';
	      //print_r($temp);
	      if (isset($temp['data'])) {
	        $pos = 0 ;
	        foreach ($temp['data'] as $prop => $value) {
	          if(is_integer(strpos($prop, 'cant_'))){
	            $prontoArticulo[++$pos] = $value;
	            $totalPronto += $value;
	          }
	        }
	      } else {
	      	$totalPronto = 0;
	      }
	    } catch (Exception $e) {
	    }

	    $item['pronto'] = $prontoArticulo;
	    $item['totalPronto'] = $totalPronto;

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
  }
  $favPorTipo['totalMonto'] = $totalMonto;
  $favPorTipo['totalPares'] = $totalPares;
}

//var_dump($arrayFavoritos);die;

/*
<button type="button" class="btn" ng-click="funciones.pdfClick(funciones.controllerUrl('getPdf', {id: '1'}, '/cliente/favoritos/reporte/'))">
                    <i class="fa fa-fw fa-file-pdf-o"></i> Descargar
                </button>
                */

?>

<style>
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
    .item-aaa {
        display: grid;
    }

    .flexibles {
      display: grid;
      grid-template-columns: repeat(1, 1fr);
      gap: 5px;
    }

    @media (min-width: 440px) {
      .flexibles {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (min-width: 768px) {
        .row-curva input {
            width: 60px;
            padding-left: 15px;
            padding-right: 0;
        }
        .col-totales {
            padding: 0 5px 0 0;
        }

        .flexibles {
          grid-template-columns: repeat(3, 1fr);
        }
    }

     @media (min-width: 1024px) {
      .flexibles {
          grid-template-columns: repeat(4, 1fr);
        }
     }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.min.js"></script>
<script>
    Koi.controller('FavoritosReporteCtrl', function ($scope, ServiceCliente) {

      $scope.funciones = funciones;

      $scope.generarReporteFavorito = function () {
          //console.log('creando pdf');
          window.print();
          /*var doc = new jsPDF('p', 'mm', 'a4');
          var specialElementHandlers = {
            '#editor': function (element, renderer) {
                return true;
            }
          };
          doc.fromHTML($('#favoritos').html(), 15, 15, {
              'width': 170,
                  'elementHandlers': specialElementHandlers
          });
          doc.save('reporteFavoritos.pdf');*/
      };

    });
</script>

<div id="favoritos" ng-controller="FavoritosReporteCtrl">
<?php
$imagesUrl = 'http://www.spiralshoes.com/zapatillas/jpg/';
if (count($arrayFavoritos) > 0) { ?>
    <div>
        <div class="row well">
            <div class="col-xs-12">
                <h2>Reporte del Pedido</h2>
                <button id="cmd" type="button" class="btn" ng-click="generarReporteFavorito()">
                    <i class="fa fa-fw fa-file-pdf-o"></i> Imprimir
                </button>
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
    <?php foreach($linea['items'] as $fav) {
    	$articulo = $fav['fav'];
    	//print_r($articulo);
      $imagenUrl = $imagesUrl . $articulo['idArticulo'] . $articulo['idColorPorArticulo'] . '_e.jpg';?>
                    <div class="item-imagen">
                        <div class="item-inner">
                            <a href="javascript:;" picture-modal>
                                <img src="<?php echo $imagenUrl ?>">
                            </a>

							<?php /*
							<div class="item-tipo" style="width: 65%">
                                <span class="badge <?php echo (($subArticulo['colorPorArticulo']['tipoProductoStock']['id'] == '1') ? 'badge-danger' : ''); ?>"><?php echo $subArticulo['colorPorArticulo']['tipoProductoStock']['nombre'] ?></span>
                                <span class="badge inverted"><?php echo $subArticulo['formaDeComercializacion'] ?></span>
                                <?php if ($subArticulo['colorPorArticulo']['tipoProductoStock']['descuentoPorc']) { ?>
                                  <span class="badge badge-danger">-<?php echo $subArticulo['colorPorArticulo']['tipoProductoStock']['descuentoPorc']; ?>%</span>
                                <?php } ?>
                            </div> */
                            ?>


                            <div class="item-precios">
	                            <?php foreach ($fav['subArticulos'] as $key => $subArticulo) {  ?>
	                            <div class="item-aaa">
	                            	<span>
	                 <?php echo $subArticulo['nombre'] . ' - ' . $subArticulo['idArticulo'] . ' ' . $subArticulo['idColorPorArticulo']; ?>
	                 				</span>
                                    <span>
                                        <span class="badge <?php echo (($subArticulo['colorPorArticulo']['tipoProductoStock']['id'] == '1') ? 'badge-danger' : ''); ?>"><?php echo $subArticulo['colorPorArticulo']['tipoProductoStock']['nombre'] ?></span>
                                        <span class="badge inverted"><?php echo $subArticulo['formaDeComercializacion'] ?></span>
                                        <?php if ($subArticulo['colorPorArticulo']['tipoProductoStock']['descuentoPorc']) { ?>
                                        <span class="badge badge-danger">-<?php echo $subArticulo['colorPorArticulo']['tipoProductoStock']['descuentoPorc']; ?>%</span>
                                    </span>

	                                <?php } ?>
	                            </div>
	                        	<?php } ?>
                        	</div>
                            <div class="item-name"><?php //echo $articulo['articulo']['nombre'] . ' - ' . $articulo['idArticulo'] . ' ' . $articulo['idColorPorArticulo'];
                            echo $articulo['articulo']['nombre'];
                             ?></div>
                        </div>
                        <div>
                        <!-- nuevo para mostrar los subArticulos con colorPorArticulo y los badges -->
                        <?php foreach ($fav['subArticulos'] as $key => $subArticulo) {
                        	//print_r($subArticulo);
                        ?>

                          <table class="tabla-curvas">
                            <thead>
                            <tr class="row-talles">
                                <th><?php echo $subArticulo['idArticulo'] . ' - ' . $subArticulo['idColorPorArticulo']; ?></th>
                                <?php foreach ($subArticulo['talles'] as $talle){ ?>
                                  <th ><?php echo $talle ?></th>
                                <?php } ?>
                                <th>T</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="row-stock">
                                <td>Stock</td>
                                <?php $total = 0; foreach ($subArticulo['talles'] as $i => $talle){ $total += $subArticulo['stock'][$i + 1]; ?>
                                  <th class="aCenter"><?php echo $subArticulo['stock'][$i + 1] ?></th>
                                <?php } ?>
                                <td><?php echo $total; ?></td>
                            </tr>
                            <tr class="row-stock">
                                <td>Pronto</td>
                                <?php $total = 0; foreach ($subArticulo['talles'] as $i => $talle){ $total += $subArticulo['pronto'][$i + 1]; ?>
                                  <th class="aCenter"><?php echo $subArticulo['pronto'][$i + 1] ?></th>
                                <?php } ?>
                                <td><?php echo $total; ?></td>
                            </tr>

                            <?php if ($subArticulo['formaDeComercializacion'] == 'L' || $subArticulo['formaDeComercializacion'] == 'T') { ?>
                            <tr class="row-curva">
                                <!-- LIBRE -->
                                <td style="padding: 3px 0;">Selec.</td>
                                <?php $total = 0; foreach ($subArticulo['talles'] as $i => $talle){ $total += $subArticulo['paresLibres'][$i]; ?>
                                  <th class="aCenter"><?php echo $subArticulo['paresLibres'][$i] ?></th>
                                <?php } ?>
                                <td><?php echo $total; ?></td>
                            </tr>
                            <?php } ?>

                            <?php if ($subArticulo['formaDeComercializacion'] == 'M') { ?>
                              <?php foreach($subArticulo['curvas'] as $j => $curva) { ?>
                              <tr class="row-curva">
                                <!-- Modular -->
                                <td>Curva <?php echo $j; ?></td>
                                <?php $total = 0; foreach ($subArticulo['talles'] as $i => $talle){ $total += $subArticulo['cantidades'][$i]; ?>
                                  <th class="aCenter"><?php echo $curva['cantidades'][$i] ?></th>
                                <?php } ?>
                                <td class="col-curvas-cantidad"><?php echo $total; ?></td>
                              </tr>
                              <?php } ?>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php } ?>
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
