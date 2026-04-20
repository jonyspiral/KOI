<?php

$letra = $_POST['form_letra'];
$numero = $_POST['form_numero'];
$fecha = $_POST['form_fecha'];
$nombreCliente = $_POST['form_nombreCliente'];
$direccion = $_POST['form_direccion'];
$nombreCondicionIva = $_POST['form_nombreCondicionIva'];
$cuit = $_POST['form_cuit'];
$condicionDeVenta = $_POST['form_condicionDeVenta'];
$descuentos = $_POST['form_descuentos'];
$subtotal = $_POST['form_subtotal'];
$subtotal2 = $_POST['form_subtotal2'];
$ivaPorc1 = $_POST['form_ivaPorc1'];
$ivaImporte1 = $_POST['form_ivaImporte1'];
$ivaPorc2 = $_POST['form_ivaPorc2'];
$ivaImporte2 = $_POST['form_ivaImporte2'];
$ivaPorc3 = $_POST['form_ivaPorc3'];
$ivaImporte3 = $_POST['form_ivaImporte3'];
$importeTotal = $_POST['form_importeTotal'];
$cantidadPares = $_POST['form_cantidadPares'];
$observaciones = $_POST['form_observaciones'];
$remitosIncluidos = $_POST['form_remitosIncluidos'];
$detalle = $_POST['form_detalle'];

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
	font-family:Arial;
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

#nroFactura{
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

#documento{
	font-size: 1.1em;
	top:200px;
	right: 60px;
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
#observaciones{
	top: 1325px;
	left: 20px;
	width: 360px;
	height: 110px;
	border-right: 1px solid black;
}
</style>
</head>
<body>
	
	<div id="letra" class="absolute tamanoLetra">
		X
	</div>
	<div id="lineaVertical1" class="absolute"></div>
	<div id="lineaVertical2" class="absolute"></div>
	
	<div id="nroFactura" class="absolute">
		<?php echo 'D' . Funciones::padLeft($numero, 8, '0'); ?>
	</div>
	
	<div id="fecha" class="absolute">
		FECHA
	</div>
	
	<div  class="fuenteArial">
		<div id='dia' class='absolute'><?php echo $fecha[0]; ?></div>
		<div id='mes' class='absolute'><?php echo $fecha[1]; ?></div>
		<div id='anio' class='absolute'><?php echo $fecha[2]; ?></div>
	</div>

	<div id="documento" class="absolute">Documento no válido como factura</div>

	<div id="division1" class="absolute">
	</div>
	
	<div id="nombre" class="absolute">
		Sr/es: 
	</div>
	<div id="nombreVal" class="textoSize1 textoSize1 absolute">
	<?php echo $nombreCliente; ?>
	</div>
	
	<div id="direccion" class="absolute">
		Domicilio: 
	</div>
	<div id="direccionVal" class="textoSize1 absolute">
		<?php echo $direccion; ?>
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
		/*
		 * {codArt: 350, nombreArt: 'Avril Woman', codColor: 'V', 
		 * 'nombreColor': 'verde', cantidad: 2, precioUnitario: 310.50, precioTotal: 721.00}
		 */
		foreach ($detalle as $articulo) {
			foreach ($articulo as $colores) {
				foreach ($colores as $precios) {
					foreach ($precios as $item) {
						$codigo = $item['codAlm'] . ($item['codArt'] ? '-' . $item['codArt'] . '-' . $item['codColor'] : '');
						echo '<div class="textoSize12 detalleCodArt fLeft aLeft"> ' . $codigo . '</div>';
						echo '<div class="textoSize12 detalleNombreArt fLeft aLeft"> '. $item['nombreArt'] . '</div>';
						echo '<div class="textoSize12 detalleCantidad fLeft aLeft"> ' . $item['cantidad'] . '</div>';	
						echo '<div class="textoSize12 detallePrecioUnitario fLeft aLeft"> ' . Funciones::formatearMoneda($item['precioUnitario']) . '</div>';		
						echo '<div class="textoSize12 detallePrecioTotal fLeft aLeft"> ' . Funciones::formatearMoneda($item['precioTotal']) . '</div>';
					}
				}
			}
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
</body>