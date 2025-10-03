<?php

$id = $_POST['form_id'];
$fecha = explode('/', $_POST['form_fecha']);
$idProveedor = $_POST['form_proveedor_id'];
$beneficiario = $_POST['form_beneficiario'];
$nombreProveedor = $_POST['form_proveedor_nombre'];
$montoEfectivo = $_POST['form_monto_efectivo'][0]->importe;
$montoCheques = $_POST['form_monto_cheques'];
$montoTransferencias = $_POST['form_monto_transferencias'];
$montoTotal = $_POST['form_monto_total'];
$montoSujetoRetenciones = $_POST['form_monto_sujeto_ret'];
//$cheques = Funciones::arraySort($_POST['form_cheques'], 'fechaVencimiento', 'Funciones::esFechaMayor');
$cheques = $_POST['form_cheques'];
$transferencias = $_POST['form_transferencias'];
$retenciones = $_POST['form_retenciones'];
$empresa = $_POST['form_empresa'];
$concepto = $_POST['form_concepto'];
$aplicaciones = $_POST['form_aplicaciones'];

?>
<head>
<link rel="stylesheet" type="text/css" href="../../../../css/styles.css" media="screen"/>
<style>
	.textoSize1 {
		font-size: 1em;
	}

	.fuenteArial {
		font-family: Arial, serif;
	}

	#spiralSa {
		top: 40px;
		left: 220px;
	}

	.textoSpiral {
		font-size: 1.5em;
		font-weight: bold;
	}

	#logo {
		top: 30px;
		left: 20px;
	}

	#infoEmpresa {
		font-size: 0.85em;
		top: 110px;
		left: 220px;
		line-height: 20px;
	}

	#ordenDePago {
		font-weight: bold;
		font-size: 1.5em;
		top: 75px;
		left: 510px;
	}

	#nroOrdenDePago {
		font-size: 1.5em;
		top: 105px;
		left: 510px;
	}

	#fecha {
		font-weight: bold;
		font-size: 1.5em;
		top: 75px;
		right: 78px;
	}

	#dia {
		font-size: 1.5em;
		top: 105px;
		right: 120px;
	}

	#mes {
		font-size: 1.5em;
		top: 105px;
		right: 85px;
	}

	#anio {
		font-size: 1.5em;
		top: 105px;
		right: 20px;
	}

	#numerosEmpresa {
		font-size: 0.85em;
		top: 170px;
		right: 20px;
	}

	#division1 {
		top: 228px;
		width: 100%;
		height: 2px;
		background-color: black;
	}

	#beneficiario {
		width: 900px;
		height: 42px;
		top: 245px;
		left: 50px;
	}

	#concepto {
		width: 900px;
		height: 42px;
		top: 270px;
		left: 50px;
	}

	#division1 {
		top: 228px;
		width: 100%;
		height: 2px;
		background-color: black;
	}

	#tituloMediosDePago {
		top: 300px;
		right: 280px;
	}

	#tituloEfectivo {
		top: 325px;
		left: 650px;
	}

	#tituloCheques {
		top: 350px;
		left: 650px;
	}

	#tituloTransferencias {
		top: 375px;
		left: 650px;
	}

	#contenidoEfectivo {
		top: 325px;
		width: 100%;
		right: 90px;
	}

	#contenidoCheques {
		top: 350px;
		width: 100%;
		right: 90px;
	}

	#contenidoTransferencias {
		top: 375px;
		width: 100%;
		right: 90px;
	}

	#tituloRetenciones {
		top: 410px;
		right: 305px;
	}

	#tituloObligacionesCanceladas {
		top: 300px;
		left: 20px;
	}

	#fondoCabeceraObligacionesCanceladas {
		top: 330px;
		height: 30px;
	}

	#detalle {
		top: 795px;
		left: 25px;
	}

	#total {
		right: 40px;
	}

	.tableHead {
		background-color: black;
		color: white;
	}
