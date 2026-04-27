<?php

$menuactual = (array_key_exists('c', $_REQUEST)) ? $_REQUEST['c'] : '';
$submenuactual = (array_key_exists('f', $_REQUEST)) ? $_REQUEST['f'] : '';

$catalogo = Catalogo::ultimo();
$familia = CatalogoSeccionFamilia::find($catalogo->id, $menuactual, $submenuactual);

$distribuidor = Usuario::logueado()->cliente->listaAplicable == 'D';

// Favoritos
$favoritos = Base::getListObject('FavoritoCliente', 'cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->cliente->id));
$arrayFavoritos = array();
foreach ($favoritos as $favorito) {
    $arrayFavoritos[] = $favorito->idArticulo . '_' . $favorito->idColorPorArticulo;
}

// Stock
$stock = array();
$where = '';
foreach ($familia->articulos as $articulo) {
    /** @var CatalogoSeccionFamiliaArticulo $articulo */
    $where .= '(cod_articulo = ' . Datos::objectToDB($articulo->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($articulo->idColorPorArticulo) . ') OR ';
}
$where = 'cod_almacen = ' . Datos::objectToDB('01') . ' AND (' . trim($where, ' OR ') . ')';
$stocks = Factory::getInstance()->getArrayFromView('stock_menos_pendiente_vw', $where);
foreach ($stocks as $item) {
    for ($j = 1; $j <= 10; $j++) {
        if (!array_key_exists($item['cod_articulo'], $stock)) {
            $stock[$item['cod_articulo']] = array();
        }
        if (!array_key_exists($item['cod_color_articulo'], $stock[$item['cod_articulo']])) {
            $stock[$item['cod_articulo']][$item['cod_color_articulo']] = array();
        }
        $stock[$item['cod_articulo']][$item['cod_color_articulo']][$j] = Funciones::toNatural(Funciones::keyIsSet($item, 'S' . $j, 0));
    }
}

// Expand
$articulos = array();
foreach ($familia->articulos as $articulo) {
    $articulos[] = array(
        'idArticulo' => $articulo->idArticulo,
        'idColorPorArticulo' => $articulo->idColorPorArticulo,
        'articulo' => array(
            'nombre' => $articulo->articulo->nombre
        ),
        'colorPorArticulo' => array(
            'nombre' => $articulo->colorPorArticulo->nombre,
            'tipoProductoStock' => array(
                'id' => $articulo->colorPorArticulo->idTipoProductoStock,
                'nombre' => $articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                'descuentoPorc' => $articulo->colorPorArticulo->tipoProductoStock->descuentoPorc
            )
        ),
        'precioMayorista' => $distribuidor ? $articulo->colorPorArticulo->precioDistribuidor : $articulo->colorPorArticulo->precioMayoristaDolar,
        'precioMinorista' => $distribuidor ? $articulo->colorPorArticulo->precioDistribuidorMinorista : $articulo->colorPorArticulo->precioMinoristaDolar,
        'formaDeComercializacion' => $articulo->colorPorArticulo->formaDeComercializacion,
        'stock' => Funciones::sumaArray(Funciones::keyIsSet(Funciones::keyIsSet($stock, $articulo->idArticulo, array()), $articulo->idColorPorArticulo, array())),
        'favorito' => in_array($articulo->idArticulo . '_' . $articulo->idColorPorArticulo, $arrayFavoritos),
        'filtros' => false
    );
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

    @media (min-width: 768px) {
        .skip-row-top {
            padding-top: 25%;
        }
    }

    @media (min-width: 1200px) {
        .skip-row-top {
            padding-top: 16.666667%;
        }
    }
</style>

<script>

    $(document).ready(function() {
        <?
        if (!$menuactual || !$submenuactual) {
            echo 'window.location.href = "/";';
        }
        ?>
    });

    Koi.controller('CatalogoCtrl', function ($scope, ServiceCliente, ServiceCatalogo) {

      ServiceCatalogo.filtros.show = true;

      $scope.funciones = funciones;
      $scope.imagesUrl = 'http://www.spiralshoes.com/zapatillas/jpg/';
      $scope.articulos = <? echo json_encode($articulos); ?>;

      $scope.$on('Catalogo:FiltrosAplicados:changed', function (e, filtrosAplicados) {
        angular.forEach($scope.articulos, function (item) {
          item.filtros = filtrosAplicados.tipoProductoStock.indexOf((item.colorPorArticulo.tipoProductoStock.id + '')) >= 0
        })
      });

      $scope.getArticulo = function (index) {
        return $scope.articulos[($scope.pagina - 1) * $scope.cantPorPagina + index];
      };

      $scope.getName = function (articulo) {
        return !articulo ? '' : articulo.articulo.nombre + ' - ' +  articulo.idArticulo + ' ' + articulo.idColorPorArticulo;
      };

      $scope.getPrecioMayorista = function (articulo) {
        return articulo.precioMayorista - (articulo.colorPorArticulo.tipoProductoStock.descuentoPorc / 100) * articulo.precioMayorista;
      };

      $scope.getPrecioMinorista = function (articulo) {
        return articulo.precioMinorista;
      };

      $scope.getImageUrl = function (articulo) {
        return articulo ? $scope.imagesUrl + articulo.idArticulo + articulo.idColorPorArticulo + '_e.jpg' : $scope.getEmptyImageUrl();
      };

      $scope.getEmptyImageUrl = function () {
        return $scope.imagesUrl + 'empty.jpg';
      };

      $scope.getUnavailableImageUrl = function () {
        return $scope.imagesUrl + 'empty.jpg'; // unavailable.jpg
      };

      /* Favoritos */
      $scope.toggleFavorito = function (articulo) {
        var cb = function (err, result) {
          if (err) {
            $.error(err);
          } else {
            articulo.favorito = !articulo.favorito;
          }
        };
        articulo.favorito ? ServiceCliente.removeFavorito(articulo, cb) : ServiceCliente.addFavorito(articulo, cb);
      };
    });
</script>

<div id="catalogo" ng-controller="CatalogoCtrl">
    <!-- Mobile -->
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2 text-left skip-row-top">
            <h3><? echo $familia->lineaProducto->tituloCatalogo; ?></h3>
            <h1><? echo $familia->familiaProducto->nombre; ?></h1>
            <p><? echo $familia->descripcion; ?></p>
        </div>
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-6">
            <div class="row">
                <div class="col-xs-6 col-sm-4 col-md-4 item" ng-repeat="articulo in articulos" ng-show="articulo.filtros">
                    <div class="item-inner">
                        <a href="javascript:;" picture-modal>
                            <img ng-src="{{getImageUrl(articulo)}}" default-src="{{getUnavailableImageUrl()}}">
                        </a>
                        <div class="item-tipo">
                            <span class="badge" ng-class="{'badge-danger': articulo.colorPorArticulo.tipoProductoStock.id == '1'}">{{ articulo.colorPorArticulo.tipoProductoStock.nombre }}</span>
                            <span class="badge inverted">{{ articulo.formaDeComercializacion }}</span>
                            <span class="badge badge-danger" ng-if="articulo.colorPorArticulo.tipoProductoStock.descuentoPorc">-{{ articulo.colorPorArticulo.tipoProductoStock.descuentoPorc }}%</span>
                        </div>
                        <div class="item-precios">
                            <span>{{ funciones.formatearMoneda(getPrecioMayorista(articulo)) }} / {{ funciones.formatearMoneda(getPrecioMinorista(articulo)) }}</span>
                        </div>
                        <div class="item-stock">
                            <span>Stock: {{ articulo.stock }}</span>
                        </div>
                        <div class="item-star">
                            <a href="javascript:;" ng-click="toggleFavorito(articulo)">
                                <i class="fa fa-2-5x" ng-class="articulo.favorito ? 'star-on' : 'star-off'"></i>
                            </a>
                        </div>
                        <div class="item-name">
                            {{getName(articulo)}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 visible-lg" style="padding-left: 5px; padding-right: 0;">
            <img style="width: 100%;" ng-src="<? echo $familia->imagenLateral; ?>">
        </div>
    </div>
</div>
