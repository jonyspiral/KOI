<?php

$distribuidor = Usuario::logueado()->cliente->listaAplicable == 'D';

// Favoritos
$favoritos = Base::getListObject('FavoritoCliente', 'cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->cliente->id));
// Stock de favoritos
$stock = array();
$where = '';
foreach ($favoritos as $favorito) {
    /** @var FavoritoCliente $favorito */
    $where .= '(cod_articulo = ' . Datos::objectToDB($favorito->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($favorito->idColorPorArticulo) . ') OR ';
}
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
$articulosUnicos = array();
foreach ($favoritos as $favorito) {
    $cantidadTalles = array();
    $primerTalle = '';
    $ultimoTalle = '';
    $stockInterno = 0;

    /** @var FavoritoCliente $favorito */
    $lin = $favorito->articulo->lineaProducto;
    
    //print_r('<br>$lin->tituloEcommerce');
    //print_r($lin);
    $articulo = $favorito;
    if (!array_key_exists($lin->tituloEcommerce, $arrayFavoritos)) {
        $arrayFavoritos[$lin->tituloEcommerce] = array(
            'nombre' => $lin->tituloEcommerce,
            'items' => array()
        );
    }

    foreach ($articulo->articulo->rangoTalle->posicion as $key => $talle) {
        //print_r('<br> articulo id ->' . $articulo->articulo->id . ' - idColorPorArticulo ->' . $articulo->idColorPorArticulo . ' talle -> ' . $talle . ' key -> ' . $key);
        if ($talle != 'X' && $talle != '0' && $talle != '' && isset($stock[$articulo->articulo->id]) && isset($stock[$articulo->articulo->id][$articulo->idColorPorArticulo])) {
        $cantidadTalles[] = array(
            'talle' => $talle,
            'cantidad' => $stock[$articulo->articulo->id][$articulo->idColorPorArticulo][$key],
          );
        //$stockInterno += intval($stock[$articulo->articulo->id][$articulo->idColorPorArticulo][$key]);
        }
    }

    // Create a unique key for the item
    $uniqueKey = $favorito->colorPorArticulo->referenciaWebMayorista . $favorito->colorPorArticulo->id;
    //print_r("<br>". $lin->tituloEcommerce .'uniqueKey//' . $uniqueKey);

    // Initialize the item if it does not exist
    if (!isset($arrayFavoritos[$lin->tituloEcommerce]['items'][$uniqueKey])) {
        // Initialize the uniqueKey array with sub-articulos
        $arrayFavoritos[$lin->tituloEcommerce]['items'][$uniqueKey] = array(
            'fav' => array(),
            'favorito' => true,
            'subArticulos' => array()  // Initialize subArticulos array
        );
    }

    $stockInterno = Funciones::keyIsSet(Funciones::keyIsSet($stock, $favorito->idArticulo, array()), $favorito->idColorPorArticulo, array());
    $stockInternoTotal = Funciones::sumaArray($stockInterno);


    //OW 2 SKATEB SMALL - 3062 NNB
    //POW 2 SKATEB MEN - 869 NNB

    // Create the main 'fav' data structure
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
        'idLinea' => $lin->tituloEcommerce,
        'precioMayorista' => $distribuidor ? $favorito->colorPorArticulo->precioDistribuidor : $favorito->colorPorArticulo->precioMayoristaDolar,
        'precioMinorista' => $distribuidor ? $favorito->colorPorArticulo->precioDistribuidorMinorista : $favorito->colorPorArticulo->precioMinoristaDolar,
        'formaDeComercializacion' => $favorito->colorPorArticulo->formaDeComercializacion,
        'stock' => $stockInterno,
        'stockTotal' => $stockInternoTotal,
        'primerTalle' => $cantidadTalles[0]['talle'],
        'ultimoTalle' => $cantidadTalles[count($cantidadTalles)-1]['talle'],
        'cantidadTalles' => $cantidadTalles,
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
        foreach ($favorito->colorPorArticulo->curvas as $curva) {
            $infoCurva = array(
                'id' => $curva->idCurva,
                'cantidades' => array(),
                'unidadesSeleccionadas' => array_key_exists($curva->idCurva, $favorito->curvas) ? $favorito->curvas[$curva->idCurva] : 0
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
            // Add the main 'fav' and its sub-articulos to the uniqueKey
            $arrayFavoritos[$lin->tituloEcommerce]['items'][$uniqueKey]['fav'] = $fav;
        }
    } else {
        $fav['paresLibres'] = array();
        for ($i = 0; $i <= 7; $i++) {
            $fav['paresLibres'][$i] = $favorito->cantidades[$i + 1] ? $favorito->cantidades[$i + 1] : 0;
        }
        $arrayFavoritos[$lin->tituloEcommerce]['items'][$uniqueKey]['fav'] = $fav;
    }

    // Now handle sub-articulos (example: based on a different property or condition)
    // You can push sub-articulos into 'subArticulos' array
    $subArticulo = array(
        'idArticulo' => $favorito->idArticulo,
        'idColorPorArticulo' => $favorito->idColorPorArticulo,
        // Add more data for sub-articulo here...
    );
    
    // Example: Add this subArticulo to the 'subArticulos' array under the same uniqueKey
    $arrayFavoritos[$lin->tituloEcommerce]['items'][$uniqueKey]['subArticulos'][] = $fav;
}


$sucursales = array();
$idSucursalDefault = '';
foreach (Usuario::logueado()->cliente->sucursales as $sucursal) {
    $sucursales[] = array(
        'id' => $sucursal->id,
        'nombre' => $sucursal->nombre
    );
    if ($sucursal->id == Usuario::logueado()->cliente->idSucursalEntrega) {
        $idSucursalDefault = Usuario::logueado()->cliente->idSucursalEntrega;
    }
}

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
        position: initial;
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

    .subarticulo {
        /* display: block; */
      display: flex;
      justify-content: space-between;
    }
     .subarticulo.sub-inline-grid {
      display: grid;
      justify-content: center;
    }

    .subarticulo .badge {
        background-color: #cacaca;
        margin-bottom: 2px;
    }
    .subarticulo .badge.badge-danger {
        background-color: #d9534f;
    }
    .subarticulo .badge.inverted {
        border: 1px solid #afafaf;
        color: #afafaf;
        background: none;
    }
    .subitem-talles {
      position: absolute;
      bottom: 0;
      left: 10px;
      right: 10px;
    }

    span.item-stock-badge {
      color: #5DB44C;
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
    }
</style>

<script>

    Koi.controller('FavoritosCtrl', function ($scope, ServiceCliente) {

      $scope.funciones = funciones;
      $scope.imagesUrl = 'http://www.spiralshoes.com/zapatillas/jpg/';

      $scope.descuento = <? echo Funciones::toFloat(Usuario::logueado()->cliente->creditoDescuentoEspecial); ?>;
      $scope.favoritos = <? echo json_encode($arrayFavoritos); ?>;
      $scope.sucursales = <? echo json_encode($sucursales); ?>;
      $scope.idSucursalPedido = '<? echo $idSucursalDefault; ?>';

      var commonCallback = function (err, result) {
        if (err) {
          $.growl.error('Error al guardar (' + err + ')');
        } else {
          $.growl.notice('Guardado');
        }
      };

      $scope.show = function (bag) {
        return Object.keys(bag).length;
      };

      $scope.addCurva = function (articulo, curva) {
        curva.unidadesSeleccionadas++;
        ServiceCliente.updateCurva(articulo, curva, commonCallback);
      };

      $scope.removeCurva = function (articulo, curva) {
        if (curva.unidadesSeleccionadas > 0) {
          curva.unidadesSeleccionadas--;
          ServiceCliente.updateCurva(articulo, curva, commonCallback);
        }
      };    

      $scope.updateLibre = function (articulo, index, talle) {
        //editado
        if (!articulo.paresLibres[index]._prevValue) {
        // Guarda el valor inicial al cargar la página
            articulo.paresLibres[index]._prevValue = articulo.paresLibres[index];
        }
        var allGood = true;
        //console.log('typeof talle', typeof talle)
        if (talle == 'X') {
            $.growl.error('El talle es incorrecto o no es valido');
            articulo.paresLibres[index] = articulo.paresLibres[index]._prevValue;
            return false
        }
        let disponible = {
            talle: '', cantidad: 0
        };
        if (articulo.formaDeComercializacion == 'T') {
            disponible = articulo.cantidadTalles.find(item => {
                return item.talle == talle
            })
            
            if (!disponible || disponible.cantidad < articulo.paresLibres[index]) {
                $.growl.error(`La cantidad ingresada supera el disponible (${disponible ? disponible.cantidad : 0})`);
                articulo.paresLibres[index] = disponible ? disponible.cantidad : 0; // Revertir al valor anterior o al máximo permitido
                return false;
            }
        }
        articulo.paresLibres.forEach(function (cant, i) {
            console.log('cant', cant, 'i', i)
          if (cant === null || typeof articulo.paresLibres[i] !== 'number' || articulo.paresLibres[i] < 0 || articulo.paresLibres[i] >= 1000) {
            if (index !== i) {
              articulo.paresLibres[i] = 0;
            } else {
              allGood = false;
            }
          }
        });
        if (allGood) {
          ServiceCliente.updateLibre(articulo, commonCallback);
        }
      };

      $scope.sumTotalColumna = function (articulo, index) {
        var total = 0;
        articulo.curvas.forEach(function (curva) {
          if (curva.unidadesSeleccionadas > 0) {
            total += funciones.toInt(curva.cantidades[index]) * funciones.toInt(curva.unidadesSeleccionadas);
          }
        });
        if (articulo.paresLibres[index]) {
          total += funciones.toInt(articulo.paresLibres[index]);
        }
        return total;
      };

      $scope.sumTotalArticuloPares = function (articulo) {
        var total = 0;
        //editado
        for (subArticulo of articulo.subArticulos) {
            subArticulo.curvas.forEach(function (curva) {
              if (curva.unidadesSeleccionadas > 0) {
                curva.cantidades.forEach(function (cantidad) {
                  total += funciones.toInt(cantidad) * curva.unidadesSeleccionadas;
                });
              }
            });
            subArticulo.paresLibres.forEach(function (cantidad) {
              total += funciones.toInt(cantidad);
            });
        }
        return total;
      };

      $scope.sumTotalArticuloDescuento = function (articulo) {
        var pares = $scope.sumTotalArticuloPares(articulo);
        if (pares) {
          return (funciones.toFloat($scope.getPrecioMayorista(articulo)) * pares) * ($scope.descuento / 100);
        }
        return 0;
      };

      $scope.sumTotalArticuloCosto = function (articulo) {
        var pares = $scope.sumTotalArticuloPares(articulo);
        if (pares) {
          return (funciones.toFloat($scope.getPrecioMayorista(articulo)) * pares) - $scope.sumTotalArticuloDescuento(articulo);
        }
        return 0;
      };

      $scope.sumTotalPares = function () {
        var total = 0;
        //editado?
        for (const idLinea of Object.keys($scope.favoritos)) {
            total += $scope.sumTotalParesPorLinea(idLinea);
        }
        return total;
      };

      $scope.sumTotalParesPorLinea = function (idLinea) {
        var total = 0;
        //editado
       for (const key of Object.keys($scope.favoritos[idLinea].items)) {
          const articulo = $scope.favoritos[idLinea].items[key];
          total += $scope.sumTotalArticuloPares(articulo);
        }
        return total;
      };

      $scope.sumTotalDescuento = function () {
        var total = 0;
        Object.keys($scope.favoritos).forEach(function (idLinea) {
          total += $scope.sumTotalDescuentoPorLinea(idLinea);
        });
        return total;
      };

      $scope.sumTotalDescuentoPorLinea = function (idLinea) {
        var total = 0;
        $scope.favoritos[idLinea].items.forEach(function (articulo) {
          total += $scope.sumTotalArticuloDescuento(articulo);
        });
        return total;
      };

      $scope.sumTotalCosto = function () {
        var total = 0;
        Object.keys($scope.favoritos).forEach(function (idLinea) {
          total += $scope.sumTotalCostoPorLinea(idLinea);
        });
        return total;
      };

      $scope.sumTotalCostoPorLinea = function (idLinea) {
        var total = 0;
        for (const key of Object.keys($scope.favoritos[idLinea].items)) {
            const articulo = $scope.favoritos[idLinea].items[key];
            total += $scope.sumTotalArticuloCosto(articulo);
        }
        return total;
      };

      $scope.getName = function (articulo) {
        return !articulo ? '' : articulo.articulo.nombre + ' - ' +  articulo.idArticulo + ' ' + articulo.idColorPorArticulo;
      };

      $scope.getPrecioMayorista = function (articulo) {
        //editado
        let art = articulo
        if (articulo.fav) {
            art = articulo.fav
        }
        return funciones.formatearMoneda(art.precioMayorista - (art.colorPorArticulo.tipoProductoStock.descuentoPorc / 100) * art.precioMayorista);
      };

      $scope.getPrecioMinorista = function (articulo) {
        //editado
        let art = articulo
        if (articulo.fav) {
            art = articulo.fav
        }
        return art.precioMinorista;
      };

      $scope.getImageUrl = function (articulo) {
        return articulo ? $scope.imagesUrl + articulo.idArticulo + articulo.idColorPorArticulo + '_e.jpg' : $scope.getEmptyImageUrl();
      };

      $scope.getEmptyImageUrl = function () {
        return $scope.imagesUrl + 'empty.jpg';
      };

      $scope.getUnavailableImageUrl = function () {
        return $scope.imagesUrl + 'empty.jpg';
      };

      /* Favoritos */
      $scope.toggleFavorito = function (articulo) {
        //console.log(articulo)
        var cb = function (err, result) {
          if (err) {
            $.error(err);
          } else {
            $scope.favoritos[articulo.idLinea].items.splice($scope.favoritos[articulo.idLinea].items.indexOf(articulo), 1);
            if (!$scope.favoritos[articulo.idLinea].items.length) {
              delete $scope.favoritos[articulo.idLinea];
            }
            articulo.favorito = !articulo.favorito;
          }
        };
        ServiceCliente.removeFavorito(articulo, cb);
      };

    $scope.isLoading = false;
    $scope.removeAllFavs = async function () {
        $scope.isLoading = true; 
           try {
            let res = await ServiceCliente.removeAllFavs();
            console.log(res);
            location.reload(); // Refresh page after completion
        } catch (error) {
            console.error("Error removing favorites:", error);
        } finally {
            $scope.isLoading = false; // Re-enable button (if needed before refresh)
            $scope.$apply(); // Ensure UI updates (since async function is outside Angular's digest cycle)
        }
    }

      $scope.toggleFavorito2 = async function (articulo) {
        console.log('articulo', articulo);

        let favorites = [];
        for (item of articulo.subArticulos) {
          favorites.push({
            idArticulo : item.idArticulo,
            idColorPorArticulo : item.idColorPorArticulo
          })
        }
        console.log("favorites", favorites)
        
        if (articulo.favorito) {
          let res = await ServiceCliente.removeFavoritoBatch(favorites);
          if (res.status == 200) {
           $scope.$apply(() => {
            articulo.favorito = false;
          });
          }
        } else {
          let res = await ServiceCliente.addFavoritoBatch(favorites);
          if (res.status == 200) {
           $scope.$apply(() => {
            articulo.favorito = true;
          });
          }
        }     
        //window.location('/favoritos/')   
      };

      $scope.showSucursales = false;
      $scope.confirmarPedido = function (idSucursalPedido) {
        ServiceCliente.confirmarPedido({idSucursal: idSucursalPedido}, function (err, result) {
          if (err) {
            $.error(err);
          } else {
            $.growl.notice('Pedido guardado correctamente');
            // $scope.limpiarCantidades(); Se quieren borrar los favoritos enteramente
            $scope.favoritos = [];
          }
        });
      };

      /* No funcionaría si lo descomentara, porque cambió la estructura de $scope.favoritos (ahora tiene idLinea)
      $scope.limpiarCantidades = function () {
        angular.forEach($scope.favoritos, function (articulo, i) {
          angular.forEach(articulo.paresLibres, function (parLibre, j) {
            articulo.paresLibres[j] = 0;
          });
          angular.forEach(articulo.curvas, function (curva, j) {
            articulo.curvas[j].unidadesSeleccionadas = 0
          });
        });
      };
      */
    });
</script>

<div id="favoritos" ng-controller="FavoritosCtrl">
    <h1 class="text-left hidden-xs">Favoritos / Nuevo pedido</h1>
    <h2 class="text-center visible-xs">Favoritos / Nuevo pedido</h2>
    <div ng-if="!show(favoritos)">
        <div class="well big">
            <h1>Aun no se han seleccionado favoritos</h1>
        </div>
    </div>
    <div ng-if="show(favoritos)">
        <div class="btn btn-danger btn-generar-pedido" ng-click="removeAllFavs()" ng-disabled="isLoading">
            <i class="fa fa-fw fa-ban"></i>Sacar Todos los Favoritos<i class="fa fa-fw fa-ban"></i>
        </div>
        <div class="row well">
            <div class="col-xs-12">
                <h2>Detalles del pedido</h2>
                <div class="detalle-pedido col-totales">
                    <table>
                        <tbody>
                            <tr>
                                <td class="titulo">Pares</td>
                                <td class="valor total-pares">{{ sumTotalPares() }}</td>
                            </tr>
                            <tr ng-if="descuento > 0">
                                <td class="titulo">Descuentos</td>
                                <td class="valor">{{ funciones.formatearMoneda(sumTotalDescuento()) }}</td>
                            </tr>
                            <tr>
                                <td class="titulo no-border">$ Total</td>
                                <td class="valor no-border total">{{ funciones.formatearMoneda(sumTotalCosto()) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 15px;">
                    <button type="button" class="btn btn-generar-pedido" ng-click="showSucursales = true" ng-show="!showSucursales">
                        <i class="fa fa-fw fa-shopping-cart"></i> Generar pedido
                    </button>
                    <a href="/favoritos/reporte/" target="_blank" class="btn btn-generar-pedido">
                        <i class="fa fa-fw fa-file"></i> Generar Reporte
                    </a>
                    <div class="form-group" ng-show="showSucursales">
                        <label for="sucursal">Sucursal de entrega:</label>
                        <select class="form-control" id="sucursal" ng-model="idSucursalPedido" style="max-width: 320px; margin: 0 auto;">
                            <option ng-repeat="sucursal in sucursales" value="{{ sucursal.id }}">{{ sucursal.id }} - {{ sucursal.nombre }}</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-generar-pedido btn-success" ng-click="confirmarPedido(idSucursalPedido)" ng-show="showSucursales">
                        <i class="fa fa-fw fa-shopping-cart"></i> Confirmar pedido
                    </button>
                    <button type="button" class="btn btn-generar-pedido" ng-click="showSucursales = false" ng-show="showSucursales">
                        <i class="fa fa-fw fa-times"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
        <div class="row" ng-repeat="(idLinea, linea) in favoritos">
            <div class="col-xs-12">
                <div class="row total">
                    <div class="col-xs-12">
                        <h2><b>[{{ linea.nombre }}]</b> {{ sumTotalParesPorLinea(idLinea) }} pares - {{ funciones.formatearMoneda(sumTotalCostoPorLinea(idLinea)) }}</h2>
                    </div>
                </div>
                <div class="row favorito" ng-repeat="articulo in linea.items">
                    <div class="col-sm-3 item-imagen">
                        <div class="item-inner">
                            <a href="javascript:;" picture-modal>
                                <img ng-src="{{getImageUrl(articulo.fav)}}" default-src="{{getUnavailableImageUrl()}}">
                            </a>
                            <div class="item-tipo" style="width: 65%">
                                <!--<span class="badge" ng-class="{'badge-danger': articulo.colorPorArticulo.tipoProductoStock.id == '1'}">{{ articulo.fav.colorPorArticulo.tipoProductoStock.nombre }}</span>
                                <span class="badge inverted">{{ articulo.fav.formaDeComercializacion }}</span>
                                <span class="badge badge-danger" ng-if="articulo.colorPorArticulo.tipoProductoStock.descuentoPorc">-{{ articulo.fav.colorPorArticulo.tipoProductoStock.descuentoPorc }}%</span>-->
                            </div>
                            <div class="item-star">
                                <a id="{{getIdStarArticulo(articulo)}}" href="javascript:;" ng-click="toggleFavorito2(articulo)" data-articulo={{articulo}}>
                                    <i class="fa fa-2-5x" ng-class="articulo.favorito ? 'star-on' : 'star-off'"></i>
                                </a>
                            </div>

                            <!-- nuevo para mostrar los subArticulos con colorPorArticulo y los badges -->
                            <div class="item-precios">
                             <!--<span>{{ funciones.formatearMoneda(getPrecioMayorista(articulo)) }} / {{ funciones.formatearMoneda(getPrecioMinorista(articulo)) }}</span> -->
                                <span class="subarticulo sub-inline-grid" ng-repeat="subArticulo in articulo.subArticulos">
                                
                                  <span>{{ subArticulo.nombre + ' - ' + subArticulo.idArticulo + ' ' + subArticulo.idColorPorArticulo }}</span>
                                  <span>
                                    <span data-toggle="popover" data-html="true" data-placement="bottom" tabindex="0"  item-stock={{subArticulo.cantidadTalles}}>
                                      <span class="badge item-stock-badge">Stock: {{ subArticulo.stockTotal }}</span>
                                    </span>
                                       <span class="badge">Pronto: <span class="stock-produccion" stock-produccion={{getArticuloCodigoColor(subArticulo)}}>0</span> </span> 
                                 </span>
                                </span>
                            </div>

                            <div class="subitem-talles">
                                <span class="subarticulo" ng-repeat="subarticuloi in articulo.subArticulos">
                                    <span>
                                      <span class="badge">{{ subarticuloi.primerTalle }} - {{ subarticuloi.ultimoTalle }}</span>
                                      <span class="badge inverted">{{ subarticuloi.formaDeComercializacion }}</span>
                                    </span>
                                    <span>
                                        <span class="badge"  class="{'badge-danger': subarticuloi.colorPorArticulo.tipoProductoStock.id == '1'}">{{ subarticuloi.colorPorArticulo.tipoProductoStock.nombre }}</span>
                                        <span class="badge badge-danger" ng-if="subarticuloi.colorPorArticulo.tipoProductoStock.descuentoPorc">-{{ subarticuloi.colorPorArticulo.tipoProductoStock.descuentoPorc }}%</span>
                                    </span> 
                                    <span>  | {{ funciones.formatearMoneda(getPrecioMayorista(subarticuloi)) }} </span>
                                </span> 
                            </div>  
                            <!--<div class="item-name">
                                {{getName(articulo.fav)}}
                            </div>-->
                        </div>
                    </div>
                    <div class="col-sm-7">
                      <div ng-repeat="subArticulo in articulo.subArticulos">
                        
                        <table class="tabla-curvas">
                            <thead>
                            <tr class="row-talles">
                                <th>{{ subArticulo.idArticulo + ' ' + subArticulo.idColorPorArticulo }}</th>
                                <th ng-repeat="talle in subArticulo.talles">{{ talle }}</th>
                                <th ng-if="articulo.formaDeComercializacion == 'M'">Cant.</th>
                                <th ng-if="articulo.formaDeComercializacion == 'M'">+/-</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="row-stock">
                                <td>Stock</td>
                                <td class="aCenter" ng-repeat="(i, talle) in subArticulo.talles">{{ subArticulo.stock[i + 1] }}</td>
                                <td ng-if="subArticulo.formaDeComercializacion == 'M'" colspan="3"></td>
                            </tr>
                            <tr class="row-curva" ng-if="subArticulo.formaDeComercializacion == 'L' || subArticulo.formaDeComercializacion == 'T'">
                                <!-- LIBRE -->
                                <td style="padding: 3px 0;">Libre</td>
                                <td ng-repeat="(i, talle) in subArticulo.talles">
                                    <input maxlength="3" type="number" step="1" min="0" max="999" ng-model="subArticulo.paresLibres[i]" ng-change="updateLibre(subArticulo, i, talle)" />
                                </td>
                            </tr>
                            <tr class="row-curva" ng-if="subArticulo.formaDeComercializacion == 'M'" ng-repeat="(j, curva) in subArticulo.curvas">
                                <!-- Modular -->
                                <td>Curva {{ j+1 }}</td>
                                <td ng-repeat="(i, talle) in subArticulo.talles">{{ curva.cantidades[i] }}</td>
                                <td class="col-curvas-cantidad">{{ curva.unidadesSeleccionadas }}</td>
                                <td>
                                    <button type="button" class="btn" ng-click="addCurva(subArticulo, curva)"><i class="fa fa-fw fa-plus"></i></button>
                                    <button type="button" class="btn" ng-disabled="curva.unidadesSeleccionadas <= 0" ng-click="removeCurva(subArticulo, curva)"><i class="fa fa-fw fa-minus"></i></button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <!-- Totales -->
                            <tr class="row-totales">
                                <td id="aCenter">Total</td>
                                <td ng-repeat="(i, talle) in subArticulo.talles">{{ sumTotalColumna(subArticulo, i) }}</td>
                                <td colspan="{{ subArticulo.formaDeComercializacion == 'M' ? '2' : '1' }}" style="background: #eaeaea;"></td>
                            </tr>
                            </tfoot>
                        </table>

                      </div>
                    </div>
                    <div class="col-sm-2 col-totales">
                        <table>
                            <tbody>
                            <tr>
                                <td class="titulo">P. Minor.</td>
                                <td class="valor">{{ funciones.formatearMoneda(getPrecioMinorista(articulo)) }}</td>
                            </tr>
                            <tr>
                                <td class="titulo">P. Mayor.</td>
                                <td class="valor">{{ funciones.formatearMoneda(getPrecioMayorista(articulo)) }}</td>
                            </tr>
                            <tr>
                                <td class="titulo">Pares</td>
                                <td class="valor total-pares">{{ sumTotalArticuloPares(articulo) }}</td>
                            </tr>
                            <tr ng-if="descuento > 0">
                                <td class="titulo">Descuento</td>
                                <td class="valor">{{ funciones.formatearMoneda(sumTotalArticuloDescuento(articulo)) }}</td>
                            </tr>
                            <tr>
                                <td class="titulo no-border">$ Total</td>
                                <td class="valor no-border total">{{ funciones.formatearMoneda(sumTotalArticuloCosto(articulo)) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