</style>
</head>
<body>
	<?php if ($empresa == '1') { ?>
		<div id="spiralSa" class="absolute textoSpiral">
            <?php echo Config::RAZON_NCNTS; ?>
			</div>

			<div id="logo" class="absolute">
            <img src="../../img/logos/logo_ncnts_new.png" style="width: 190px; height: 190px">
			</div>

            <div id="infoEmpresa" class="absolute aLeft">
                Herrera 1761<br/>
                Ciudad Autónoma de Buenos Aires<br/>
                Tel./Fax: 1142186382
            </div>

			<div id="ordenDePago" class="absolute">ORDEN DE PAGO</div>

		<div id="nroOrdenDePago" class="absolute">Nº <?php echo Funciones::padLeft($id, 8, '0'); ?></div>

		<div id="numerosEmpresa" class="absolute aRight">
			C.U.I.T Nº <?php echo Funciones::ponerGuionesAlCuit(Config::CUIT_NCNTS); ?><br/>
			ING. BRUTOS Nº <?php echo Funciones::ponerGuionesAlCuit(Config::CUIT_NCNTS); ?><br/>
			INICIO DE ACTIVIDADES 09/2018
		</div>
	<? } else { ?>
		<div id="nroOrdenDePago" class="absolute"><?php echo Funciones::padLeft($id, 8, '0'); ?></div>
	<? } ?>

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

	<div id="beneficiario" class="absolute s18 aLeft">
		<span class="bold">Beneficiario: </span><?php echo(is_null($idProveedor) ? $beneficiario : '[' . $idProveedor . '] ' . $nombreProveedor); ?>
	</div>

	<div id="concepto" class="absolute s18 aLeft">
		<span class="bold">Concepto: </span><?php echo(is_null($concepto) ? '-' : $concepto); ?>
	</div>

	<div id="tituloMediosDePago" class="absolute bold underline s19">Medios de pago</div>
	<span id="tituloEfectivo" class="absolute bold s18 aLeft">Efvo.: </span><span id="contenidoEfectivo" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoEfectivo); ?></span>
	<span id="tituloCheques" class="absolute bold s18 aLeft">Cheques: </span><span id="contenidoCheques" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoCheques); ?></span>
	<span id="tituloTransferencias" class="absolute bold s18 aLeft">Transferencias: </span><span id="contenidoTransferencias" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($montoTransferencias); ?></span>

	<?php
	$html .= '<div id="tituloObligacionesCanceladas" class="absolute bold underline s19">Obligaciones canceladas</div>
	<div id="fondoCabeceraObligacionesCanceladas" class="detalle absolute">
		<table style="width: 55%;">
			<thead class="tableHead">
				<tr>
					<th>FECHA</th>
					<th>DOC.</th>
					<th>NRO.</th>
					<th>APLICADO</th>
				</tr>
		</thead>
		<tbody>';

	foreach ($aplicaciones as $aplicacion) {
		/** @var DocumentoHija $aplicacion */
		$html .= '<tr>';
		$html .= '<td class="s14 aCenter">' . $aplicacion->madre->fecha . '</td>';
		$html .= '<td class="s14 aCenter">' . $aplicacion->madre->tipoDocumento . '</td>';
		$html .= '<td class="s14 aCenter">' . Funciones::padLeft($aplicacion->madre->puntoDeVenta, 4, 0) . '-' . Funciones::padLeft($aplicacion->madre->nroDocumento, 8, 0) . '</td>';
		$html .= '<td class="s14">' . Funciones::formatearMoneda($aplicacion->importe) . '</td>';
		$html .= '</tr>';
	}

	$html .= '</tbody></table></div>';
	$top2 = 350 + count($aplicaciones) * 23;

	$top = 435;
	if (count($retenciones) > 0) {
		echo '<div id="tituloRetenciones" class="absolute bold underline s19">Retenciones</div>';
	}

	foreach ($retenciones as $retencion) {
		/** @var RetencionEfectuada $retencion */
		echo '<span class="absolute bold s18 aLeft" style="top: ' . $top . 'px; left: 650px">' . $retencion->tipoRetencion->nombre . ': ' . '</span><span class="absolute s18 aRight" style="top: ' . $top . 'px; right: 90">(' . Funciones::formatearMoneda($montoSujetoRetenciones) . ') ' . Funciones::formatearMoneda($retencion->importe) . '</span>';
		$top += 30;
	}

	if (count($transferencias) > 0) {
		$html .= '<table id="tablaCheques" class="detalle absolute" style="top: ' . ($top2 > $top ? $top2 : $top) . 'px; width: 100%">
				<thead class="tableHead">
					<tr>
						<th>FECHA</th>
						<th>CUENTA BANCARIA</th>
						<th>NÚMERO</th>
						<th>IMPORTE</th>
					</tr>
				</thead>
				<tbody>';

		foreach ($transferencias as $transferencia) {
			/** @var TransferenciaBancariaImporte $transferencia */
			$fechaTransferencia = $transferencia->transferenciaBancariaOperacion->fechaTransferencia;
			$fechaTransferencia = ($fechaTransferencia ? $fechaTransferencia : $transferencia->transferenciaBancariaOperacion->fechaAlta);
			$html .= '<tr>';
			$html .= '<td class="s14 aCenter">' . $fechaTransferencia . '</td>';
			$html .= '<td class="s14">' . $transferencia->cuentaBancaria->nombre . '</td>';
			$html .= '<td class="s14 aCenter">' . $transferencia->transferenciaBancariaOperacion->numeroTransferencia . '</td>';
			$html .= '<td class="s14">' . Funciones::formatearMoneda($transferencia->transferenciaBancariaOperacion->importeTotal) . '</td>';
			$html .= '</tr>';
		}

		$html .= '</tbody></table></div>';

		if($top2 > $top) {
			$top2 += 30 + count($transferencias) * 23;
		} else {
			$top += 30 + count($transferencias) * 23;
		}
	}

	if (count($cheques) > 0) {
		$html .= '<table id="tablaCheques" class="detalle absolute" style="top: ' . ($top2 > $top ? $top2 : $top) . 'px; width: 100%">
				<thead class="tableHead">
					<tr>
						<th>Nº CHEQUE</th>
						<th>BANCO</th>
						<th>FECHA VTO.</th>
						<th>LIBRADOR</th>
						<th>CUIT</th>
						<th>IMPORTE</th>
					</tr>
				</thead>
				<tbody>';

		foreach ($cheques as $cheque) {
			/** @var Cheque $cheque */
			$html .= '<tr>';
			$html .= '<td class="s14 aCenter">' . $cheque->numero . '</td>';
			$html .= '<td class="s14">' . $cheque->banco->nombre . '</td>';
			$html .= '<td class="s14 aCenter">' . $cheque->fechaVencimiento . '</td>';
			$html .= '<td class="s14">' . $cheque->libradorNombre . '</td>';
			$html .= '<td class="s14 aCenter">' . $cheque->libradorCuit . '</td>';
			$html .= '<td class="s14">' . Funciones::formatearMoneda($cheque->importe) . '</td>';
			$html .= '</tr>';
		}
	}

	$total = 1390;
	if($total > $top && $total > $top2){
		$top = $total;
	} else {
		if($top2 > $top) {
			$top2 += 30 + count($cheques) * 23;
			$top = $top2;
		} else {
			$top += 30 + count($cheques) * 23;
		}
	}

	$html .= '</tbody></table>';
	$html .= '<div id="total" class="absolute s22" style="top: ' . ($top2 > $top ? $top2 : $top) . 'px;>
				<span class="bold">Total: </span>' . Funciones::formatearMoneda($montoTotal) .
			  '</div>';
	echo $html;
	?>
</body>
</html>