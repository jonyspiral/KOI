<?php

/**
 * @var PedidoCliente $pedido
 */
$pedido = $_POST['form_pedido'];
$cliente = $pedido->cliente;
$fecha = explode('/', Funciones::formatearFecha($pedido->fechaAlta, 'd/m/Y'));
$importeLetras = NumeroALetras::numero2Letras();

$subtotal = $pedido->importeTotal;
$descuentos = $pedido->importeTotal * (Funciones::toFloat($cliente->creditoDescuentoEspecial) / 100);
$subtotal2 = $pedido->importeTotal - $descuentos;
$importeTotal = $subtotal2;
$observaciones = $pedido->observaciones;



?>
<head>
    <link rel="stylesheet" type="text/css" href="../../../../css/styles.css" media="screen"/>
    <style>
        .centrar{
            margin:0 auto 0 auto;
        }

        .textoSize1{
            font-size:1em;
        }

        .textoSize12{
            font-size:1.2em;
        }

        .textoSize15{
            font-size:1.5em;
        }

        .textoSize08{
            font-size:0.8em;
        }

        .fuenteArial{
            font-family: Arial,serif;
        }

        #spiralSa{
            top: 40px;
            left: 220px;
        }

        .textoSpiral{
            font-size: 1.5em;
            font-weight: bold;
        }

        #logo{
            top: 30px;
            left: 20px;
        }

        #infoEmpresa{
            font-size: 0.85em;
            top: 110px;
            left: 250px;
            line-height:20px;
        }
        #lineaVertical1{
            left: 530px;
            width: 2px;
            height: 50px;
            background-color: black;
        }
        #lineaVertical2{
            top: 173px;
            left: 530px;
            width: 2px;
            height: 57px;
            background-color: black;
        }

        #letra{
            width: 120px;
            height: 120px;
            top: 50px;
            left: 470px;
            border: 2px solid black;
        }

        .tamanoLetra{
            font-size: 6em;
        }
        #pedido {
            font-weight: bold;
            font-size: 1.5em;
            top: 75px;
            right: 245px;
        }

        #nroPedido{
            font-size: 1.5em;
            top: 105px;
            right: 220px;
        }
        #fecha{
            font-weight: bold;
            font-size: 1.5em;
            top: 75px;
            right: 42px;
        }

        #dia{
            font-size: 1.5em;
            top: 105px;
            right: 120px;
        }

        #mes{
            font-size: 1.5em;
            top: 105px;
            right: 85px;
        }

        #anio{
            font-size: 1.5em;
            top: 105px;
            right: 20px;
        }

        #division1{
            top: 228px;
            width: 100%;
            height: 2px;
            background-color: black;
        }
        #nombre{
            top: 250px;
            left: 50px;
        }

        #nombreVal{
            top: 250px;
            left: 100px;
        }

        #direccion{
            top: 280px;
            left: 20px;
        }
        #direccionVal{
            top: 280px;
            left: 100px;
        }

        #cuit{
            top: 250px;
            right: 300px;
        }

        #cuitVal{
            top: 250px;
            right: 180px;
        }

        #condicionIVA{
            top: 280px;
            right: 300px;
        }
        #condicionIVAval{
            top: 280px;
            left: 745px;
        }

        #fondoCabeceraDetalle{
            top: 350px;
            width: 100%;
            height: 30px;
            background-color: black;
        }

        #cabeceraCodigo{
            top: 355px;
            left: 50px;
        }

        #cabeceraDescripcion{
            top: 355px;
            left: 350px;
        }

        #cabeceraCantidad{
            top: 355px;
            right: 280px;
        }

        #cabeceraUnitario{
            top: 355px;
            right: 140px;
        }

        #cabeceraTotal{
            top: 355px;
            right: 50px;
        }

        #detalle{
            top: 390px;
            left: 35px;
        }
        .detalleCodArt{
            width:160px;

        }
        .detalleNombreArt{
            width:495px;

        }
        .detalleCantidad{
            width:105px;

        }
        .detallePrecioUnitario{
            width:110px;

        }
        .detallePrecioTotal{
            width:110px;
        }

        #division2{
            top: 1230px;
            width: 100%;
            height: 3px;
            background-color: black;
        }

        #subtotal1{
            top: 1245px;
            left: 50px;
        }

        #descuento{
            top: 1245px;
            left: 200px;
        }

        #subtotal2{
            top: 1245px;
            left: 380px;
        }
        #total{
            top: 1245px;
            left: 930px;
        }
        #numSubtotal1{
            top: 1265px;
            left: 35px;
        }

        #numDescuento{
            top: 1265px;
            left: 210px;
        }

        #numSubtotal2{
            top: 1265px;
            left: 365px;
        }
        #numTotal{
            top: 1265px;
            left: 895px;
        }
        #division3{
            top: 1300px;
            width: 100%;
            height: 15px;
            background-color: black;
        }
        #observaciones {
            top: 1325px;
            left: 20px;
            width: 360px;
            height: 110px;
            border-right: 1px solid black;
        }
        #importeLetras {
            top: 1325px;
            right: 20px;
            width: 622px;
            height: 46px;
            border-bottom: 1px solid black;
        }
    </style>
