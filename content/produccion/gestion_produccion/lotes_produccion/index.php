<?php
$idLoteDeProduccion = Funciones::get('id');
?>

<style>
</style>

<script type='text/javascript'>

    Koi.controller('AppCtrl', function ($scope) {

        $scope.modo = 'inicio';

        $scope.limpiarScreen = function() {
            $scope.curvas = [];
            $scope.tareas = [];
            $scope.lote = {
                ordenesDeFabricacion: []
            };
            $scope.lotes = [];
        };

        $scope.getLista = function() {
            $scope.limpiarScreen();
            funciones.get(funciones.controllerUrl('buscar'), {}, $.proxy(function(json) {
                this.lotes = json.data;
            }, $scope));
        };

        $scope.buscar = function(idBuscar) {
            $scope.limpiarScreen();
            var url = funciones.controllerUrl('buscar', {id: idBuscar});
            var msgError = 'El lote de producción "' + idBuscar + '" no existe.',
                cbSuccess = $.proxy(function(json){
                    this.lote = json;
                    funciones.cambiarTitulo(tituloPrograma + ' - Lote Nº ' + this.lote.id);
                }, $scope);
            funciones.buscar(url, cbSuccess, msgError);
        };

        $scope.guardar = function(lanzar) {
            lanzar = !!lanzar;
            var obj = $scope.armoObjetoGuardar(lanzar);
            funciones.guardar(funciones.controllerUrl(obj.id ? 'editar' : 'agregar'), obj, function(){
                $scope.buscar(this.data.id);
            });
        };

        $scope.armoObjetoGuardar = function(lanzar) {
            var ordenes = [];
            for (i in $scope.lote.ordenesDeFabricacion) {
                var orden = $scope.lote.ordenesDeFabricacion[i];
                if (!$scope.ordenConfirmada(orden)) {
                    ordenes.push(orden);
                }
            }
            return {
                id: $scope.lote.id,
                nombre: $scope.nombreLote,
                ordenesDeFabricacion: ordenes,
                lanzar: lanzar
            };
        };

        $scope.importarForecast = function() {
            var idForecast = $('#inputForecast_selectedValue').val();
            if (!idForecast) {
                $.error('Debe elegir un Forecast para continuar');
            }
            funciones.guardar(funciones.controllerUrl('importarForecast'), {idForecast: idForecast}, function(){
                $scope.buscar(this.data.id);
            });
        };

        $scope.lanzarTareas = function() {
            var error = $scope.hayErrorGuardar(true);
            if (!error) {
                $scope.guardar(true);
            } else {
                $.error(error);
            }
        };

        $scope.hayErrorGuardar = function(lanzar) {
            lanzar = !!lanzar;
            var aLanzar = 0;
            for (i in $scope.lote.ordenesDeFabricacion) {
                var orden = $scope.lote.ordenesDeFabricacion[i];
                if (orden.anulado != 'S' && !$scope.ordenConfirmada(orden) && (!lanzar || orden.lanzar)) {
                    if (orden.curvaDeProduccion && orden.curvaDeProduccion.id) {
                        if ($scope.sumarColumna(orden, 0) != orden.cantidadTotal) {
                            return 'Alguna de las órdenes sin confirmar tiene errores en las cantidades (verificar curvas)';
                        }
                        aLanzar++;
                    } else {
                        if (lanzar && orden.lanzar) {
                            return 'Para lanzar una tarea debe seleccionar su curva y verificar las cantidades';
                        }
                    }
                }
            }
            if (lanzar && !aLanzar) {
                return 'No hay ninguna orden seleccionada para ser lanzada';
            }
            return false;
        };

        $scope.borrar = function() {
            var msg = '¿Está seguro que desea borrar el lote Nº ' + $scope.lote.id + '?',
                url = funciones.controllerUrl('borrar');
            funciones.borrar(msg, url, {id: $scope.lote.id}, function() {
                cambiarModo('inicio');
            });
        };

        $scope.puedeBorrar = function() {
            if (!$scope.lote.id) {
                return false;
            }
            var todasSinConfirmar = true;
            angular.forEach($scope.lote.ordenesDeFabricacion, function(orden) {
                if (orden.confirmada == 'S') {
                    todasSinConfirmar = false;
                }
            });
            return todasSinConfirmar;
        };

        $scope.agregarOrden = function() {
            var div = '' +
                '<div class="p10">' +
                '<table><tbody>' +
                '<tr><td class="w120"><label for="inputArticulo">Artículo:</label></td><td class="w240"><input id="inputArticulo" class="textbox autoSuggestBox obligatorio w200" name="Articulo" /></td></tr>' +
                '<tr><td class="w120"><label for="inputColor">Color:</label></td><td><input id="inputColor" class="textbox autoSuggestBox obligatorio w200" name="ColorPorArticulo" linkedTo="inputArticulo,Articulo" /></td></tr>' +
                '<tr><td class="w120"><label for="inputPatron">Patrón:</label></td><td><input id="inputPatron" class="textbox autoSuggestBox obligatorio w200" name="Patron"  linkedTo="inputArticulo,Articulo;inputColor,ColorPorArticulo" /></td></tr>' +
                '</div>';
            var botones = [{value: 'Aceptar', action: $.proxy(function() {
                var obj = {
                    idArticulo: $('#inputArticulo_selectedValue').val(),
                    idColor: $('#inputColor_selectedValue').val(),
                    idPatron: $('#inputPatron_selectedValue').val()
                };
                if (obj.idArticulo && obj.idColor && obj.idPatron) {
                    funciones.get(funciones.controllerUrl('getInfoArticulo'), obj, $.proxy(function(json) {
                        var orden = {
                            patron: json.data,
                            articulo: json.data.articulo,
                            colorPorArticulo: json.data.colorPorArticulo,
                            anulado: 'N',
                            confirmada: 'N',
                            cantidad: {},
                            cantidadTotal: 0,
                            show: 1
                        };
                        this.lote.ordenesDeFabricacion.push(orden);
                        $.jPopUp.close();
                    }, this));
                }
            }, $scope)}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
            $.jPopUp.show(div, botones);
            $('#inputArticulo').focus();
        };

        $scope.borrarOrden = function($index) {
            var orden = $scope.lote.ordenesDeFabricacion[$index];
            if (orden.id) {
                orden.anulado = 'S';
                orden.show = false;
            } else {
                $scope.lote.ordenesDeFabricacion.splice($index, 1);
            }
        };


        /** FUNCIONES VARIAS **/

        $scope.agregarClick = function() {
            cambiarModo('preAgregar');
            $scope.nombreLote = '';
        };

        $scope.ordenConfirmada = function(orden) {
            return orden.confirmada == 'S';
        };

        $scope.toggleOrden = function($index) {
            $scope.lote.ordenesDeFabricacion[$index].show = $scope.lote.ordenesDeFabricacion[$index].show ? false : true;
        };

        $scope.toInt = function(cantidad) {
            return funciones.toInt(cantidad);
        };

        $scope.getDescripcionSituacion = function(situacion) {
            switch (situacion) {
                case 'T':
                    return 'Terminada';
                case 'I':
                    return 'Iniciada';
            };
            return 'Programada';
        };

        $scope.inicializarCurvaLibre = function(orden) {
            orden.maxLibres = 0;
            orden.curvaLibre = {};
            var cantidadDeCurvas = $scope.calcularCantidadDeCurvas(orden, orden.curvaDeProduccion);
            if (orden.curvaDeProduccion && orden.curvaDeProduccion.id/* && cantidadDeCurvas*/) {
                angular.forEach(orden.curvaDeProduccion.cantidad, function(value, key) {
                    orden.curvaLibre[key] = orden.cantidad[key] - cantidadDeCurvas * value;
                });
            }
            $scope.calcularMaxLibres(orden);
        };

        $scope.seleccionarCurva = function(orden, curva) {
            orden.curvaDeProduccion = curva;
            $scope.calcularMaxLibres(orden);
        };

        $scope.calcularCantidadPorCurvas = function(orden) {
            if (orden.curvaDeProduccion && orden.curvaDeProduccion.id) {
                var total = orden.cantidadTotal;
                var totalCurva = orden.curvaDeProduccion.cantidadTotal;
                var resto = (total % totalCurva);
                return total - resto;
            }
            return 0;
        };

        $scope.calcularMaxLibres = function(orden) {
            orden.maxLibres = orden.cantidadTotal - $scope.calcularCantidadPorCurvas(orden);
        };

        $scope.calcularCantidadDeCurvas = function(orden, curva) {
            if (orden.curvaDeProduccion && orden.curvaDeProduccion.id == curva.id) {
                return Math.floor(orden.cantidadTotal / orden.curvaDeProduccion.cantidadTotal);
            }
            return 0;
        };

        $scope.sumarColumna = function(orden, posicion) {
            var cantidadCurvas = $scope.calcularCantidadDeCurvas(orden, orden.curvaDeProduccion);
            if (!orden.curvaDeProduccion || !orden.curvaDeProduccion.id) {
                return 0;
            }
            if (posicion == 0) {
                return orden.curvaDeProduccion.cantidadTotal * cantidadCurvas + funciones.sumaArray(orden.curvaLibre, true);
            }
            return cantidadCurvas * funciones.toInt(orden.curvaDeProduccion.cantidad[posicion]) + Math.max(funciones.toInt(orden.curvaLibre[posicion], 0));
        };

        $scope.sumaArray = function(array) {
            return funciones.sumaArray(array);
        };
    });

    $(document).ready(function(){
        tituloPrograma = 'Lotes de producción';
        cambiarModo('inicio');
    });

    function cambiarModo(modo){
        funciones.cambiarModo(modo);
        $('#agregarOrden').hide();
        $('#lanzarTareas').hide();
        switch (modo){
            case 'inicio':
                funciones.cambiarTitulo(tituloPrograma);
                funciones.scope().getLista();
                break;
            case 'buscar':
                cambiarModo('editar');
                break;
            case 'editar':
                $('#agregarOrden').show();
                $('#lanzarTareas').show();
                if (funciones.scope().puedeBorrar()) {
                    $('#btnBorrar').show();
                }
                break;
            case 'agregar':
                $('#agregarOrden').show();
                funciones.cambiarTitulo(tituloPrograma + ' - Nuevo lote');
                funciones.scope().nombreLote = $('#inputNombre').val();
                break;
            case 'preAgregar':
                $('#btnAgregar').hide();
                $('#btnCancelarBuscar').show();
                break;
        }
        funciones.scope().$apply();
    }
</script>
<div id='programaTitulo'></div>
<div id='programaContenido' ng-controller='AppCtrl'>

    <!-- Lista de LOTES -->
    <div id='divLotes' class='customScroll aCenter' data-ng-if="modo == 'inicio' && lotes.length">
        <div class="inline-block w30p">
            <table id='tablaLotes' class='registrosAlternados'>
                <thead class="tableHeader">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr id='tr_{{lote.id}}' ng-repeat='lot in lotes'>
                        <td class='w10p aCenter'>
                            <label>{{lot.id}}</label>
                        </td>
                        <td class='w40p'>
                            <label>{{lot.nombre}}</label>
                        </td>
                        <td class='w40p aCenter'>
                            <label>{{lot.fechaAlta}}</label>
                        </td>
                        <td class='w10p aCenter p5'>
                            <a href='#' class='boton' title='Ver' data-ng-click='buscar(lot.id);'><img src='/img/botones/25/buscar.gif' /></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pantalla para importar un FORECAST o crear un LOTE DESDE CERO -->
    <div class='aCenter hInherit' data-ng-if="modo == 'preAgregar'">
        <div class="fLeft w45p pTop170">
            <h2>Importar lote desde Forecast</h2>
            <div class="pTop15"><input id="inputForecast" class="textbox autoSuggestBox obligatorio w200" name="Forecast" /></div>
            <div class="mTop10"><a href='#' class='boton' title='Aplicar' data-ng-click="importarForecast();"><img src="/img/botones/40/aceptar.gif" /></a></div>
        </div>
        <div class="vLine1" style="opacity: 0.3;"></div>
        <div class="fRight w45p pTop170">
            <h2>Agregar un lote desde cero</h2>
            <div class="pTop15"><label for='inputNombre'>Nombre: </label><input id="inputNombre" class="textbox obligatorio w140" maxlength="20" data-ng-model="inputNombre" /></div>
            <div class="mTop10"><a href='#' class='boton' title='Agregar desde cero' onclick='funciones.agregarClick()'><img src="/img/botones/40/agregar.gif" /></a></div>
        </div>
    </div>

    <!-- Edición de UN LOTE. Lista de ÓRDENES DE PRODUCCION -->
    <div id='divLote' class='customScroll' data-ng-if="(modo == 'editar' && lote.id) || modo == 'agregar'">
        <table id='tablaOrdenes' class='w100p'>
            <thead class="tableHeader">
                <tr>
                    <th> </th>
                    <th>Ver</th>
                    <th>ID</th>
                    <th>Artículo</th>
                    <th>Color</th>
                    <th>F. inicio</th>
                    <th>F. fin</th>
                    <th>Cantidad</th>
                    <th> </th>
                </tr>
            </thead>
            <tbody data-ng-repeat="orden in lote.ordenesDeFabricacion">
                <tr data-ng-class="ordenConfirmada(orden) ? 'indicador-verde' : 'indicador-gris'" data-ng-hide="orden.anulado == 'S'">
                    <td class='w4p aCenter p3' data-ng-class="orden.show ? 'bTopDarkGray bLeftDarkGray' : 'bTopWhite bBottomWhite bLeftWhite'">
                        <input type='checkbox' data-ng-model="orden.lanzar" data-ng-disabled="ordenConfirmada(orden)" title='Seleccionar orden para lanzar tareas' />
                    </td>
                    <td class='w5p aCenter p3' data-ng-class="orden.show ? 'bTopDarkGray' : 'bTopWhite bBottomWhite'">
                        <a href='#' class='boton' title='Ver' data-ng-click='toggleOrden($index);'>
                            <img src="/img/botones/25/{{ordenConfirmada(orden) ? 'buscar' : 'editar'}}.gif" />
                        </a>
                    </td>
                    <td class='w8p aCenter' data-ng-class="orden.show ? 'bTopDarkGray' : 'bTopWhite bBottomWhite'">
                        <label>{{orden.id}}</label>
                    </td>
                    <td class='w30p p5' data-ng-class="orden.show ? 'bTopDarkGray' : 'bTopWhite bBottomWhite'">
                        <label>[{{orden.articulo.id}}] - {{orden.articulo.nombre}}</label>
                    </td>
                    <td class='w20p p5' data-ng-class="orden.show ? 'bTopDarkGray' : 'bTopWhite bBottomWhite'">
                        <label>[{{orden.colorPorArticulo.id}}] - {{orden.colorPorArticulo.nombre}}</label>
                    </td>
                    <td class='w10p aCenter' data-ng-class="orden.show ? 'bTopDarkGray' : 'bTopWhite bBottomWhite'">
                        <label>{{orden.fechaInicio}}</label>
                    </td>
                    <td class='w10p aCenter' data-ng-class="orden.show ? 'bTopDarkGray' : 'bTopWhite bBottomWhite'">
                        <label>{{orden.fechaFin}}</label>
                    </td>
                    <td class='w8p aCenter' data-ng-class="orden.show ? 'bTopDarkGray' : 'bTopWhite bBottomWhite'">
                        <label>{{orden.cantidadTotal}}</label>
                    </td>
                    <td class='w5p aCenter p3' data-ng-class="orden.show ? 'bTopDarkGray bRightDarkGray' : 'bTopWhite bBottomWhite bRightWhite'">
                        <a href='#' class='boton' title='Borrar orden' data-ng-hide="ordenConfirmada(orden)" data-ng-click='borrarOrden($index);'><img src='/img/botones/25/borrar.gif' /></a>
                    </td>
                </tr>

                <!-- Edición de UNA ORDEN DE PRODUCCIÓN -->
                <tr data-ng-if="!ordenConfirmada(orden) || orden.show" data-ng-show="orden.show">
                    <td colspan="9" class="bAllDarkGray bGradientWhiteLightGray p10" style="border-top: 0;">
                        <div class="fLeft w35p">
                            <table cellspacing="10" border="0">
                                <tbody>
                                    <tr class="tableRow">
                                        <td style="width: 80px;"><label class="bold">Artículo:</label></td>
                                        <td style="width: 250px;"><label>[{{orden.articulo.id}}] - {{orden.articulo.nombre}}</label></td>
                                    </tr>
                                    <tr class="tableRow">
                                        <td><label class="bold">Color:</label></td>
                                        <td><label>[{{orden.colorPorArticulo.id}}] - {{orden.colorPorArticulo.nombre}}</label></td>
                                    </tr>
                                    <tr class="tableRow">
                                        <td><label class="bold">Patrón:</label></td>
                                        <td><label>Versión {{orden.patron.version}}</label></td>
                                    </tr>
                                    <tr class="tableRow">
                                        <td><label class="bold">Cantidad:</label></td>
                                        <td>
                                            <input class="textbox inputForm w90 aCenter" data-ng-model="orden.cantidadTotal" validate="EnteroPositivo" data-ng-hide="ordenConfirmada(orden)" data-ng-change="calcularMaxLibres(orden)" />
                                            <label data-ng-show="ordenConfirmada(orden)">{{orden.cantidadTotal}}</label>
                                        </td>
                                    </tr>
                                    <tr class="tableRow">
                                        <td><label class="bold">F. Inicio:</label></td>
                                        <td>
                                            <span data-ng-hide="ordenConfirmada(orden)"><input class='textbox inputForm w90 aCenter noPicker' data-ng-model="orden.fechaInicio" validate='Fecha' /></span>
                                            <label data-ng-show="ordenConfirmada(orden)">{{orden.fechaInicio}}</label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="fRight w65p">

                            <!-- Tabla de TAREAS -->
                            <table class='registrosAlternados w100p' data-ng-if="ordenConfirmada(orden)">
                                <caption><h2 style="margin-top: 0;">Tareas</h2></caption>
                                <thead class="tableHeader">
                                    <tr>
                                        <th>Nº</th>
                                        <th data-ng-repeat="talle in orden.articulo.rangoTalle.posicion" data-ng-if="talle">{{talle}}</th>
                                        <th>Total</th>
                                        <th>Situación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-ng-repeat="tarea in orden.tareas">
                                        <td class='aCenter'>
                                            <label>{{tarea.idOrdenDeFabricacion | fixed:4}}-{{tarea.numero | fixed:2}}</label>
                                        </td>
                                        <td class='aCenter' data-ng-repeat="(posicion, talle) in orden.articulo.rangoTalle.posicion" data-ng-if="talle">
                                            <label>{{toInt(tarea.cantidad[posicion])}}</label>
                                        </td>
                                        <td class='aCenter'>
                                            <label>{{tarea.cantidadTotal}}</label>
                                        </td>
                                        <td class='aCenter'>
                                            <label>{{getDescripcionSituacion(tarea.situacion)}}</label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Tabla de CURVAS -->
                            <table class='registrosAlternados w100p' data-ng-if="!ordenConfirmada(orden)" data-ng-init="inicializarCurvaLibre(orden)">
                                <caption><h2 style="margin-top: 0;">Curvas</h2></caption>
                                <thead class="tableHeader">
                                    <tr>
                                        <th>Total</th>
                                        <th data-ng-repeat="talle in orden.articulo.rangoTalle.posicion" data-ng-if="talle">{{talle}}</th>
                                        <th> </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-ng-repeat="curva in orden.articulo.curvasDeProduccion">
                                        <td class='aCenter' data-ng-class="{'bTopDarkGray bBottomDarkGray bLeftDarkGray': orden.curvaDeProduccion && orden.curvaDeProduccion.id == curva.id}">
                                            <label><span>{{calcularCantidadDeCurvas(orden, curva)}}</span> curvas de {{curva.cantidadTotal}} pares</label>
                                        </td>
                                        <td class='aCenter' data-ng-repeat="(posicion, talle) in orden.articulo.rangoTalle.posicion" data-ng-if="talle" data-ng-class="{'bTopDarkGray bBottomDarkGray': orden.curvaDeProduccion && orden.curvaDeProduccion.id == curva.id}">
                                            <label>{{toInt(curva.cantidad[posicion])}}</label>
                                        </td>
                                        <td class='aCenter' data-ng-class="{'bTopDarkGray bBottomDarkGray bRightDarkGray': orden.curvaDeProduccion && orden.curvaDeProduccion.id == curva.id}">
                                            <a href='#' class='boton' title='Seleccionar curva' data-ng-click='seleccionarCurva(orden, curva);'>
                                                <img src="/img/botones/25/aceptar.gif" />
                                            </a>
                                        </td>
                                    </tr>
                                    <tr data-ng-class="sumaArray(orden.curvaLibre) < orden.maxLibres ? 'indicador-amarillo' : (sumaArray(orden.curvaLibre) > orden.maxLibres ? 'indicador-rojo' : 'indicador-verde')">
                                        <td class='aCenter'>
                                            <label>Libres: {{sumaArray(orden.curvaLibre)}} / {{orden.maxLibres}}</label>
                                        </td>
                                        <td class='aCenter' data-ng-repeat="(posicion, talle) in orden.articulo.rangoTalle.posicion" data-ng-if="talle">
                                            <input class="textbox aCenter w25" type="text" validate="EnteroPositivo" data-ng-model="orden.curvaLibre[posicion]" data-ng-disabled="!orden.curvaDeProduccion.id">
                                        </td>
                                        <td class='aCenter'>
                                        </td>
                                    </tr>
                                    <tr class="bold">
                                        <td class='aCenter' data-ng-class="sumaArray(orden.curvaLibre) < orden.maxLibres ? 'indicador-amarillo' : (sumaArray(orden.curvaLibre) > orden.maxLibres ? 'indicador-rojo' : 'indicador-verde')">
                                            {{sumarColumna(orden, 0)}}
                                        </td>
                                        <td class='aCenter' data-ng-repeat="(posicion, talle) in orden.articulo.rangoTalle.posicion" data-ng-if="talle">
                                            <label>{{sumarColumna(orden, posicion)}}</label>
                                        </td>
                                        <td class='aCenter'> </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div id='programaPie'>
    <div class='botonera'>
        <?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.scope().agregarClick();', 'permiso' => 'produccion/gestion_produccion/lotes_produccion/agregar/')); ?>
        <?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.scope().agregarOrden();', 'id' => 'agregarOrden', 'title' => 'Agregar orden')); ?>
        <?php Html::echoBotonera(array('boton' => 'download', 'accion' => 'funciones.scope().lanzarTareas();', 'id' => 'lanzarTareas', 'title' => 'Guardar cambios y lanzar tareas seleccionadas')); ?>
        <?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
        <?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'produccion/gestion_produccion/lotes_produccion/borrar/')); ?>
        <?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
        <?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarEditar')); ?>
    </div>
</div>