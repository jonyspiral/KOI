<?php

$id = $_POST['form_id'];
$fecha = $_POST['form_fecha'];
$idCliente = $_POST['form_cliente_id'];
$recibidoDe = $_POST['form_recibido_de'];
$nombreCliente = $_POST['form_cliente_nombre'];
$montoEfectivo = $_POST['form_monto_efectivo'];
$montoCheques = $_POST['form_monto_cheques'];
$montoTransferencias = $_POST['form_monto_transferencias'];
$montoTotal = $_POST['form_monto_total'];
$cheques = $_POST['form_cheques'];
$retenciones = $_POST['form_retenciones'];

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
	left: -20px;
}

#infoEmpresa{
	font-size: 0.85em;
	top: 95px;
	left: 220px;
	line-height:20px;
}

#recibo{
	font-weight: bold;
	font-size: 1.5em;
	top: 190px;
	left: 220px;
}

#division1{
	top: 228px;
	width: 100%;
	height: 2px;
	background-color: black;
}

#importes{
	top: 250px;
	left: 50px;	
}

#retenciones{
	top: 250px;
	right: 50px;
}

#tituloValores{
	top: 360px;
	font-weight: bold;
}

#fondoCabeceraDetalle{
	top: 395px;
	width: 100%;
	height: 30px;
	background-color: black;
}

#cabeceraNumeroCheque{
	top: 400px;
	left: 30px;
}

#cabeceraBanco{
	top: 400px;
	left: 220px;
}

#cabeceraFechaVto{
	top: 400px;
	right: 540px;
}

#cabeceraLibrador{
	top: 400px;
	right: 340px;
}

#cabeceraLibradorCuit{
	top: 400px;
	right: 180px;
}

#cabeceraImporte{
	top: 400px;
	right: 30px;
}

#detalle{
	top: 435px;
	left: 35px;
}

.detalleNumeroCheque{
	width: 100px;
}

.detalleBanco{
	width: 255px;
}

.detalleFechaVto{
	width: 115px;
}

.detalleLibrador{
	width: 265px;
}

.detalleLibradorCuit{
	width: 135px;
}

.detalleImporte{
	width: 100px;
}
</style>
</head>
<body>
	<div id="spiralSa" class="absolute textoSpiral">
		Spiral Shoes S.A.
	</div>

	<div id="logo" class="absolute">
		<img src="../../img/logos/logo.png" style="width: 252.7px; height: 190px">
	</div>

	<div id="infoEmpresa" class="absolute aLeft">
		Chaco 2317(1822)<br/>
		valentín Alsina<br/>
		Provincia de Buenos Aires<br/>
		Tel./Fax: 0810-362-SPIR (7747)
	</div>

	<div id="division1" class="absolute">
	</div>

	<div id="recibo" class="absolute">
		<?php echo 'RECIBO' . ($idCliente ? ' DE COBRANZA' : '') . ' - Nº ' . Funciones::padLeft($id, 8, '0'); ?>
	</div>

	<div id="importes" class="absolute aLeft s18">
		<span class="bold">Efvo.: </span><?php echo Funciones::formatearMoneda($montoEfectivo); ?><br/>
		<span class="bold">Cheques: </span><?php echo Funciones::formatearMoneda($montoCheques); ?><br/>
		<span class="bold">Transferencias: </span><?php echo Funciones::formatearMoneda($montoTransferencias); ?><br/>
		<span class="bold">Total: </span><?php echo Funciones::formatearMoneda($montoTotal); ?>
	</div>

	<div id="retenciones" class="absolute aLeft s18">
		<?php
			foreach ($retenciones as $retencion) {
				/** @var Retencion $retencion */
				echo '<span class="bold">' . $retencion->tipoRetencion->nombre . ': ' . '</span>' . Funciones::formatearMoneda($retencion->importe) . '<br/>';
			}
		?>
		</div>

	<?php
		if(count($cheques) > 0){
			echo
				'<div id="tituloValores" class="absolute s17">Detalle valores</div>
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

				<div id="cabeceraLibradorCuit" class="absolute white">
					CUIT
				</div>

				<div id="cabeceraImporte" class="absolute white">
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
				echo '<div class="textoSize12 detalleImporte fLeft aLeft"> ' . Funciones::formatearMoneda($cheque->importe) . '</div><br/>';
			}
			echo '</div>';
		}
	?>
</body>
</html>
