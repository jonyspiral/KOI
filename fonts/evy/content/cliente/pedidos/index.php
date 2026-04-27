<?php

// Favoritos
$pedidos = Base::getListObject('PedidoCliente', 'cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->cliente->id));

$arrayPedidos = array();
foreach ($pedidos as $pedido) {
    /** @var PedidoCliente $pedido */

    $detallePorRangoTalle = array();
    foreach ($pedido->detalle as $detalle) {
        // Datos básicos
        $item = array(
            'idArticulo' => $detalle->idArticulo,
            'idColorPorArticulo' => $detalle->idColorPorArticulo,
            'articulo' => array(
                'nombre' => $detalle->articulo->nombre
            ),
            'colorPorArticulo' => array(
                'nombre' => $detalle->colorPorArticulo->nombre
            ),
            'precioUnitario' => $detalle->precioUnitario,
            'cantidades' => array(),
            'importeTotal' => $detalle->calcularImporteTotal(),
            'cantidadPares' => $detalle->calcularTotalPares(),
        );

        // Cantidades
        foreach ($detalle->cantidades as $cant) {
            $item['cantidades'][] = Funciones::toInt($cant);
        }

        if (!array_key_exists($detalle->articulo->rangoTalle->id, $detallePorRangoTalle)) {
            // Rango talle
            $talles = array();
            foreach ($detalle->articulo->rangoTalle->posicion as $pos) {
                if (isset($pos)) {
                    $talles[] = $pos;
                }
            }

            $detallePorRangoTalle[$detalle->articulo->rangoTalle->id] = array(
                'items' => array(),
                'talles' => $talles
            );
        }

        $detallePorRangoTalle[$detalle->articulo->rangoTalle->id]['items'][] = $item;
    }

    // Datos básicos
    $ped = array(
        'id' => $pedido->id,
        'anulado' => $pedido->anulado(),
        'sucursal' => array(
            'id' => $pedido->idSucursal,
            'nombre' => $pedido->sucursal->nombre,
            'direccion' => $pedido->sucursal->direccionCalle . ' ' . $pedido->sucursal->direccionNumero,
            'direccionLocalidad' => $pedido->sucursal->direccionCalle . ' ' . $pedido->sucursal->direccionLocalidad->nombre,
            'direccionProvincia' => $pedido->sucursal->direccionCalle . ' ' . $pedido->sucursal->direccionProvincia->nombre
        ),
        'importeTotal' => $pedido->importeTotal,
        'cantidadPares' => $pedido->calcularTotalPares(),
        'estado' => $pedido->estado,
        'detalle' => $detallePorRangoTalle,
        'abierto' => false,
        'confirmandoBorrar' => false
    );

    $arrayPedidos[] = $ped;
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

    /* Curvas */
    .tabla-pedidos {
        width: 100%;
    }
    .row-pedido {
        cursor: pointer;
    }
    .row-pedido.par {
        background-color: #efefef;
    }
    .row-pedido.impar {
        background-color: #d8d7d7;
    }
    .row-pedido.anulado {
        background-color: #ffa3a3;
    }
    .row-pedido.curso {
        background-color: #91e291;
    }
    .row-header th, .row-talles th {
        font-size: 15px;
        font-weight: bold;
        text-align: center;
        background-color: #333333;
        color: #FFFFFF;
        padding: 1px 0;
    }
    .row-pedido>td {
        padding: 5px 0;
    }
    .bigger {
        font-size: 15px;
        font-weight: bold;
    }
    .tabla-cantidades {
        width: 70%;
        margin: 10px auto;
    }
    .tabla-cantidades th, .tabla-cantidades td {
        border: 1px solid white;
    }
    .row-cantidades {
        background-color: #eaeaea;
    }

    /* Tabla de detalles del pedido */
    .detalle-pedido > table {
        height: 80px;
        margin: 0 auto;
        max-width: 320px;
        text-align: left;
        font-size: 17px;
    }
    .detalle-pedido>td {
        box-shadow: inset 0 0 0 1px #d8d7d7;
    }
</style>

<script>

    Koi.controller('PedidosCtrl', function ($scope, ServiceCliente) {

      $scope.funciones = funciones;
      $scope.pedidos = <? echo json_encode($arrayPedidos); ?>;

      $scope.toggleDetalle = function (pedido) {
        pedido.abierto = !pedido.abierto;
      };

      $scope.getName = function (articulo) {
        return !articulo ? '' : articulo.articulo.nombre + ' - ' +  articulo.idArticulo + ' ' + articulo.idColorPorArticulo;
      };

      $scope.showSucursales = false;
      $scope.cancelarPedido = function (pedido) {
        ServiceCliente.cancelarPedido(pedido, function (err, result) {
          if (err) {
            $.error(err);
          } else {
            $.growl.notice('Pedido cancelado correctamente');
            pedido.anulado = true;
          }
        });
      };
    });
</script>

<div id="pedidos" ng-controller="PedidosCtrl">
    <h1 class="text-left hidden-xs">Mis pedidos</h1>
    <h2 class="text-center visible-xs">Mis pedidos</h2>
    <div ng-if="!pedidos.length">
        <div class="well big">
            <h1>Aún no se han realizado pedidos</h1>
        </div>
    </div>
    <div ng-if="pedidos.length">
        <table class="tabla-pedidos">
            <thead>
                <tr class="row-header">
                    <th></th><!-- Ver detalle (caret para abajo o para el costado) -->
                    <th>#</th>
                    <th>Estado</th>
                    <th>Sucursal</th>
                    <th>Pares</th>
                    <th>Total</th>
                    <th></th><!-- Acciones ("Cancelar pedido") -->
                </tr>
            </thead>
            <tbody ng-repeat="(i, pedido) in pedidos">
                <tr class="row-pedido" ng-class="pedido.anulado ? 'anulado' : (pedido.estado == 'C' ? 'curso' : (i % 2 ? 'impar' : 'par'))">
                    <td><a href="javascript:;" ng-click="toggleDetalle(pedido)"><i class="fa fa-fw" ng-class="pedido.abierto ? 'fa-caret-down' : 'fa-caret-right'"></i></a></td>
                    <td ng-click="toggleDetalle(pedido)">{{ pedido.id }}</td>
                    <td ng-class="pedido.estado == 'C' ? 'pedido-encurso' : 'pedido-pendiente'" ng-click="toggleDetalle(pedido)">
                        {{ pedido.anulado ? 'Cancelado' : (pedido.estado == 'C' ? 'En curso' : 'Pendiente de aprobación') }}
                    </td>
                    <td ng-click="toggleDetalle(pedido)">[{{ pedido.sucursal.id }}] {{ pedido.sucursal.nombre }} - {{ pedido.sucursal.direccion }}</td>
                    <td class="bigger" ng-click="toggleDetalle(pedido)">{{ pedido.cantidadPares }}</td>
                    <td class="bigger" ng-click="toggleDetalle(pedido)">{{ funciones.formatearMoneda(pedido.importeTotal) }}</td>
                    <td>
                        <div>
                            <button type="button" class="btn" ng-click="funciones.pdfClick(funciones.controllerUrl('getPdf', {id: pedido.id}, '/cliente/pedidos/'))" ng-show="!pedido.confirmandoBorrar && !pedido.anulado">
                                <i class="fa fa-fw fa-file-pdf-o"></i>
                            </button>
                            <button type="button" class="btn" ng-click="pedido.confirmandoBorrar = true" ng-show="!pedido.confirmandoBorrar && !pedido.anulado && pedido.estado != 'C'">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                        <div ng-show="pedido.confirmandoBorrar && !pedido.anulado && pedido.estado != 'C'">
                            <div class="bold">¿Borrar?</div>
                            <button type="button" class="btn btn-danger" ng-click="cancelarPedido(pedido)"><i class="fa fa-fw fa-check"></i></button>
                            <button type="button" class="btn" ng-click="pedido.confirmandoBorrar = false"><i class="fa fa-fw fa-times"></i></button>
                        </div>
                    </td>
                </tr>
                <tr class="detalle-pedido" ng-show="pedido.abierto">
                    <td colspan="7">
                        <table class="tabla-cantidades" ng-repeat="(idRango, rango) in pedido.detalle">
                            <thead>
                            <tr class="row-talles">
                                <th>Articulo</th>
                                <th ng-repeat="talle in rango.talles">{{ talle }}</th>
                                <th>Cant.</th>
                                <th>$ Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="row-cantidades" ng-repeat="articulo in rango.items">
                                <td>{{ getName(articulo) }}</td>
                                <td class="aCenter" ng-repeat="(i, talle) in rango.talles">{{ articulo.cantidades[i] }}</td>
                                <td class="bigger">{{ articulo.cantidadPares }}</td>
                                <td class="bigger">{{ funciones.formatearMoneda(articulo.importeTotal) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
