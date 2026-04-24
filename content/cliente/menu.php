<?php

$menuactual = (array_key_exists('c', $_REQUEST)) ? $_REQUEST['c'] : '';
$submenuactual = (array_key_exists('f', $_REQUEST)) ? $_REQUEST['f'] : '';

$catalogo = Catalogo::ultimo();

$arrayTipos = array();
$where = 'mostrar_en_catalogo = ' . Datos::objectToDB('S');
$order = ' ORDER BY id_tipo_producto_stock';
$tiposProductoStock = Factory::getInstance()->getListObject('TipoProductoStock', $where . $order);

foreach ($tiposProductoStock as $tipo) {
    $arrayTipos[Funciones::toString($tipo->id)] = $tipo->nombreCatalogo;
}

$filtrosDefault = array('1', '2', '3', '4');
if ($filtrosSession = Funciones::session('catalogo_filtros')) {
    try {
        $filtrosDefault = json_decode($filtrosSession, true);
    } catch (Exception $ex) { }
}

?>

<style>
    .filtros {
        padding: 15px;
        text-align: left;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .filtros h4 {
        text-align: center;
        margin-bottom: 13px;
    }
    .filtros .filtro-box {
        /*border: 1px solid #a7a7a7;*/
        margin-top: 15px;
        padding: 0 15px;
    }
    .radio label, .checkbox label {
        min-height: 14px;
        font-size: 14px;
        line-height: 14px;
    }
    input[type="checkbox"] {
        width: 16px;
        height: 16px !important;
        margin: 0;
    }
    .badge-danger {
  background-color: #e74c3c;
  color: white;
  padding: 2px 5px;
  font-size: 10px;
  border-radius: 3px;
  margin-left: 5px;
  text-transform: uppercase;
}

</style>

<script>
    var menu;

    function toggleMenu() {
      if (menu.hasClass('sidebar-show')) {
        hideMenu();
      } else {
        menu.addClass('sidebar-show');
        menu.focus();
      }
    }

    function hideMenu() {
      menu.removeClass('sidebar-show');
    }

    $(document).ready(function () {
      menu = $('#sidebar');

      $('.sidebar-button').unbind('click').bind('click', toggleMenu);

      $(document).click(function (event) {
        if (! $(event.target).closest('#sidebar, .sidebar-button').length) {
          hideMenu();
        }
      })
    });

    Koi.controller('FiltrosCtrl', function ($scope, $timeout, ServiceCatalogo) {

      $scope.totalFiltros = 0;
      $scope.totalFiltrosActivos = 0;

      $scope.hayQueMostrarFiltros = function () {
        return ServiceCatalogo.filtros.show;
      };

      $scope.tiposProductoStock = <?= json_encode($arrayTipos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

      $scope.filtros = {
        tipoProductoStock: {}
      };

      $scope.changeFiltro = function (idFiltro) {
        var arrayFiltros = [];
        angular.forEach($scope.filtros[idFiltro], function (value, key) {
          if (value) {
            arrayFiltros.push(key);
          }
        });
        ServiceCatalogo.filtros.set(idFiltro, arrayFiltros);
        ServiceCatalogo.actualizarFiltros(arrayFiltros);

        $scope.actualizarTotalFiltrosActivos();
      };

      $scope.actualizarTotalFiltrosActivos = function () {
        $scope.totalFiltrosActivos = 0;
        angular.forEach($scope.filtros, function (filtro) {
          angular.forEach(filtro, function (value) {
            $scope.totalFiltrosActivos += value ? 1 : 0;
          });
        });
      };

      $timeout(function () {
        $scope.filtrosDefault = <?= json_encode($filtrosDefault, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        angular.forEach($scope.filtrosDefault, function (filtro) {
          $scope.filtros.tipoProductoStock[filtro] = true; // '1'
        });
        $scope.changeFiltro('tipoProductoStock');
      }, 200);

      $scope.totalFiltros += Object.keys($scope.tiposProductoStock).length;
    });
</script>

<aside id="sidebar" class="sidebar" sidebar ng-controller="FiltrosCtrl">
    <div id="sidebar-button" class="sidebar-button black-button hidden-xs">
        <div class="sidebar-button-help black-button hidden-xs" ng-show="hayQueMostrarFiltros()">
            FILTROS
            <span class="sidebar-button-help-badge">({{ totalFiltrosActivos }}/{{ totalFiltros }})</span>
        </div>
        <div>
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
    </div>

    <div data-scrollbar="true" data-height="100%">
        <div class="filtros" ng-show="hayQueMostrarFiltros()">
            <h3>FILTROS</h3>
            <div class="filtro-box">
                <div class="checkbox" ng-repeat="(idTipo, nombreTipo) in tiposProductoStock">
                    <label>
                        <input type="checkbox" ng-change="changeFiltro('tipoProductoStock')" ng-model="filtros['tipoProductoStock'][idTipo]">
                        {{ nombreTipo }}
                    </label>
                </div>
                <hr>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" ng-change="changeFiltro('tipoProductoStock')" ng-model="filtros['tipoProductoStock'][12]">
                        Con Stock
                    </label>
                </div>
            </div>
        </div>

        <ul class="nav">
            <li><a href="/">INICIO</a></li>

            <?php
            // Lista de palabras clave para marcar como NEW
            $keywords = array('drop', 'trip', 'y2', 'g.o.a.t basic', 'pow aircush',  );

            foreach ($catalogo->secciones as $seccion) {
                $menuActive = $menuactual == $seccion->idLineaProducto;
                $familiasHtml = '';
                $seccionTienePalabraClave = false;

                foreach ($seccion->familias as $familia) {
                    $isActive = ($menuActive && $submenuactual == $familia->idFamiliaProducto ? ' active' : '');
                    $nombre = $familia->familiaProducto->nombre;

                    // Buscar coincidencia con keywords
                    $tienePalabraClave = false;
                    foreach ($keywords as $kw) {
                        if (stripos($nombre, $kw) !== false) {
                            $tienePalabraClave = true;
                            $seccionTienePalabraClave = true;
                            break;
                        }
                    }

                    $icono = $tienePalabraClave ? ' <span class="badge badge-danger">NEW</span>' : '';
                    $familiasHtml .= '<li class="' . $isActive . '">';
                    $familiasHtml .= '<a href="/catalogo/?c=' . $seccion->idLineaProducto . '&f=' . $familia->idFamiliaProducto . '">' . $nombre . $icono . '</a></li>';
                }

                $tituloPadre = $seccion->lineaProducto->tituloCatalogo;
                $iconoPadre = $seccionTienePalabraClave ? ' <span class="badge badge-danger">NEW</span>' : '';

                echo '
                    <li class="has-sub' . ($menuActive ? ' active' : '') . '">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            <span>' . $tituloPadre . $iconoPadre . '</span>
                        </a>
                        <ul class="sub-menu">
                            <li><a href="/catalogo/?c=' . $seccion->idLineaProducto . '&f=all">Todos</a></li>
                            ' . $familiasHtml . '
                        </ul>
                    </li>';
            }
            ?>
        </ul>
    </div>
</aside>