</head>
<body>

<div id="spiralSa" class="absolute textoSpiral">
    <?php echo Config::RAZON_RTG; ?>
</div>

<div id="logo" class="absolute">
    <img src="../../img/logos/logo.png" style="width: 253px; height: 190px">
</div>

<div id="infoEmpresa" class="absolute aRight">
    Herrera 1761<br/>
    Ciudad Autónoma de Buenos Aires<br/>
    Tel./Fax: 0810-362-7747
</div>

<div id="letra" class="absolute tamanoLetra">
    X
</div>
<div id="lineaVertical1" class="absolute"></div>
<div id="lineaVertical2" class="absolute"></div>

<div id="pedido" class="absolute">
    PEDIDO
</div>

<div id="nroPedido" class="absolute">
    <?php echo Funciones::padLeft($pedido->id, 8, '0'); ?>
</div>

<div id="fecha" class="absolute">
    FECHA
</div>

<div  class="fuenteArial">
    <div id='dia' class='absolute'><?php echo $fecha[0]; ?></div>
    <div id='mes' class='absolute'><?php echo $fecha[1]; ?></div>
    <div id='anio' class='absolute'><?php echo $fecha[2]; ?></div>
</div>

<div id="division1" class="absolute">
</div>

<div id="nombre" class="absolute">
    Sr/es:
</div>
<div id="nombreVal" class="textoSize1 textoSize1 absolute">
    <?php echo $cliente->razonSocial; ?>
</div>

<div id="direccion" class="absolute">
    Domicilio:
</div>
<div id="direccionVal" class="textoSize1 absolute aLeft">
    <?php echo $cliente->direccionFacturacion(); ?>
</div>

<div id="condicionIVA" class="absolute">
    I.V.A.:
</div>
<div id="condicionIVAval" class="absolute">
    <?php echo $cliente->condicionIva->nombre; ?>
</div>

<div id="cuit" class="absolute">
    C.U.I.T.:
</div>
<div id="cuitVal" class="textoSize1 absolute">
    <?php echo $cliente->cuit; ?>
</div>

<div id="fondoCabeceraDetalle" class="absolute"></div>


<div id="cabeceraCodigo" class="absolute white">
    CODIGO
</div>

<div id="cabeceraDescripcion" class="absolute white">
    DESCRIPCION
</div>

<div id="cabeceraCantidad" class="absolute white">
    CANTIDAD
</div>

<div id="cabeceraUnitario" class="absolute white">
    P.UNITARIO
</div>

<div id="cabeceraTotal" class="absolute white">
    TOTAL
</div>
<div id="detalle" class="absolute">
    <?php
    foreach ($pedido->detalle as $item) {
        echo '<div class="textoSize12 detalleCodArt fLeft aLeft"> ' . $item->idArticulo . '-' . $item->idColorPorArticulo . '</div>';
        echo '<div class="textoSize12 detalleNombreArt fLeft aLeft"> '. $item->articulo->nombre . '</div>';
        echo '<div class="textoSize12 detalleCantidad fLeft aLeft"> ' . $item->calcularTotalPares() . '</div>';
        echo '<div class="textoSize12 detallePrecioUnitario fLeft aLeft"> ' . Funciones::formatearMoneda($item->precioUnitario) . '</div>';
        echo '<div class="textoSize12 detallePrecioTotal fLeft aLeft"> ' . Funciones::formatearMoneda($item->calcularImporteTotal()) . '</div>';
    }
    ?>
</div>

<div id="division2" class="absolute"></div>

<div id="subtotal1" class="absolute">
    Subtotal
</div>

<div id="numSubtotal1" class="textoSize12 absolute">
    <?php echo Funciones::formatearMoneda($subtotal); ?>
</div>

<div id="descuento" class="absolute">
    Descuento
</div>

<div id="numDescuento" class="textoSize12 absolute">
    <?php echo Funciones::formatearMoneda($descuentos); ?>
</div>

<div id="subtotal2" class="absolute">
    Subtotal
</div>

<div id="numSubtotal2" class="textoSize12 absolute">
    <?php echo Funciones::formatearMoneda($subtotal2); ?>
</div>

<div id="total" class="absolute">
    Total
</div>
<div id="numTotal" class="textoSize12 absolute">
    <?php echo Funciones::formatearMoneda($importeTotal); ?>
</div>

<div id="division3" class="absolute"></div>

<div id="observaciones" class="absolute aLeft">
    <span class='underline'>OBSERVACIONES</span>: <?php echo $observaciones;?>
</div>
<div id="importeLetras" class="absolute aLeft">
    <div class='pLeft5'><?php echo $importeLetras;?></div>
</div>
</body>
</html>
