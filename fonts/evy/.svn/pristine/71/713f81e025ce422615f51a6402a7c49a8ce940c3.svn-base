<?php

$menuactual = (array_key_exists('c', $_REQUEST)) ? $_REQUEST['c'] : '';
$submenuactual = (array_key_exists('f', $_REQUEST)) ? $_REQUEST['f'] : '';

$catalogo = Catalogo::ultimo();

$arrayTipos = array();
$tiposProductoStock = Factory::getInstance()->getListObject('TipoProductoStock', 'mostrar_en_catalogo = ' . Datos::objectToDB('S'));
foreach ($tiposProductoStock as $tipo) {
    $arrayTipos[Funciones::toString($tipo->id)] = $tipo->nombreCatalogo;
}

$filtrosDefault = array('1', '2');
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

      $scope.tiposProductoStock = <? echo json_encode($arrayTipos) ?>;

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
        $scope.filtrosDefault = <? echo json_encode($filtrosDefault); ?>;
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
        <div class="sidebar-button-help black-button" ng-show="hayQueMostrarFiltros()">
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
                <!--<h4>Tipo de producto</h4>-->
                <div class="checkbox" ng-repeat="(idTipo, nombreTipo) in tiposProductoStock">
                    <label><input type="checkbox" ng-change="changeFiltro('tipoProductoStock')" ng-model="filtros['tipoProductoStock'][idTipo]">{{ nombreTipo }}</label>
                </div>
            </div>
        </div>
        <ul class="nav">
            <li><a href="/">INICIO</a></li>
            <?php
                foreach ($catalogo->secciones as $seccion) {
                    $menuActive = $menuactual == $seccion->idLineaProducto;
                    $html = '
                        <li class="has-sub' . ($menuActive ? ' active' : '') . '">
                            <a href="javascript:;">
                                <b class="caret pull-right"></b>
                                <!--<i class="fa fa-user-secret"></i>-->
                                <span>' . $seccion->lineaProducto->tituloCatalogo . '</span>
                            </a>
                            <ul class="sub-menu">';

                    foreach ($seccion->familias as $familia) {
                        $html .= '<li class="'. ($menuActive && $submenuactual == $familia->idFamiliaProducto ? ' active' : '') . '">';
                        $html .= '<a href="/catalogo/?c=' . $seccion->idLineaProducto . '&f=' . $familia->idFamiliaProducto . '">' . $familia->familiaProducto->nombre . '</a></li>';
                        $html .= '</li>';
                    }

                    $html .= '</ul></li>';

                    echo $html;
                }
            ?>
        </ul>
    </div>
</aside>
