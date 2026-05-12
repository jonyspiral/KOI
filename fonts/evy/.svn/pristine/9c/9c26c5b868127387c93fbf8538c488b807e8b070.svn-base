<?php

$id = $_POST['form_id'];
$fecha = explode('/', $_POST['form_fecha']);
$idCliente = $_POST['form_cliente_id'];
$recibidoDe = $_POST['form_recibido_de'];
$nombreCliente = $_POST['form_cliente_nombre'];
$montoEfectivo = $_POST['form_monto_efectivo'];
$montoCheques = $_POST['form_monto_cheques'];
$montoTransferencias = $_POST['form_monto_transferencias'];
$montoTotal = $_POST['form_monto_total'];
$cheques = $_POST['form_cheques'];
$retenciones = $_POST['form_retenciones'];
$empresa = $_POST['form_empresa'];
$aplicaciones = $_POST['form_aplicaciones'];
$observaciones = $_POST['form_observaciones'];

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
	left: -20px;
}
#infoEmpresa{
	font-size: 0.85em;
	top: 110px;
	left: 220px;
	line-height:20px;
}
#recibo{
	font-weight: bold;
	font-size: 1.5em;
	top: 75px;
	left: 510px;
}
#nroRecibo{
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

#tituloMediosDePago{
	top: 335px;
	right: 280px;
}

#nombre{
	width: 900px;
	height: 42px;
	top: 250px;
	left: 50px;	
}

#observaciones{
	width: 900px;
	height: 42px;
	top: 295px;
	left: 50px;
}

#division1{
	top: 228px;
	width: 100%;
	height: 2px;
	background-color: black;
}

#tituloEfectivo{
	top: 360px;
	left: 650px;
}

#tituloCheques{
	top: 385px;
	left: 650px;
}

#tituloTransferencias{
	top: 410px;
	left: 650px;
}

#contenidoEfectivo{
	top: 360px;
	width: 100%;
	right: 90px;
}

#contenidoCheques{
	top: 385px;
	width: 100%;
	right: 90px;
}

#contenidoTransferencias{
	top: 410px;
	width: 100%;
	right: 90px;
}

#tituloRetenciones{
	/*top: 410px;*/
	top: 425px;
	right: 305px;
}

#tituloObligacionesCanceladas{
	top: 335px;
	left: 20px;
}

#fondoCabeceraObligacionesCanceladas{
	top: 365px;
	width: 55%;
	height: 30px;
	background-color: black;
}

#cabeceraFecha{
	top: 370px;
	left: 40px;
}

#cabeceraDoc{
	top: 370px;
	left: 170px;
}

#cabeceraNro{
	top: 370px;
	left: 307px;
}

#cabeceraAplicado{
	top: 370px;
	left: 450px;
}

#detalleObligacionesCanceladas{
	top: 400px;
	left: 20px;
}

.detalleFecha{
	width: 148px;
}

.detalleDoc{
	width: 105px;
}

.detalleNro {
	width: 160px;
}

.detalleAplicado{
	width: 100px;
}

#fondoCabeceraDetalle{
	top: 735px;
	width: 100%;
	height: 30px;
	background-color: black;
}

#cabeceraNumeroCheque{
	top: 740px;
	left: 30px;
}

#cabeceraBanco{
	top: 740px;
	left: 220px;
}

#cabeceraFechaVto{
	top: 740px;
	right: 590px;
}

#cabeceraLibrador{
	top: 740px;
	right: 370px;
}

#cabeceraLibradorCuit{
	top: 740px;
	right: 205px;
}

#cabeceraImporte{
	top: 740px;
	right: 30px;
}

#detalle{
	top: 770px;
	left: 25px;
}

