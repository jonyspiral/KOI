<?php

$id = $_POST['form_id'];
$cajaOrigenId = $_POST['form_cajaOrigenId'];
$cajaOrigenNombre = $_POST['form_cajaOrigenNombre'];
$responsable = $_POST['form_responsable'];
$fecha = explode('/', $_POST['form_fecha']);
$total = $_POST['form_total'];
$cheques = $_POST['form_cheques'];
$esTemporal = $_POST['form_esTemporal'];

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../../css/styles.css" media="screen"/>
<style>
#spiralSa{
	top: 60px;
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

#infoCobro{
	font-size: 0.85em;
	top: 240px;
	left: 20px;
	line-height:20px;
}

#boleta{
	font-weight: bold;
	font-size: 1.5em;
	top: 95px;
	left: 220px;
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

#fondoCabeceraDetalle{
	top: 290px;
	width: 100%;
	height: 30px;
	background-color: black;
}

#cabeceraNumeroCheque{
	top: 295px;
	left: 50px;
}

#cabeceraBanco{
	top: 295px;
	left: 280px;
}

#cabeceraFechaVto{
	top: 295px;
	right: 445px;
}

#cabeceraLibrador{
	top: 295px;
	right: 230px;
}

#cabeceraImporte{
	top: 295px;
	right: 50px;
}

#detalle{
	top: 330px;
	left: 35px;
}

#total{
	top: 1050px;
	right: 40px;
}

.detalleNumeroCheque{
	width: 220px;

}
.detalleBanco{
	width: 225px;

}
.detalleFechaVto{
	width: 210px;

}
.detalleLibrador{
	width: 195px;

}
.detalleImporte{
	width: 100px;
}
#fecha{
	font-weight: bold;
	font-size: 1.5em;
	top: 75px;
	right: 78px;
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
</style>
</head>
<body>
	<div id="spiralSa" class="absolute textoSpiral">
		<?php echo Config::RAZON_NCNTS; ?>.
	</div>

	<div id="logo" class="absolute">
		<img src="../../img/logos/logo_ncnts.png" style="width: 190px; height: 190px">
	</div>

	<div id="infoCobro" class="absolute aLeft">
		Caja origen: <?php echo $cajaOrigenNombre . ' (' . $cajaOrigenId . ')' ?><br/>
		Responsable: <?php echo $responsable ?><br/>
	</div>

	<div id="boleta" class="absolute">
		<?php echo 'COBRO DE CHEQUES POR VENTANILLA' . ($esTemporal ? ' TEMPORAL' : '') . ' - ' . Funciones::padLeft($id, 8, '0'); ?>
	</div>

	<div id="fecha" class="absolute">
		FECHA
	</div>

	<div class="fuenteArial">
		<div id='dia' class='absolute'><?php echo $fecha[0]; ?></div>
		<div id='mes' class='absolute'><?php echo $fecha[1]; ?></div>
		<div id='anio' class='absolute'><?php echo $fecha[2]; ?></div>
	</div>

	<div id="division1" class="absolute">
	</div>
	
<div id="fondoCabeceraDetalle" class="absolute"></div>

	<div id="cabeceraNumeroCheque" class="absolute white">
		Nº CHEQUE
	</div>
	
	<div id="cabeceraBanco" class="absolute white">
		BANCO
	</div>
	
	<div id="cabeceraFechaVto" class="absolute white">
		FECHA VTO.
	</div>
	
	<div id="cabeceraLibrador" class="absolute white">
		LIBRADOR
	</div>
	
	<div id="cabeceraImporte" class="absolute white">
		IMPORTE
	</div>
<div id="detalle" class="absolute">
	<?php
		foreach ($cheques as $cheque) {
			/** @var Cheque $cheque */
			echo '<div class="textoSize12 detalleNumeroCheque fLeft aLeft"> ' . $cheque->numero . '</div>';
			echo '<div class="textoSize12 detalleBanco fLeft aLeft"> '. $cheque->banco->nombre . '</div>';
			echo '<div class="textoSize12 detalleFechaVto fLeft aLeft"> ' . $cheque->fechaVencimiento . '</div>';
			echo '<div class="textoSize12 detalleLibrador fLeft aLeft"> ' . $cheque->libradorNombre . '</div>';
			echo '<div class="textoSize12 detalleImporte fLeft aLeft"> ' . Funciones::formatearMoneda($cheque->importe) . '</div><br/>';
		}
	?>
	<div id="total" class="absolute s22"><span class="bold">Total: </span><?php echo Funciones::formatearMoneda($total) ?></div>
</div>
</body>
</html>
