<?php

$letra = $_POST['form_letra'];
$numeroGuia = $_POST['form_numeroGuia'];
$fecha = $_POST['form_fecha'];
$senores = $_POST['form_senores'];
$clienteNro = $_POST['form_clienteNro'];
$direccionCalle = $_POST['form_direccionCalle'];
$direccionNumero = $_POST['form_direccionNumero'];
$direccionPiso = $_POST['form_direccionPiso'];
$direccionDpto = $_POST['form_direccionDpto'];
$direccionLocalidad = $_POST['form_direccionLocalidad'];
$direccionCP = $_POST['form_direccionCP'];
$cuit = $_POST['form_cuit'];
$condicionIva = $_POST['form_nombreCondicionIVA'];
$transportistaSenor = $_POST['form_transportistaSenor'];
$transportistaDomicilio = $_POST['form_transportistaDomicilio'];
$transportistaCUIT = $_POST['form_transportistaCUIT'];
$transportistaDNI= $_POST['form_transportistaDNI'];
$detalle = $_POST['form_detalle'];

?>
<head>
<link rel="stylesheet" type="text/css" href="../../../../css/styles.css" media="screen"/>
<style>

#c1{
	left: 50px;
}
#c2{
	top: 20px;
	left: 350px;
}
#letra{
	top: 55px;
	left: 675px;
}
#guiaDePorte{
	top: 63px;
	left: 800px;
}

#nroGuia{
	top: 80px;
	left: 805px;
}

#fecha{
	top: 100px;
	left: 780px;
}

#numerosCabecera{
	top: 165px;
	left: 700px;
}

#sr{
	top: 250px;
	left: 80px;
}

#domicilio{
	top: 310px;
	left: 50px;
}
#clienteNro{
	top: 250px;
	left: 650px;
}

#cuit{
	top: 280px;
	left: 650px;
}
#iva{
	top: 310px;
	left: 650px;
}

#fondoDetalle{
	top: 395px;
	width: 93.5%;
	height: 30px;
	background-color: black;
	margin-left: 40px;
}

#cantidad{
	top: 400px;
	left: 120px;
}
#descripcion{
	top: 400px;
	left: 550px;
}
#detalleItems{
	top: 430px;
}
#labelDatosTransportista{
	top: 1240px;
	left: 40px;
}
#f2{
	top: 1240px;
	left: 550px;
}
#aclaracion{
	top: 1260px;
	left: 550px;
}
#transportista{
	top: 1280px;
	left: 40px;
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
.textoSize13{
	font-size:1.3em;
}
.textoSize14{
	font-size:1.4em;
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
.fondoSombra {
	background: url('../../img/varias/sombra_underline.gif') no-repeat;
	background-size: 140px;
	background-position-y: 16px;
	background-position-x: 83px;
}

.fondoSombraDescripcion {
	background: url('../../img/varias/sombra_underline.gif') no-repeat;
	background-size: 140px;
	background-position-y: 16px;
	background-position-x: 235px;
}

.detalleCodArt{
	width:70px;
}
.detalleDescripcion{
	width:550px;
}
.detalleCantidad{
	width:200px;
	padding-left:150px;
}
#originalDuplicado{
	position: absolute;
	top: 100px;
	right: 10px;
}
#lineHead{
	position: absolute;
	top:215px;
	left:40px;
}

#lineFoot{
	position: absolute;
	top:1200px;
	left:40px;
}
</style>
</head>
<body>

<div id="cabecera">
	<div id="c1" class="absolute">
		<p class="textoSpiral"><b>SPIRAL SHOES S.A</b></p>
					
		 		<p class="textoSize08 absolute">
				Chaco 2317 (1822)
				<br/>
				Valentín Alsina
				<br/>
				Provincia de Buenos Aires
				<br/>
				Tel./Fax: 0810-362-SPIR (7747)
				<br/>
				<br/>
				
				IVA RESPONSABLE INSCRIPTO
				</p>
	</div>
	<div id="c2" class="absolute">
		<img src="../../img/logos/logo.png" width="300px" height="231px" alt="" />
	</div>
	<div id="c3">
		<div id="letra" class="textoA absolute"><?php echo $letra; ?></div>
		<div id="guiaDePorte" class="absolute"><b>GUIA DE PORTE</b></div>
		<div id="nroGuia" class="absolute"><?php echo $numeroGuia ?></div>
		<div id="fecha" class="absolute textoSize1 fuenteArial"><b>FECHA: <?php echo $fecha[0].' / '. $fecha[1]. ' / '. $fecha[2]; ?></b></div>
		<span id="numerosCabecera" class="textoSize07 absolute">C.U.I.T Nº 33-71005145-9<BR/>ING. BRUTOS Nº 33-71005145-9<BR/>INICIO DE ACTIVIDADES: 04/2007</span>		
	</div>
</div>
<div id="lineHead">__________________________________________________________________________________________________________</div>
<div id="datos">
	<div id="d1">
		<div id="sr" class="absolute">Sr/es: <?php echo $senores; ?></div>
		<div id="domicilio" class="absolute">Domicilio: <?php echo $direccionCalle . '-' . $direccionNumero . '-'. $direccionPiso . '-'. $direccionDpto . '-' . $direccionLocalidad;  ?></div>
	</div>
	<div id="d2">
		<div id="clienteNro" class="absolute">Cliente Nro: <?php echo $clienteNro ?></div>
		<div id="cuit" class="absolute">CUIT: <?php echo $cuit; ?></div>
		<div id="iva" class="absolute">IVA: <?php echo $condicionIva; ?></div>
	</div>
</div>
<div id="fondoDetalle" class="absolute"></div>
<div id="detalle">
	<div id="cantidad" class="absolute white">CANTIDAD</div>
	<div id="descripcion" class="absolute white">DESCRIPCION</div>
	<div id="detalleItems" class="absolute">
		<?php
			/*
			 * {codArt: 350, nombreArt: 'Avril Woman', codColor: 'V', 
			 * 'nombreColor': 'verde', cantidad: 2, precioUnitario: 310.50, precioTotal: 721.00}
			 */
			foreach ($detalle as $item) {
				echo '<div class="textoSize13 detalleCantidad fLeft aLeft"> <br/>' . $item['cantidad'] . '</div>';
				echo '<div class="textoSize13 detalleDescripcion fLeft aLeft"><br/>'. $item['detalle'] . '</div>';
			}
		?>
	</div>
</div>
<div id="lineFoot">__________________________________________________________________________________________________________</div>
<div id="footer">
	<div id="f1">
		<div id="labelDatosTransportista" class="textoSize12 absolute">Datos del transportista</div>
		<div id="transportista" class="absolute aLeft">
			<span class="textoSize12">Sr: <?php echo $transportistaSenor ?><br/></span>
			<span class="textoSize12">Domicilio: <?php echo $transportistaDomicilio ?><br/></span>
			<span class="textoSize12">CUIT: <?php echo $transportistaCUIT ?><br/></span>
			<span class="textoSize12">DNI: <?php echo $transportistaDNI ?></span>
		</div>
	</div>
	<div id="f2" class="absolute aRight">
		<div id="firma" class="textosize12 ">Firma: ________________________</div><br/>	
		<div id="aclaracion" class="textosize12">Aclaracion: ________________________</div><br/>
		<div id="originalDuplicado" class="textosize1">original / duplicado</div>	
	</div>	
</div>
</body>