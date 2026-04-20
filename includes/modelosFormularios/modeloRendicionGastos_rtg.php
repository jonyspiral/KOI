<?php

$id = $_POST['form_id'];
$empresa = $_POST['form_empresa'];
$importeTotal = $_POST['form_importe_total'];
$fecha = explode('/', $_POST['form_fecha']);
$observaciones = $_POST['form_observaciones'];
$aplicaciones = $_POST['form_aplicaciones'];
$gastitos = $_POST['form_gastitos'];

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
#ordenDePago{
	font-weight: bold;
	font-size: 1.5em;
	top: 75px;
	left: 510px;
}
#nroOrdenDePago{
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

#observaciones{
	width: 900px;
	height: 42px;
	top: 245px;
	left: 50px;
}

#division1{
	top: 228px;
	width: 100%;
	height: 2px;
	background-color: black;
}

#tituloMediosDePago{
	top: 300px;
	right: 280px;
}

#tituloEfectivo{
	top: 325px;
	left: 650px;
}

#contenidoEfectivo{
	top: 325px;
	width: 100%;
	right: 90px;
}

#tituloObligacionesCanceladas{
	top: 300px;
	left: 20px;
}

#fondoCabeceraObligacionesCanceladas{
	top: 330px;
	width: 55%;
	height: 30px;
	background-color: black;
}

#cabeceraFecha{
	top: 335px;
	left: 40px;
}

#cabeceraDoc{
	top: 335px;
	left: 170px;
}

#cabeceraNro{
	top: 335px;
	left: 307px;
}

#cabeceraAplicado{
	top: 335px;
	left: 450px;
}

#detalleObligacionesCanceladas{
	top: 365px;
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
	/*top: 735px;*/
	top: 850px;
	width: 100%;
	height: 30px;
	background-color: black;
}

#cabeceraFechaGasto{
	top: 855px;
	left: 40px;
}

#cabeceraPersona{
	top: 855px;
	left: 140px;
}

#cabeceraObservaciones{
	top: 855px;
	right: 400px;
}

#cabeceraImporte{
	top: 855px;
	right: 30px;
}

#detalle{
	top: 885px;
	left: 25px;
}

.detalleFechaGasto{
	width: 100px;
}

.detallePersona{
	width: 170px;
}

.detalleObservaciones{
	width: 600px;
}

.detalleImporte{
	width: 100px;
}

#total{
	top: 245px;
	right: 40px;
}
</style>
</head>
<body>
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

			<div id="ordenDePago" class="absolute">RENDICIÓN DE GASTOS</div>

		<div id="nroOrdenDePago" class="absolute">Nº ' . Funciones::padLeft($id, 8, '0') . '</div>

		<div id="numerosEmpresa" class="absolute aRight">
			C.U.I.T Nº ' . Funciones::ponerGuionesAlCuit(Config::CUIT_RTG) . '<br/>
			ING. BRUTOS Nº ' . Funciones::ponerGuionesAlCuit(Config::CUIT_RTG) . '<br/>
			INICIO DE ACTIVIDADES 02/2018
		</div>';
	}else{
		echo '<div id="nroOrdenDePago" class="absolute">' . Funciones::padLeft($id, 8, '0') . '</div>';
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

	<div id="observaciones" class="absolute s18 aLeft">
		<span class="bold">Observaciones: </span><?php echo (is_null($observaciones) ? '-' : $observaciones); ?>
	</div>

	<div id="tituloMediosDePago" class="absolute bold underline s19">Medios de pago</div>
	<span id="tituloEfectivo" class="absolute bold s18 aLeft">Efvo.: </span><span id="contenidoEfectivo" class="absolute s18 aRight"><?php echo Funciones::formatearMoneda($importeTotal); ?></span>

	<div id="tituloObligacionesCanceladas" class="absolute bold underline s19">Obligaciones canceladas</div>

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
			echo '<div class="textoSize12 detalleNro fLeft aLeft"> ' . Funciones::padLeft((empty($aplicacion->madre->puntoDeVenta) ? '' : 0), 4, 0) . '-' . Funciones::padLeft($aplicacion->madre->nroDocumento, 8, 0) . '</div>';
			echo '<div class="textoSize12 detalleAplicado fLeft aRight"> ' . Funciones::formatearMoneda($aplicacion->importe) . '</div><br/>';
		}
	?>
	</div>

	<?php
	if(count($gastitos) > 0){
		echo
		'<div id="fondoCabeceraDetalle" class="absolute"></div>

		<div id="cabeceraFechaGasto" class="absolute white">
			FECHA
		</div>

		<div id="cabeceraPersona" class="absolute white">
			PERSONA GASTO
		</div>

		<div id="cabeceraObservaciones" class="absolute white">
			OBSERVACIONES
		</div>

		<div id="cabeceraImporte" class="absolute white aRight">
			IMPORTE
		</div>

		<div id="detalle" class="absolute">';

		foreach ($gastitos as $gastito) {
			/** @var Gastito $gastito */
			echo '<div class="textoSize12 detalleFechaGasto fLeft aLeft"> ' . $gastito->fecha . '</div>';
			echo '<div class="textoSize12 detallePersona fLeft aLeft"> '. $gastito->personaGasto->nombre . '</div>';
			echo '<div class="textoSize12 detalleObservaciones fLeft aLeft"> ' . $gastito->observaciones . '</div>';
			echo '<div class="textoSize12 detalleImporte fLeft aRight"> ' . Funciones::formatearMoneda($gastito->importe) . '</div><br/>';
		}
		echo '</div>';
	}
	?>

	<div id="total" class="absolute s22"><span class="bold">Total: </span><?php echo Funciones::formatearMoneda($importeTotal) ?></div>
</body>
</html>