#total{
	top: 1380px;
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
	<?php
	if($empresa == '1'){
	echo
		'<div id="spiralSa" class="absolute textoSpiral">
		Spiral Shoes S.A
		</div>

		<div id="logo" class="absolute">
		<img src="../../img/logos/logo.png" style="width: 252.7px; height: 190px">
		</div>

		<div id="infoEmpresa" class="absolute aLeft">
			Chaco 2317(1822)<br/>
			Valentín Alsina<br/>
			Provincia de Buenos Aires<br/>
			Tel./Fax: 0810-362-SPIR (7747)
		</div>

		<div id="recibo" class="absolute">RECIBO' . ($idCliente ? ' DE COBRANZA' : '') .'</div>

		<div id="nroRecibo" class="absolute">Nº ' . Funciones::padLeft($id, 8, '0') . '</div>

		<div id="numerosEmpresa" class="absolute aRight">
			C.U.I.T Nº 33-71005145-9<br/>
			ING. BRUTOS Nº 33-71005145-9<br/>
			INICIO DE ACTIVIDADES 04/2007
		</div>';
	}else{
		echo '<div id="nroRecibo" class="absolute">' . Funciones::padLeft($id, 8, '0') . '</div>';
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

	<div id="nombre" class="absolute s18 aLeft">
		<span class="bold">Recibimos de: </span><?php echo (is_null($idCliente) ? $recibidoDe : '[' . $idCliente . '] ' . $nombreCliente) ?><span class="bold">, la cantidad de: </span><?php echo NumeroALetras::numero2Letras($montoTotal); ?>
	</div>

	<div id="observaciones" class="absolute s18 aLeft">
		<span class="bold">Obs: </span><?php echo (is_null($observaciones) ? '-' : $observaciones); ?>
	</div>

	<div id="tituloMediosDePago" class="absolute bold underline s19">Medios de pago</div>
	<span id="tituloEfectivo" class="absolute bold s18 aLeft">Efvo.: </span><span id="contenidoEfectivo" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoEfectivo); ?></span>
	<span id="tituloCheques" class="absolute bold s18 aLeft">Cheques: </span><span id="contenidoCheques" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoCheques); ?></span>
	<span id="tituloTransferencias" class="absolute bold s18 aLeft">Transferencias: </span><span id="contenidoTransferencias" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoTransferencias); ?></span>

	<div id="tituloObligacionesCanceladas" class="absolute bold underline s19">Documentos cancelados</div>

	<div id="fondoCabeceraObligacionesCanceladas" class="absolute"></div>

	<div id="cabeceraFecha" class="absolute white">
		FECHA
	</div>

	<div id="cabeceraDoc" class="absolute white">
		DOC.
	</div>

	<div id="cabeceraNro" class="absolute white">
		NRO.
	</div>

	<div id="cabeceraAplicado" class="absolute white">
		APLICADO
	</div>

	<div id="detalleObligacionesCanceladas" class="absolute">
		<?php
		foreach ($aplicaciones as $aplicacion) {
			/** @var DocumentoHija $aplicacion */
			echo '<div class="textoSize12 detalleFecha fLeft aLeft"> ' . $aplicacion->madre->fecha . '</div>';
			echo '<div class="textoSize12 detalleDoc fLeft aLeft"> '. $aplicacion->madre->tipoDocumento . '</div>';
			echo '<div class="textoSize12 detalleNro fLeft aLeft"> ' . Funciones::padLeft($aplicacion->madre->puntoDeVenta, 4, 0) . '-' . Funciones::padLeft($aplicacion->madre->numero, 8, 0) . '</div>';
			echo '<div class="textoSize12 detalleAplicado fLeft aRight"> ' . Funciones::formatearMoneda($aplicacion->importe) . '</div><br/>';
		}
		?>
	</div>

	<?php
	$top = 450;
	if(count($retenciones) > 0)
		echo '<div id="tituloRetenciones" class="absolute bold underline s19">Retenciones</div>';

	foreach ($retenciones as $retencion) {
		/** @var Retencion $retencion */
		echo '<span class="absolute bold s18 aLeft" style="top: ' . $top . 'px; left: 650px">' . $retencion->tipoRetencion->nombre . ': ' . '</span><span class="absolute s18 aRight" style="top: ' . $top . 'px; right: 90">' . Funciones::formatearMoneda($retencion->importe) . '</span>';
		$top += 30;
	}
	?>

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
