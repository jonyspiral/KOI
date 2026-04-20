<?php

$id = $_POST['form_id'];
$fecha = explode('/', $_POST['form_fecha']);
$beneficiario = $_POST['form_socio'];
$montoEfectivo = $_POST['form_monto_efectivo'][0]->importe;
$montoCheques = $_POST['form_monto_cheques'];
$montoTransferencias = $_POST['form_monto_transferencias'];
$montoTotal = $_POST['form_monto_total'];
$cheques = $_POST['form_cheques'];
$empresa = $_POST['form_empresa'];
$concepto = $_POST['form_concepto'];
$aplicaciones = $_POST['form_aplicaciones'];
$tipoOperacion = $_POST['form_tipo_operacion'];

?>
<head>
<link rel="stylesheet" type="text/css" href="../../../../css/styles.css" media="screen"/>
<style>
.textoSize1{
	font-size:1em;
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
	left: 220px;
	line-height:20px;
}
#retiroSocio{
	font-weight: bold;
	font-size: 1.5em;
	top: 75px;
	left: 510px;
}
#nroRetiroSocio{
	font-size: 1.5em;
	top: 105px;
	left: 510px;
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
#numerosEmpresa{
	font-size: 0.85em;
	top: 170px;
	right: 20px;
}
#division1{
	top: 228px;
	width: 100%;
	height: 2px;
	background-color: black;
}

#beneficiario{
	width: 900px;
	height: 42px;
	top: 245px;
	left: 50px;
}

#concepto{
	width: 900px;
	height: 42px;
	top: 270px;
	left: 50px;
}

#division1{
	top: 228px;
	width: 100%;
	height: 2px;
	background-color: black;
}

#tituloMediosDePago{
	top: 320px;
	left: 50px;
}

#tituloEfectivo{
	top: 345px;
	left: 60px;
}

#tituloCheques{
	top: 370px;
	left: 60px;
}

#tituloTransferencias{
	top: 395px;
	left: 60px;
}

#contenidoEfectivo{
	top: 345px;
	width: 100%;
	right: 700px;
}

#contenidoCheques{
	top: 370px;
	width: 100%;
	right: 700px;
}

#contenidoTransferencias{
	top: 395px;
	width: 100%;
	right: 700px;
}

#fondoCabeceraDetalle{
	top: 435px;
	width: 100%;
	height: 30px;
	background-color: black;
}

#cabeceraNumeroCheque{
	top: 440px;
	left: 30px;
}

#cabeceraBanco{
	top: 440px;
	left: 220px;
}

#cabeceraFechaVto{
	top: 440px;
	right: 590px;
}

#cabeceraLibrador{
	top: 440px;
	right: 370px;
}

#cabeceraLibradorCuit{
	top: 440px;
	right: 205px;
}

#cabeceraImporte{
	top: 440px;
	right: 30px;
}

#detalle{
	top: 470px;
	left: 25px;
}

#total{
	top: 1390px;
	right: 40px;
}

.detalleNumeroCheque{
	width: 100px;
}

.detalleBanco{
	width: 225px;
}

.detalleFechaVto{
	width: 105px;
}

.detalleLibrador{
	width: 300px;
}

.detalleLibradorCuit{
	width: 155px;
}

.detalleImporte{
	width: 100px;
}
</style>
</head>
<body>
	<div id="retiroSocio" class="absolute"><?php echo ($tipoOperacion == 'E' ? 'APORTE' : 'RETIRO') . ' DE SOCIO' ?></div>
	<?php
	if($empresa == '1'){
		echo
			'<div id="spiralSa" class="absolute textoSpiral">
			<?php echo Config::RAZON_RTG; ?>
			</div>

			<div id="logo" class="absolute">
			<img src="../../img/logos/logo_rtg.png" style="width: 190px; height: 190px">
			</div>

            <div id="infoEmpresa" class="absolute aLeft">
                Herrera 1761<br/>
                Ciudad Autónoma de Buenos Aires<br/>
                Tel./Fax: 0810-362-7747
            </div>

		<div id="nroRetiroSocio" class="absolute">Nº ' . Funciones::padLeft($id, 8, '0') . '</div>

		<div id="numerosEmpresa" class="absolute aRight">
			C.U.I.T Nº ' . Funciones::ponerGuionesAlCuit(Config::CUIT_RTG) . '<br/>
			ING. BRUTOS Nº ' . Funciones::ponerGuionesAlCuit(Config::CUIT_RTG) . '<br/>
			INICIO DE ACTIVIDADES 02/2018
		</div>';
	}else{
		echo '<div id="nroRetiroSocio" class="absolute">' . Funciones::padLeft($id, 8, '0') . '</div>';
	}
	?>

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

	<div id="beneficiario" class="absolute s18 aLeft">
		<span class="bold">Beneficiario: </span><?php echo (is_null($idProveedor) ? $beneficiario : '[' . $idProveedor . '] ' . $nombreProveedor); ?>
	</div>

	<div id="concepto" class="absolute s18 aLeft">
		<span class="bold">Concepto: </span><?php echo (is_null($concepto) ? '-' : $concepto); ?>
	</div>

	<div id="tituloMediosDePago" class="absolute bold underline s19">Medios de pago</div>
	<span id="tituloEfectivo" class="absolute bold s18 aLeft">Efvo.: </span><span id="contenidoEfectivo" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoEfectivo); ?></span>
	<span id="tituloCheques" class="absolute bold s18 aLeft">Cheques: </span><span id="contenidoCheques" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoCheques); ?></span>
	<span id="tituloTransferencias" class="absolute bold s18 aLeft">Transferencias: </span><span id="contenidoTransferencias" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoTransferencias); ?></span>

	<?php
	if(count($cheques) > 0){
		echo
		'<div id="fondoCabeceraDetalle" class="absolute"></div>

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

		<div id="cabeceraLibradorCuit" class="absolute white">
			CUIT
		</div>

		<div id="cabeceraImporte" class="absolute white aRight">
			IMPORTE
		</div>
		<div id="detalle" class="absolute">';

		foreach ($cheques as $cheque) {
			/** @var Cheque $cheque */
			echo '<div class="textoSize12 detalleNumeroCheque fLeft aLeft"> ' . $cheque->numero . '</div>';
			echo '<div class="textoSize12 detalleBanco fLeft aLeft"> '. $cheque->banco->nombre . '</div>';
			echo '<div class="textoSize12 detalleFechaVto fLeft aLeft"> ' . $cheque->fechaVencimiento . '</div>';
			echo '<div class="textoSize12 detalleLibrador fLeft aLeft"> ' . $cheque->libradorNombre . '</div>';
			echo '<div class="textoSize12 detalleLibradorCuit fLeft aLeft"> ' . $cheque->libradorCuit . '</div>';
			echo '<div class="textoSize12 detalleImporte fLeft aRight"> ' . Funciones::formatearMoneda($cheque->importe) . '</div><br/>';
		}
		echo '</div>';
	}
	?>
	<div id="total" class="absolute s22"><span class="bold">Total: </span><?php echo Funciones::formatearMoneda($montoTotal) ?></div>
</body>
</html>