<?php

$letra = $_POST['form_letra'];
$numero = $_POST['form_numero'];
$fecha = $_POST['form_fecha'];
$nombreCliente = $_POST['form_nombreCliente'];
$direccion = $_POST['form_direccion'];
$nombreCondicionIva = $_POST['form_nombreCondicionIva'];
$cuit = $_POST['form_cuit'];
$condicionDeVenta = $_POST['form_condicionDeVenta'];
$subtotal = $_POST['form_subtotal'];
$subtotal2 = $_POST['form_subtotal2'];
$descuentos = $_POST['form_descuentos'];
$ivaPorc1 = $_POST['form_ivaPorc1'];
$ivaImporte1 = $_POST['form_ivaImporte1'];
$ivaPorc2 = $_POST['form_ivaPorc2'];
$ivaImporte2 = $_POST['form_ivaImporte2'];
$importeTotal = $_POST['form_importeTotal'];
$cantidadPares = $_POST['form_cantidadPares'];
$cae = $_POST['form_cae'];
$caeVencimiento = $_POST['form_caeVencimiento'];
$remitosIncluidos = $_POST['form_remitosIncluidos'];
$detalle = $_POST['form_detalle'];

?>
<head>
<link rel="stylesheet" type="text/css" href="../../../../css/styles.css" media="screen"/>
<style>

.centrar{
	margin:0 auto 0 auto;
}
.textoA{
	
	font-size:4em;
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

.textoSize07{
	font-size:0.8em;
}

.textoSpiral{
	font-size:2em;
}

.fuenteArial{
	font-family:Arial;
}

#dia{
	position: absolute;
	top:160px;
	right:99px;
	font-size:1.9em;
}
#mes{
	position: absolute;
	top:160px;
	right:50px;
	font-size:1.9em;
}
#anio{
	position: absolute;
	top:160px;
	right:2px;
	font-size:1.9em;
}
#nombre{
	top: 300px;
	left: 160px;
}
#direccion{
	top: 345px;
	left: 160px;
}
#condicionIVA{
	top: 400px;
	left: 160px;
}

#cuit{
	top: 400px;
	right: 190px;
}

#detalle{
	top: 570px;
	left: 70px;
}

#subtotal{
	top: 1143px;
	left: 350px;	
}

#descuento{
	top: 1143px;
	right: 330px;
}

#subtotal2{
	top: 1124px;
	right: 35px;
}

#impuesto{
	top: 1171px;
	right: 35px;
}

#subtotal3{
	top: 1214px;
	right: 35px;
}

#total{
	top: 1261px;
	right: 30px;
}

#IVAinscripto{
	top: 1263px;
	left: 250px;
}
.detalleCodArt{
	width:110px;
	
}
.detalleNombreArt{
	width:495px;
	
}
.detalleCantidad{
	width:110px;
	
}
.detallePrecioUnitario{
	width:110px;
	
}
.detallePrecioTotal{
	width:110px;
	
}


</style>
</head>
<body>

<div  class="fuenteArial">
	<?php echo '<div id="dia">' . $fecha[0]. " </div> "; 
		  echo '<div id="mes">' . $fecha[1] . " </div> ";
		  echo '<div id="anio">' . Funciones::padLeft($fecha[2]-2000, 2, "0") . '</div>';
	?>
</div>

<div id="nombre" class="textoSize1 textoSize1 absolute">
<?php echo $nombreCliente; ?>
</div>

<div id="direccion" class="textoSize1 absolute">
	<?php echo $direccion; ?>
</div>

<div id="condicionIVA" class="absolute">
	<?php echo $nombreCondicionIva; ?>
</div>

<div id="cuit" class="textoSize1 absolute">
	<?php echo $cuit; ?>
</div>

<div id="detalle" class="container_16 absolute">
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

<div id="subtotal" class="textoSize12 absolute">
	<?php echo Funciones::formatearMoneda($subtotal); ?>
</div>

<div id="descuento" class="textoSize12 absolute">
	<?php echo Funciones::formatearMoneda($descuentos); ?>
</div>

<div id="subtotal2" class="textoSize12 absolute">
	<?php echo Funciones::formatearMoneda($subtotal - $descuentos); ?>
</div>

<div id="impuesto" class="textoSize12 absolute">

</div>

<div id="subtotal3" class="textoSize12 absolute">
	<?php echo Funciones::formatearMoneda($subtotal - $descuentos + $ivaImporte1); ?>
</div>

<div id="total" class="textoSize12 absolute">
	<?php echo Funciones::formatearMoneda($importeTotal); ?>
</div>

<div id="IVAinscripto" class="textoSize12 absolute">
	<?php echo Funciones::formatearMoneda($ivaImporte1); ?>
</div>

</body>
</html>