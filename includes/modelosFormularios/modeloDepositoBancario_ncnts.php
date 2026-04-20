<?php

$id = $_POST['form_id'];
$cajaOrigenId = $_POST['form_cajaOrigenId'];
$cajaOrigenNombre = $_POST['form_cajaOrigenNombre'];
$cuentaBancariaNombre = $_POST['form_cuentaBancariaNombre'];
$fecha = $_POST['form_fecha'];
$ventaDeCheque = $_POST['form_ventaDeCheque'];
$numeroBoleta = $_POST['form_numeroBoleta'];
$efectivo = $_POST['form_efectivo'];
$cheques = $_POST['form_cheques'];
$esDepositoTemporal = $_POST['es_deposito_temporal'];

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

#infoDeposito{
	font-size: 0.85em;
	top: 130px;
	left: 220px;
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
</style>
</head>
<body>
	<div id="spiralSa" class="absolute textoSpiral">
		<?php echo Config::RAZON_NCNTS; ?>.
	</div>

	<div id="logo" class="absolute">
		<img src="../../img/logos/logo_ncnts.png" style="width: 190px; height: 190px">
	</div>

	<div id="infoDeposito" class="absolute aLeft">
		Caja origen: <?php echo $cajaOrigenNombre . ' (' . $cajaOrigenId . ')' ?><br/>
		Cuenta bancaria: <?php echo $cuentaBancariaNombre ?><br/>
		<?php echo ($ventaDeCheque ? '' : 'Número boleta: ' . (is_null($numeroBoleta) ? '-' : $numeroBoleta)) ?>
	</div>

	<div id="boleta" class="absolute">
		<?php echo ($ventaDeCheque ? 'VENTA DE CHEQUES' : ($esDepositoTemporal ? 'BOLETA DE DEPÓSITO' : 'DEPÓSITO BANCARIO')) . ' - ' . Funciones::padLeft($id, 8, '0'); ?>
	</div>

	<div id="division1" class="absolute">
	</div>
	
	<div id="nombre" class="absolute s18">
		<span class="bold">Monto en efectivo: </span><?php echo Funciones::formatearMoneda($efectivo); ?>
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
		$total = 0;
		foreach ($cheques as $cheque) {
			/** @var Cheque $cheque */
			echo '<div class="textoSize12 detalleNumeroCheque fLeft aLeft"> ' . $cheque->numero . '</div>';
			echo '<div class="textoSize12 detalleBanco fLeft aLeft"> '. $cheque->banco->nombre . '</div>';
			echo '<div class="textoSize12 detalleFechaVto fLeft aLeft"> ' . $cheque->fechaVencimiento . '</div>';
			echo '<div class="textoSize12 detalleLibrador fLeft aLeft"> ' . $cheque->libradorNombre . '</div>';
			echo '<div class="textoSize12 detalleImporte fLeft aLeft"> ' . Funciones::formatearMoneda($cheque->importe) . '</div><br/>';
			$total += $cheque->importe;
		}
	?>
	<div id="total" class="absolute s22"><span class="bold">Total: </span><?php echo Funciones::formatearMoneda($total + $efectivo) ?></div>
</div>
</body>
</html>
